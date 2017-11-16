\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* View: access_rules_administrators_vw {{{
 * Права доступа администраторов
 */
drop view if exists access_rules_administrators_vw cascade;

create or replace view access_rules_administrators_vw as
  select ara_id_pk as id
      , adm_id_pk as admin_id
      , adm_username as admin_username
      , adm_password as admin_password
      , adm_salt as admin_salt
      , adm_name as admin_name
      , adm_image as admin_image
      , adm_sex as admin_sex
      , adm_jobtitle as admin_jobtitle
      , adm_department as admin_department
      , adm_date_registration as admin_date_registration
      , adm_enabled as admin_enabled
      , ara_parent_id_fk as parent_id
      , ara_module_name as module_name
      , ara_module_caption as module_caption
      , ara_table_name as table_name
      , ara_hide_module as hide_module
      , ara_add_records as add_records
      , ara_edit_records as edit_records
      , ara_delete_records as delete_records
      , ara_filter as filter
      , ara_enabled as enabled
    from access_rules_administrators_tbl
    join administrators_tbl on adm_id_pk = ara_adm_id_fk;

alter view access_rules_administrators_vw owner to :admuser;
grant select on access_rules_administrators_vw to :mainuser;
/* }}} View: access_rules_administrators_vw */


set client_min_messages = 'notice';
\echo 'Views by module Access created.'
