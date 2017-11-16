\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Table: adverts_tbl {{{
 * Объявления
 */
drop table if exists adverts_tbl;

-- sequence: seq_adv_id_pk {{{
drop sequence if exists seq_adv_id_pk;
create sequence seq_adv_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_adv_id_pk owner to :admuser;
grant all on seq_adv_id_pk to :mainuser;
-- }}}

create table adverts_tbl ( --{{{
  adv_id_pk       dm_pk default nextval('seq_adv_id_pk'::regclass),
  adv_lng_id_fk   dm_ref,
  adv_usr_id_fk   dm_ref,
  adv_type        dm_microenum_nn,  --sell|buy
  adv_title       dm_text_large_nn,
  adv_date_create dm_datetime_now,
  adv_text        dm_text_nn,
  adv_image       dm_image,
  adv_robots      dm_text_small;
  adv_category    dm_microenum; --car|autopart
  adv_cost        dm_cost;
  adv_rat_id_fk   dm_ref;
  adv_name        dm_text_large;
  adv_user_name   dm_text_medium;
  adv_user_city   dm_text_medium;
  adv_user_email  dm_text_medium;
  adv_user_phone  dm_text_medium;
  adv_description dm_text_xxlarge;
  adv_keywords    dm_text_xxlarge;
  adv_enabled     dm_bool_true,

  constraint PK_ADVERTS_TBL primary key (adv_id_pk)
) tablespace :tablespace;
-- }}}

update adverts_tbl set adv_name = adv_title;
update adverts_tbl set adv_category = 'autopart';
update adverts_tbl set adv_rat_id_fk = 2;

create index adverts_tbl_adv_date_create_idx
  on adverts_tbl (adv_date_create desc nulls last)
  tablespace :tablespace;

create index adverts_tbl_adv_rat_id_fk_idx
  on adverts_tbl (adv_rat_id_fk)
  tablespace :tablespace;

alter table adverts_tbl owner to :admuser;
grant select, insert, update on adverts_tbl to :mainuser;
/* }}} Table: adverts_tbl */


/* Table: adverts_attachments_tbl {{{
 * Приложения к объявлениям
 */
drop table if exists adverts_attachments_tbl;

-- sequence: seq_ada_id_pk {{{
drop sequence if exists seq_ada_id_pk;
create sequence seq_ada_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_ada_id_pk owner to :admuser;
grant all on seq_ada_id_pk to :mainuser;
-- }}}

create table adverts_attachments_tbl ( --{{{
  ada_id_pk       dm_pk default nextval('seq_ada_id_pk'::regclass),
  ada_adv_id_fk   dm_ref_nn,
  ada_image       dm_image,
  ada_enabled     dm_bool_true,

  constraint PK_ADVERTS_ATTACHMENTS_TBL primary key (ada_id_pk)
) tablespace :tablespace;
-- }}}

alter table adverts_attachments_tbl owner to :admuser;
grant select, insert, update on adverts_attachments_tbl to :mainuser;
/* }}} Table: adverts_attachments_tbl */


set client_min_messages = 'notice';
\echo 'Tables by module Adverts created.'
