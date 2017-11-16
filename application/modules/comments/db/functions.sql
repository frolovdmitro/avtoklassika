\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Function: lock_comments {{{
 */
drop function if exists lock_comments(integer) cascade;

create or replace function lock_comments(integer)
  returns boolean as
$BODY$
declare
  subject_id alias for $1;
  _id integer;
begin
  select cmt_id_pk into _id
  from comments_tbl
  where cmt_subject_id_fk = subject_id for update;

  return true;
end;
$BODY$
  language 'plpgsql' volatile cost 100;
alter function lock_comments(integer) owner to :admuser;
/* }}} Function: lock_comments */


/* Function: cmt_visible_childs {{{
 * Скрывать ответы на коммент, если скрыт родительский коммент
 */
drop function if exists cmt_visible_childs() cascade;

create or replace function cmt_visible_childs()
  returns trigger AS
$BODY$
begin
  if (old.cmt_visible != new.cmt_visible) then
    update comments_tbl set cmt_visible=new.cmt_visible
      where cmt_left_id_fk > new.cmt_left_id_fk
        and cmt_right_id_fk < new.cmt_right_id_fk
        and cmt_subject_id_fk = new.cmt_subject_id_fk;
  end if;

  return new;
end
$BODY$
  language 'plpgsql' VOLATILE;

drop trigger if exists cmt_visible_childs_tg on comments_tbl;
create trigger cmt_visible_childs_tg
  after update
ON comments_tbl
for each row
  execute procedure cmt_visible_childs();
/* }}} Function: cmt_visible_childs */


/* Function: comments_before_insert_func {{{
 * Добавление комментария
 */
drop function if exists comments_before_insert_func(param int) cascade;

create or replace function comments_before_insert_func()
  returns trigger as
$BODY$
declare
  _left_id       integer;
  _level         integer;
  _tmp_left_id   integer;
  _tmp_right_id  integer;
  _tmp_level     integer;
  _tmp_id        integer;
  _tmp_parent_id integer;
begin
  perform lock_comments(new.cmt_subject_id_fk);
  -- нельзя эти поля ручками ставить:
  new._trigger_for_delete := false;
  new._trigger_lock_update := false;
  _left_id := 0;
  _level := 0;

  -- если мы указали родителя:
  if new.cmt_parent_id_fk is not null and new.cmt_parent_id_fk > 0 then
    select cmt_right_id_fk, "cmt_level" + 1 into _left_id, _level
    from comments_tbl
    where cmt_id_pk = new.cmt_parent_id_fk and
      cmt_subject_id_fk = new.cmt_subject_id_fk;
  end if;

  -- если мы указали левый ключ:
  if new.cmt_left_id_fk is not null and new.cmt_left_id_fk > 0 and
    (_left_id is null or _left_id = 0) then

    select cmt_id_pk, cmt_left_id_fk, cmt_right_id_fk, cmt_level,
      cmt_parent_id_fk into _tmp_id, _tmp_left_id, _tmp_right_id, _tmp_level,
      _tmp_parent_id
    from comments_tbl
    where cmt_subject_id_fk = new.cmt_subject_id_fk
      and (cmt_left_id_fk = new.cmt_left_id_fk
        or cmt_right_id_fk = new.cmt_left_id_fk);

    if _tmp_left_id is not null and _tmp_left_id > 0
      and new.cmt_left_id_fk = _tmp_left_id then

      new.cmt_parent_id_fk := _tmp_parent_id;
      _left_id := new.cmt_left_id_fk;
      _level := _tmp_level;
    elsif _tmp_left_id is not null and _tmp_left_id > 0
      and new.cmt_left_id_fk = _tmp_right_id then

      new.cmt_parent_id_fk := _tmp_id;
      _left_id := new.cmt_left_id_fk;
      _level := _tmp_level + 1;
    end if;
  end if;

  -- если родитель или левый ключ не указан, или мы ничего не нашли:
  if _left_id is null or _left_id = 0 then
    select max(cmt_right_id_fk) + 1 into _left_id
    from comments_tbl
    where cmt_subject_id_fk = new.cmt_subject_id_fk;
    if _left_id is null or _left_id = 0 then
      _left_id := 1;
    end if;
    _level := 0;
    new.cmt_parent_id_fk := 0;
  end if;

  -- устанавливаем полученные ключи для узла:
  new.cmt_left_id_fk := _left_id;
  new.cmt_right_id_fk := _left_id + 1;
  new."cmt_level" := _level;

-- формируем развыв в дереве на месте вставки:
  update comments_tbl set cmt_left_id_fk = cmt_left_id_fk +
    case when cmt_left_id_fk >= _left_id then 2 else 0 end,
    cmt_right_id_fk = cmt_right_id_fk + 2, _trigger_lock_update = true
  where cmt_subject_id_fk = new.cmt_subject_id_fk
    and cmt_right_id_fk >= _left_id;

  return new;
end;
$BODY$
  language 'plpgsql' volatile cost 100;

alter function comments_before_insert_func() owner to :admuser;

create trigger comments_before_insert_tr
  before insert on comments_tbl
for each row
  execute procedure comments_before_insert_func();
/* }}} Function: comments_before_insert_func */


/* Function: comments_after_delete_func {{{
 * Удаление комментария
 */
drop function if exists comments_after_delete_fn() cascade;

create or replace function comments_after_delete_func()
  returns trigger as
$BODY$
declare
  _skew_tree integer;
begin
  perform lock_comments(old.cmt_subject_id_fk);

  -- проверяем, стоит ли выполнять триггер:
  if old._trigger_for_delete = true then
    return old; end if;

  -- помечаем на удаление дочерние узлы:
  update comments_tbl set _trigger_for_delete = true, _trigger_lock_update = true
  where cmt_subject_id_fk = old.cmt_subject_id_fk and
    cmt_left_id_fk > old.cmt_left_id_fk and cmt_right_id_fk < old.cmt_right_id_fk;

  -- удаляем помеченные узлы:
  delete from comments_tbl
  where cmt_subject_id_fk = old.cmt_subject_id_fk
    and cmt_left_id_fk > old.cmt_left_id_fk
    and cmt_right_id_fk < old.cmt_right_id_fk;

  -- убираем разрыв в ключах:
  _skew_tree := old.cmt_right_id_fk - old.cmt_left_id_fk + 1;

  update comments_tbl set cmt_left_id_fk =
    case
      when cmt_left_id_fk > old.cmt_left_id_fk then cmt_left_id_fk - _skew_tree
      else cmt_left_id_fk
    end, cmt_right_id_fk = cmt_right_id_fk - _skew_tree,
    _trigger_lock_update = true
  where cmt_right_id_fk > old.cmt_right_id_fk
    and cmt_subject_id_fk = old.cmt_subject_id_fk;

  return old;
end;
$BODY$
  language 'plpgsql' volatile cost 100;

alter function comments_after_delete_func() owner to :admuser;

drop trigger if exists comments_after_delete_tr on comments_tbl;
create trigger comments_after_delete_tr
  after delete on comments_tbl
for each row
  execute procedure comments_after_delete_func();
/* }}} Function: comments_after_delete_func */


set client_min_messages = 'notice';
\echo 'Functions by module Comments created.'
