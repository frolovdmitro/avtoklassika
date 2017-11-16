\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* View: slider_vw {{{
 * Слайдер на главной странице
 */
drop view if exists slider_vw cascade;

create or replace view slider_vw as
  select sld_name_ru as name_ru, sld_name_en as name_en, sld_name_de as name_de,
  sld_image_ru as image_ru, sld_image_en as image_en, sld_image_de as image_de,
  sld_link as link,
  sld_languages as languages,
  case when sld_target_blank= true then 1 else null end as target_blank
  from slider_tbl
  where sld_enabled = true
  order by sld_order;

alter view slider_vw owner to :admuser;
grant select on slider_vw to :mainuser;
/* }}} View: slider_vw */


set client_min_messages = 'notice';
\echo 'Views by module Slider created.'
