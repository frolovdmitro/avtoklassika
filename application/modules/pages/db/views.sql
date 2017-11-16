\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* View: pages_sitemap_vw {{{
 * Представление для построения sitemap.xml
 */
drop view if exists pages_sitemap_vw cascade;

create or replace view pages_sitemap_vw as
  select pg_id_pk as id,
  pg_synonym as synonym,
  coalesce(coalesce(pg_caption_ru, pg_caption_en), pg_caption_de) as name,
  pg_lastmod as lastmod, pg_id_pk as ordered_field,
  pg_languages as languages
  from pages_tbl
  where pg_enabled = true;

alter view pages_sitemap_vw owner to :admuser;
grant select on pages_sitemap_vw to :mainuser;
/* }}} View: pages_sitemap_vw */


/* View: pages_files_vw {{{
 * 
 */
drop view if exists pages_files_vw cascade;

create or replace view pages_files_vw as
  select pfl_id_pk as id
    , pg_id_pk as page_id
    , pg_synonym as page_synonym
    , pg_title_ru as page_title_ru
    , pg_title_en as page_title_en
    , pg_title_de as page_title_de
    , pg_caption_ru as page_caption_ru
    , pg_caption_en as page_caption_en
    , pg_caption_de as page_caption_de
    , pg_keywords_ru as page_keywords_ru
    , pg_keywords_en as page_keywords_en
    , pg_keywords_de as page_keywords_de
    , pg_description_ru as page_description_ru
    , pg_description_en as page_description_en
    , pg_description_de as page_description_de
    , pg_css_class as page_css_class
    , pg_languages as page_languages
    , pg_enabled as page_enabled
    , pfl_name_ru as file_name_ru
    , pfl_name_en as file_name_en
    , pfl_name_de as file_name_de
    , pfl_file as file
    , pfl_width as width
    , pfl_height as height
    , pfl_languages as languages
  from pages_files_tbl
  join pages_tbl on pg_id_pk = pfl_pg_id_fk
;

alter view pages_files_vw owner to :admuser;
grant select on pages_files_vw to :mainuser;
/* }}} View: pages_files_vw */


set client_min_messages = 'notice';
\echo 'Views by module Pages created.'
