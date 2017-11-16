\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Table: pages_features_tbl {{{
 * Дополнительные характеристики различных страниц
 */
drop table if exists pages_features_tbl cascade;

-- sequence: seq_pft_id_pk {{{
drop sequence if exists seq_pft_id_pk;
create sequence seq_pft_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_pft_id_pk owner to :admuser;
grant all on seq_pft_id_pk to :mainuser;
-- }}}

create table pages_features_tbl ( --{{{
  pft_id_pk           dm_pk default nextval('seq_pft_id_pk'::regclass),
  pft_without_page    dm_bool_true,
  pft_route           dm_text_large_nn,
  pft_h1_en           dm_text_medium,
  pft_h1_ru           dm_text_medium,
  pft_h1_ua           dm_text_medium,
  pft_h1_de           dm_text_medium,
  pft_breadcrumb_en   dm_text_medium,
  pft_breadcrumb_ru   dm_text_medium,
  pft_breadcrumb_ua   dm_text_medium,
  pft_breadcrumb_de   dm_text_medium,
  pft_title_en        dm_text_large,
  pft_title_ru        dm_text_large,
  pft_title_ua        dm_text_large,
  pft_title_de        dm_text_large,
  pft_description_en  dm_text_xlarge,
  pft_description_ru  dm_text_xlarge,
  pft_description_ua  dm_text_xlarge,
  pft_description_de  dm_text_xlarge,
  pft_keywords_en     dm_text_xlarge,
  pft_keywords_ru     dm_text_xlarge,
  pft_keywords_ua     dm_text_xlarge,
  pft_keywords_de     dm_text_xlarge,
  pft_text_en         dm_text,
  pft_text_ru         dm_text,
  pft_text_ua         dm_text,
  pft_text_de         dm_text,
  pft_metas           dm_text,
  pft_seo_header_en   dm_text_xlarge,
  pft_seo_header_ru   dm_text_xlarge,
  pft_seo_header_ua   dm_text_xlarge,
  pft_seo_header_de   dm_text_xlarge,
  pft_seo_text_en     dm_text,
  pft_seo_text_ru     dm_text,
  pft_seo_text_ua     dm_text,
  pft_seo_text_de     dm_text,
  pft_languages       dm_languages,
  pft_enabled         dm_bool_true,

  constraint PK_PAGES_FEATURES_TBL primary key (pft_id_pk)
) tablespace :tablespace;
-- }}}

alter table pages_features_tbl owner to :admuser;
grant select on pages_features_tbl to :mainuser;
/* }}} Table: pages_features_tbl */


set client_min_messages = 'notice';
\echo 'Tables by module PagesFeatures created.'
