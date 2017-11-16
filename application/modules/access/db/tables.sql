\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Table: administrators_tbl {{{
 * Администраторы панели управления
 */
drop table if exists administrators_tbl cascade;

-- sequence: seq_adm_id_pk {{{
drop sequence if exists seq_adm_id_pk;
create sequence seq_adm_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_adm_id_pk owner to :admuser;
grant all on seq_adm_id_pk to :mainuser;
-- }}}

create table administrators_tbl ( --{{{
  adm_id_pk             dm_pk default nextval('seq_adm_id_pk'::regclass),
  adm_username          dm_text_mini_nn,
  adm_password          dm_password_nn,
  adm_salt              dm_text_micro,
  adm_name              dm_text_small_nn,
  adm_image             dm_image,
  adm_sex               dm_microenum, -- male|female
  adm_jobtitle          dm_text_small,
  adm_department        dm_text_small,
  adm_date_registration dm_datetime_now_nn,
  adm_enabled           dm_bool_true,

  constraint PK_ADMINISTRATORS_TBL primary key (adm_id_pk)
) tablespace :tablespace;
-- }}}

alter table administrators_tbl owner to :admuser;
grant select on administrators_tbl to :mainuser;
/* }}} Table: administrators_tbl */


/* Table: access_rules_administrators_tbl {{{
 * Права администраторов
 */
drop table if exists access_rules_administrators_tbl cascade;

-- sequence: seq_ara_id_pk {{{
drop sequence if exists seq_ara_id_pk;
create sequence seq_ara_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_ara_id_pk owner to :admuser;
grant all on seq_ara_id_pk to :mainuser;
-- }}}

create table access_rules_administrators_tbl ( --{{{
  ara_id_pk           dm_pk default nextval('seq_ara_id_pk'::regclass),
  ara_adm_id_fk       dm_ref_nn,
  ara_parent_id_fk    dm_ref,
  ara_module_name     dm_text_small_nn,
  ara_module_caption  dm_text_medium_nn,
  ara_table_name      dm_text_mini,
  ara_hide_module     dm_bool_false,
  ara_add_records     dm_microenum_nn, -- ALL|ONLY_YOUR|NOT
  ara_edit_records    dm_microenum_nn, -- ALL|ONLY_YOUR|NOT
  ara_delete_records  dm_microenum_nn, -- ALL|ONLY_YOUR|NOT
  ara_filter          dm_text_xxlarge,
  ara_enabled         dm_bool_true,

  constraint PK_ACCESS_RULES_ADMINISTRATORS_TBL primary key (ara_id_pk)
) tablespace :tablespace;
-- }}}

alter table access_rules_administrators_tbl owner to :admuser;
grant all on access_rules_administrators_tbl to :mainuser;
/* }}} Table: access_rules_administrators_tbl */


set client_min_messages = 'notice';
\echo 'Tables by module Access created.'
