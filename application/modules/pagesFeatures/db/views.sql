\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* View: pages_features_vw {{{
 * Дополнительные характиристики урл
 */
drop view if exists pages_features_vw cascade;

create or replace view pages_features_vw as
  select pft_h1_en as h1_en, pft_h1_ru as h1_ru, pft_h1_de as h1_de,
    pft_breadcrumb_en as breadcrumb_en, pft_breadcrumb_ru as breadcrumb_ru,
    pft_breadcrumb_de as breadcrumb_de, pft_title_en as title_en,
    pft_title_ru as title_ru, pft_title_de as title_de,
    pft_description_en as description_en,
    pft_description_ru as description_ru,
    pft_description_de as description_de,
    pft_keywords_en as keywords_en, pft_keywords_ru as keywords_ru,
    pft_keywords_de as keywords_de, pft_text_en as text_en,
    pft_text_ru as text_ru, pft_text_de as text_de, pft_metas as metas,
    pft_seo_header_en as seo_header_en, pft_seo_header_ru as seo_header_ru,
    pft_seo_header_de as seo_header_de, pft_seo_text_en as seo_text_en,
    pft_seo_text_ru as seo_text_ru, pft_seo_text_de as seo_text_de,
    pft_languages as languages, pft_without_page as without_page,
    pft_route as route
  from pages_features_tbl
  where pft_enabled = true
  ;

alter view pages_features_vw owner to :admuser;
grant select on pages_features_vw to :mainuser;
/* }}} View: pages_features_vw */


set client_min_messages = 'notice';
\echo 'Views by module PagesFeatures created.'
