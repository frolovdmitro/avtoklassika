\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Table: news_tbl {{{
 * Категории новостей
 */
drop table if exists news_tbl cascade;
drop sequence if exists seq_nw_id_pk;

-- sequence: seq_nw_id_pk {{{
create sequence seq_nw_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
ALTER SEQUENCE seq_nw_id_pk OWNER TO :admuser;
-- }}}

create table news_tbl ( -- {{{
  nw_id_pk                        dm_int_nn default nextval('seq_nw_id_pk'::regclass),
  nw_nct_id_fk                    dm_ref,
  nw_enabled                      dm_bool_true,
  nw_synonym                      dm_text_medium,
  nw_canonical                    dm_text_large,
  nw_datetime                     dm_datetime,
  nw_image                        dm_image,
  nw_name_ru                      dm_text,
  nw_name_en                      dm_text,
  nw_name_de                      dm_text,
  nw_title_ru                     dm_text,
  nw_title_en                     dm_text,
  nw_title_de                     dm_text,
  nw_keywords_ru                  dm_text,
  nw_keywords_en                  dm_text,
  nw_keywords_de                  dm_text,
  nw_description_ru               dm_text,
  nw_description_en               dm_text,
  nw_description_de               dm_text,
  nw_text_ru                      dm_text,
  nw_text_en                      dm_text,
  nw_text_de                      dm_text,
  nw_languages                    dm_languages,
  nw_count_views                  dm_int default 0,

  constraint PK_NEWS_TBL primary key (nw_id_pk)
) tablespace :tablespace;
-- }}}

alter table news_tbl add column nw_nwc_id_fk dm_ref;
alter table news_tbl add column nw_car_id_fk dm_ref;
alter table news_tbl add column nw_robots dm_text_small;
alter table news_tbl add column nw_last_update dm_datetime_now;

alter table news_tbl owner to :admuser;
grant select, update on news_tbl to :mainuser;
/* }}} Table: news_tbl */


/* Table: news_files_tbl {{{
 * Категории новостей
 */
drop table if exists news_files_tbl;
drop sequence if exists seq_nwf_id_pk;

-- sequence: seq_nwf_id_pk {{{
create sequence seq_nwf_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter table seq_nwf_id_pk OWNER to :admuser;
-- }}}

create table news_files_tbl ( -- {{{
  nwf_id_pk       dm_int_nn default nextval('seq_nwf_id_pk'::regclass),
  nwf_nw_id_fk    dm_ref,
  nwf_name        dm_text,
  nwf_file        dm_file,
  nwf_width       dm_int,
  nwf_height      dm_int,
  nwf_languages   dm_languages,

  constraint PK_NEWS_FILES_TBL primary key (nwf_id_pk)
) tablespace :tablespace;
-- }}}

ALTER TABLE news_files_tbl OWNER TO :admuser;
GRANT SELECT ON news_files_tbl TO :mainuser;
/* }}} Table: news_files_tbl */


/* Table: news_categories_tbl {{{
 * Категории новостей
 */
drop table if exists news_categories_tbl;

-- sequence: seq_nwc_id_pk {{{
drop sequence if exists seq_nwc_id_pk;
create sequence seq_nwc_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_nwc_id_pk owner to :admuser;
grant all on seq_nwc_id_pk to :mainuser;
-- }}}

-- sequence: seq_nwc_id_pk {{{
drop sequence if exists seq_nwc_order;
create sequence seq_nwc_order
  start with 1
  increment by 250
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_nwc_order owner to :admuser;
grant all on seq_nwc_order to :mainuser;
-- }}}

create table news_categories_tbl ( --{{{
  nwc_id_pk     dm_int_nn default nextval('seq_nwc_id_pk'::regclass),
  nwc_name_ru   dm_text_medium,
  nwc_name_en   dm_text_medium,
  nwc_name_de   dm_text_medium,
  nwc_synonym   dm_text_medium,
  nwc_title_ru       dm_text,
  nwc_title_en       dm_text,
  nwc_title_de       dm_text,
  nwc_keywords_ru    dm_text,
  nwc_keywords_en    dm_text,
  nwc_keywords_de    dm_text,
  nwc_description_ru dm_text,
  nwc_description_en dm_text,
  nwc_description_de dm_text,
  nwc_languages      dm_languages,
  nwc_order     dm_int default nextval('seq_nwc_order'::regclass),
  nwc_enabled   dm_bool_true,

  constraint PK_NEWS_CATEGORIES_TBL primary key (nwc_id_pk)
) tablespace :tablespace;
-- }}}

alter table news_categories_tbl add column nwc_lastmod dm_datetime_now;
alter table news_categories_tbl add column nwc_robots dm_text_small;

alter table news_categories_tbl owner to :admuser;
grant select on news_categories_tbl to :mainuser;
/* }}} Table: news_categories_tbl */


/* Table: news_tags_tbl {{{
 * Привязка новости к категориям
 */
drop table if exists news_tags_tbl;

-- sequence: seq_ntg_id_pk {{{
drop sequence if exists seq_ntg_id_pk;
create sequence seq_ntg_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_ntg_id_pk owner to :admuser;
grant all on seq_ntg_id_pk to :mainuser;
-- }}}

create table news_tags_tbl ( --{{{
  ntg_id_pk     dm_int default nextval('seq_ntg_id_pk'::regclass),
  ntg_nwc_id_fk dm_int,
  ntg_nw_id_fk dm_int,

  constraint PK_NEWS_TAGS_TBL primary key (ntg_id_pk)
) tablespace :tablespace;
-- }}}

alter table news_tags_tbl owner to :admuser;
grant select on news_tags_tbl to :mainuser;
/* }}} Table: news_tags_tbl */


set client_min_messages = 'notice';
\echo 'Tables by module News created.'
