\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Table: comments_tbl {{{
 * Комментарии
 */
drop table if exists comments_tbl;

-- sequence: seq_cmt_id_pk {{{
drop sequence if exists seq_cmt_id_pk;
create sequence seq_cmt_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_cmt_id_pk owner to :admuser;
grant all on seq_cmt_id_pk to :mainuser;
-- }}}

-- sequence: seq_cmt_id_pk {{{
drop sequence if exists seq_cmt_order;
create sequence seq_cmt_order
  start with 1
  increment by 250
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_cmt_order owner to :admuser;
grant all on seq_cmt_order to :mainuser;
-- }}}

create table comments_tbl ( --{{{
  cmt_id_pk         dm_pk default nextval('seq_cmt_id_pk'::regclass),
  cmt_usr_id_fk     dm_int,
  cmt_anonymous     dm_text_medium,
  cmt_subject_id_fk dm_int,
  cmt_parent_id_fk  dm_int default 0,
  cmt_datetime      dm_datetime_now,
  cmt_name          dm_text_medium,
  cmt_text          dm_text,
  cmt_type          dm_enum,--news|adverts|detail
  cmt_left_id_fk    dm_int,
  cmt_right_id_fk   dm_int,
  cmt_level         dm_int default 0,
  cmt_visible       dm_bool_true,
  _trigger_lock_update  dm_bool_false,
  _trigger_for_delete   dm_bool_false,
  cmt_enabled       dm_bool_true,

  constraint PK_COMMENTS_TBL primary key (cmt_id_pk)
) tablespace :tablespace;
-- }}}

alter table comments_tbl add column cmt_email dm_email;

alter table comments_tbl owner to :admuser;
grant all on comments_tbl to :admuser;
grant all on comments_tbl to :mainuser;
grant select, insert on comments_tbl to :mainuser;
/* }}} Table: comments_tbl */


set client_min_messages = 'notice';
\echo 'Tables by module Comments created.'
