\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* View: mails_vw {{{
 * Письма расслок
 */
drop view if exists mails_vw cascade;

create or replace view mails_vw as
  select mls_id_pk as id
     , mlr_id_pk as mailer_id
     , mlr_type as mailer_type
     , mlr_datetime as mailer_datetime
     , mlr_subject_ru as mailer_idsubject_ru
     , mlr_subject_en as mailer_idsubject_en
     , mlr_subject_de as mailer_idsubject_de
     , mlr_note as mailer_note
     , mlr_languages as mailer_languages
     , mls_parent_type as parent_type
     , mls_parent_id_fk as parent_id_fk
     , mls_type as type
     , mls_datetime as datetime
     , mls_email as email
     , mls_subject as subject
     , mls_body as body
     , mls_opened as opened
     , mls_link as link
     , mls_unsubscribe as unsubscribe
     , mls_unsubscribed as unsubscribed
     , mls_status as status
  from mails_tbl
  join mailers_tbl on mlr_id_pk = mls_mlr_id_fk;

alter view mails_vw owner to :admuser;
grant select on mails_vw to :mainuser;
/* }}} View: mails_vw */


/* View: count_mails_vw {{{
 * Количество писем в расслке
 */
drop view if exists count_mails_vw cascade;

create or replace view count_mails_vw as
  select mls_parent_id_fk, count(mls_id_pk) as count_mails, mls_parent_type
  from mails_tbl
  group by mls_parent_id_fk, mls_parent_type;

alter view count_mails_vw owner to :admuser;
grant select on count_mails_vw to :mainuser;
/* }}} View: count_mails_vw */


set client_min_messages = 'notice';
\echo 'Views by module Mailer created.'
