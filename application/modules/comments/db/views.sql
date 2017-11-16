\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* View: comments_vw {{{
 * Комментрии
 */
drop view if exists comments_vw cascade;

create or replace view comments_vw as
  select cmt_id_pk as id, cmt_subject_id_fk as subject_id, cmt_name as name,
    cmt_email as email, cmt_text as text, cmt_type as type,
    to_char(cmt_datetime, 'DD/MM/YYYY HH24:MI') as datetime,
    cmt_level as level
  from comments_tbl
  where cmt_enabled = true
  order by cmt_left_id_fk;

alter view comments_vw owner to :admuser;
grant select on comments_vw to :mainuser;
/* }}} View: comments_vw */


set client_min_messages = 'notice';
\echo 'Views by module Comments created.'
