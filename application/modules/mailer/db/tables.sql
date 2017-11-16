\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Table: mailers_tbl {{{
 * Рассылки
 */
drop table if exists mailers_tbl cascade;

-- sequence: seq_mlr_id_pk {{{
drop sequence if exists seq_mlr_id_pk;
create sequence seq_mlr_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_mlr_id_pk owner to :admuser;
grant all on seq_mlr_id_pk to :mainuser;
-- }}}

create table mailers_tbl ( --{{{
   mlr_id_pk      dm_pk default nextval('seq_mlr_id_pk'::regclass),
   mlr_type       dm_smallenum_nn,
   mlr_datetime   dm_datetime_now_nn,
   mlr_subject_ru dm_text_large,
   mlr_subject_en dm_text_large,
   mlr_subject_de dm_text_large,
   mlr_note       dm_text_large,
   mlr_languages  dm_languages,

   constraint PK_MAILERS_TBL primary key (mlr_id_pk)
) tablespace :tablespace;
-- }}}

alter table mailers_tbl owner to :admuser;
grant select, insert, update on mailers_tbl to :mainuser;
/* }}} Table: mailers_tbl */


/* Table: mails_tbl {{{
 * Рассылки
 */
drop table if exists mails_tbl cascade;

-- sequence: seq_mls_id_pk {{{
drop sequence if exists seq_mls_id_pk;
create sequence seq_mls_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_mls_id_pk owner to :admuser;
grant all on seq_mls_id_pk to :mainuser;
-- }}}

create table mails_tbl ( --{{{
   mls_id_pk        dm_pk default nextval('seq_mls_id_pk'::regclass),
   mls_mlr_id_fk    dm_ref,
   mls_parent_type  dm_microenum_nn, --user
   mls_parent_id_fk dm_ref,
   mls_type         dm_smallenum, --subscribe|mail_administration
   mls_datetime     dm_datetime_now_nn,
   mls_email        dm_email_nn,
   mls_subject      dm_text_large,
   mls_body         dm_text,
   mls_opened       dm_bool_false,
   mls_link         dm_url,
   mls_unsubscribe  dm_text_medium,
   mls_unsubscribed dm_bool_false,
   mls_status       dm_microenum, --send|error

   constraint PK_MAILS_TBL primary key (mls_id_pk)
) tablespace :tablespace;
-- }}}

alter table mails_tbl owner to :admuser;
grant select, insert, update on mails_tbl to :mainuser;
/* }}} Table: mails_tbl */


set client_min_messages = 'notice';
\echo 'Tables by module Mailer created.'
