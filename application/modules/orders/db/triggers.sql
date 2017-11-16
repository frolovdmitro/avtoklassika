\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Trigger: apt_set_last_order_count_childs_trg {{{
 *
 */
create or replace function apt_set_last_order_count_childs()
  returns trigger AS
$BODY$
declare
  _last_order integer;
  _count_childs integer;
begin
  IF (TG_OP = 'INSERT') then
    select coalesce(max(apt_order)+1, 1) into _last_order from autoparts_tbl
      where apt_parent_id_fk=new.apt_parent_id_fk;

    select coalesce(count(apt_id_pk), 0)+1 into _count_childs from autoparts_tbl
      where apt_parent_id_fk=new.apt_parent_id_fk;

    update autoparts_tbl set apt_childs=_count_childs
      where apt_id_pk=new.apt_parent_id_fk;

    new.apt_order := _last_order;
  elseif (TG_OP = 'DELETE') then
    select coalesce(count(apt_id_pk), 0) into _count_childs from autoparts_tbl
      where apt_parent_id_fk=old.apt_parent_id_fk;

    update autoparts_tbl set apt_childs=_count_childs
      where apt_id_pk=old.apt_parent_id_fk;
  end if;
    return new;
end
$BODY$
  language 'plpgsql' VOLATILE;

-- trigger: apt_set_last_order_count_childs {{{
drop trigger if exists apt_set_last_order_count_childs on autoparts_tbl;
create trigger apt_set_last_order_count_childs
  before insert
ON autoparts_tbl
for each row
  execute procedure apt_set_last_order_count_childs();
-- }}}

-- trigger: apt_set_last_order_count_childs1 {{{
drop trigger if exists apt_set_last_order_count_childs1 on autoparts_tbl;
create trigger apt_set_last_order_count_childs1
  after delete
ON autoparts_tbl
for each row
  execute procedure apt_set_last_order_count_childs();
-- }}}
/* }}} Trigger: apt_set_last_order_count_childs_trg */


/* Trigger: set_cost_order_detail_trg {{{
 * Тригер устанавливает цену детали при ее добавлении в заказ
 */
drop function if exists set_cost_order_detail_trg_fn() cascade;
create or replace function set_cost_order_detail_trg_fn()
  returns trigger as
$BODY$
declare
  _id integer;
begin
  new.odd_cost = (select cost_unformat from
    color_size_cost_count_fn(new.odd_dpt_id_fk, new.odd_dac_id_fk,
      new.odd_das_id_fk));

  return new;
end
$BODY$
  language 'plpgsql' VOLATILE;

-- trigger: set_cost_order_detail_trg {{{
drop trigger if exists set_cost_order_detail_trg on orders_details_tbl;
create trigger set_cost_order_detail_trg
  before insert
on orders_details_tbl
for each row
  execute procedure set_cost_order_detail_trg_fn();
-- }}}
/* }}} Trigger: set_cost_order_detail_trg */


/* Trigger: set_order_sum_trg {{{
 * Тригер устанавливает сумму заказа
 */
drop function if exists set_order_sum_trg_fn() cascade;
create or replace function set_order_sum_trg_fn()
  returns trigger as
$BODY$
declare
  _id integer;
  _sum float;
begin
  select sum(odd_cost*odd_count) into _sum from orders_details_tbl
    where odd_ord_id_fk = new.odd_ord_id_fk;
  update orders_tbl set ord_sum = _sum where ord_id_pk = new.odd_ord_id_fk;

  return new;
end
$BODY$
  language 'plpgsql' VOLATILE;

-- trigger: set_order_sum_trg {{{
drop trigger if exists set_order_sum_trg on orders_details_tbl;
create trigger set_order_sum_trg
  after insert or delete
on orders_details_tbl
for each row
  execute procedure set_order_sum_trg_fn();
-- }}}
/* }}} Trigger: set_order_sum_trg */


set client_min_messages = 'notice';
\echo 'Triggers by module Orders created.'
