\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Table: smm_tbl {{{
 * SMM
 */
drop table if exists smm_tbl;

-- sequence: seq_smm_id_pk {{{
drop sequence if exists seq_smm_id_pk;
create sequence seq_smm_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_smm_id_pk owner to :admuser;
grant all on seq_smm_id_pk to :mainuser;
-- }}}

create table smm_tbl ( --{{{
  smm_id_pk      dm_pk default nextval('seq_smm_id_pk'::regclass),
  smm_type             dm_text_small_nn,
  smm_id               dm_text_small,
  smm_page_id          dm_text_small,
  smm_name_ru          dm_text_small,
  smm_name_en          dm_text_small,
  smm_name_de          dm_text_small,
  smm_url              dm_text_medium,
  smm_css_class        dm_text_small,
  smm_show_like_button dm_bool_true,
  smm_show_like_box    dm_bool_true,
  smm_languages        dm_languages,
  smm_order      dm_order default currval('seq_smm_id_pk'::regclass),
  smm_enabled    dm_bool_true,

  constraint PK_SMM_TBL primary key (smm_id_pk)
) tablespace :tablespace;
-- }}}

alter table smm_tbl owner to :admuser;
grant select on smm_tbl to :mainuser;
/* }}} Table: smm_tbl */


set client_min_messages = 'notice';
\echo 'Tables by module Smm created.'
