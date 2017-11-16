\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Table: pages_tbl {{{
 * Статические страницы
 */
drop table if exists pages_tbl;

-- sequence: seq_pg_id_pk {{{
drop sequence if exists seq_pg_id_pk;
create sequence seq_pg_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_pg_id_pk owner to :admuser;
grant all on seq_pg_id_pk to :mainuser;
-- }}}

create table pages_tbl ( --{{{
  pg_id_pk          dm_pk default nextval('seq_pg_id_pk'::regclass),
  pg_synonym        dm_text_medium_nn,
  pg_canonical      dm_text_medium,
  pg_301_redirect   dm_text_medium,
  pg_robots         dm_text_small,
  pg_lastmod        dm_datetime_now,
  pg_title_ru       dm_text_large,
  pg_title_en       dm_text_large,
  pg_title_de       dm_text_large,
  pg_caption_ru     dm_text_large,
  pg_caption_en     dm_text_large,
  pg_caption_de     dm_text_large,
  pg_keywords_ru    dm_text_xlarge,
  pg_keywords_en    dm_text_xlarge,
  pg_keywords_de    dm_text_xlarge,
  pg_description_ru dm_text_xlarge,
  pg_description_en dm_text_xlarge,
  pg_description_de dm_text_xlarge,
  pg_text_ru        dm_text,
  pg_text_en        dm_text,
  pg_text_de        dm_text,
  pg_css_class      dm_text_small,
  pg_languages      dm_languages,
  pg_enabled        dm_bool_true,

  constraint PK_PAGES_TBL primary key (pg_id_pk)
) tablespace :tablespace;
-- }}}

alter table pages_tbl owner to :admuser;
grant select on pages_tbl to :mainuser;
/* }}} Table: pages_tbl */


/* Table: pages_files_tbl {{{
 *  Файлы для статических страниц
 */
drop table if exists pages_files_tbl;

-- sequence: seq_pfl_id_pk {{{
drop sequence if exists seq_pfl_id_pk;
create sequence seq_pfl_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_pfl_id_pk owner to :admuser;
grant all on seq_pfl_id_pk to :mainuser;
-- }}}

create table pages_files_tbl ( --{{{
  pfl_id_pk     dm_pk default nextval('seq_pfl_id_pk'::regclass),
  pfl_pg_id_fk  dm_ref_nn,
  pfl_name_ru   dm_text_medium,
  pfl_name_en   dm_text_medium,
  pfl_name_de   dm_text_medium,
  pfl_file      dm_file_nn,
  pfl_width     dm_int,
  pfl_height    dm_int,
  pfl_languages dm_languages,

  constraint PK_PAGES_FILES_TBL primary key (pfl_id_pk)
) tablespace :tablespace;
-- }}}

alter table pages_files_tbl owner to :admuser;
grant select on pages_files_tbl to :mainuser;
/* }}} Table: pages_files_tbl */


set client_min_messages = 'notice';
\echo 'Tables by module Pages created.'
