\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* View: car_market_vw {{{
 * Объявления
 */
drop view if exists car_market_vw cascade;

create or replace view car_market_vw as
  select cmk_id_pk as id,
    cmk_general_features_ru as general_features_ru,
    cmk_general_features_en as general_features_en,
    cmk_general_features_de as general_features_de,
    cmk_small_description_ru as small_description_ru,
    cmk_small_description_en as small_description_en,
    cmk_small_description_de as small_description_de,
    cmk_image as image, cmk_price_eur as price, cmk_year as year,
    car_name_ru, car_name_en, car_name_de, cmk_is_original as is_original
  from car_market_tbl
  left join cars_tbl on car_id_pk = cmk_car_id_fk
  where cmk_enabled = true
  order by cmk_date_create desc;

alter view car_market_vw owner to :admuser;
grant select on car_market_vw to :mainuser;
/* }}} View: car_market_vw */


/* View: car_market_info_vw {{{
 * Объявления
 */
drop view if exists car_market_info_vw cascade;

create or replace view car_market_info_vw as
  select cmk_id_pk as id, cmk_background as background,
    cmk_h1_ru as h1_ru, cmk_h1_en as h1_en, cmk_h1_de as h1_de,
    cmk_title_ru as title_ru, cmk_title_en as title_en, cmk_title_de as title_de,
    cmk_description_ru as description_ru, cmk_description_en as description_en,
    cmk_description_de as description_de, cmk_keywords_ru as keywords_ru,
    cmk_keywords_en as keywords_en, cmk_keywords_de as keywords_de,
    cmk_image as image, cmk_price_eur as price, cmk_year as year,
    car_name_ru, car_name_en, car_name_de, cmk_seria as seria,
    cmk_is_original as is_original
  from car_market_tbl
  left join cars_tbl on car_id_pk = cmk_car_id_fk
  where cmk_enabled = true
  order by cmk_date_create desc;

alter view car_market_info_vw owner to :admuser;
grant select on car_market_info_vw to :mainuser;
/* }}} View: car_market_info_vw */


/* View: car_market_photos_vw {{{
 * Объявления
 */
drop view if exists car_market_photos_vw cascade;

create or replace view car_market_photos_vw as
  select cmp_cmk_id_fk as car_market_id, cmp_name_ru as name_ru,
    cmp_name_en as name_en, cmp_name_de as name_de,
    cmp_image as image, cmp_order as ord
  from car_market_photos_tbl
  where cmp_enabled = true;

alter view car_market_photos_vw owner to :admuser;
grant select on car_market_photos_vw to :mainuser;
/* }}} View: car_market_photos_vw */


/* View: car_market_features_vw {{{
 * Объявления
 */
drop view if exists car_market_features_vw cascade;

create or replace view car_market_features_vw as
  select cmf_cmk_id_fk as car_market_id, cmf_header_ru as header_ru,
    cmf_header_en as header_en, cmf_header_de as header_de,
    cmf_text_ru as text_ru, cmf_text_en as text_en, cmf_text_de as text_de,
    cmf_icon as icon, cmf_order as ord
  from car_market_features_tbl
  where cmf_enabled = true;

alter view car_market_features_vw owner to :admuser;
grant select on car_market_features_vw to :mainuser;
/* }}} View: car_market_features_vw */


/* View: car_market_descriptions_vw {{{
 * Объявления
 */
drop view if exists car_market_descriptions_vw cascade;

create or replace view car_market_descriptions_vw as
  select cmd_cmk_id_fk as car_market_id, cmd_header_ru as header_ru,
    cmd_header_en as header_en, cmd_header_de as header_de,
    cmd_text_ru as text_ru, cmd_text_en as text_en, cmd_text_de as text_de,
    cmd_image as image, cmd_youtube_id as youtube_id, cmd_order as ord
  from car_market_descriptions_tbl
  where cmd_enabled = true;

alter view car_market_descriptions_vw owner to :admuser;
grant select on car_market_descriptions_vw to :mainuser;
/* }}} View: car_market_descriptions_vw */


set client_min_messages = 'notice';
\echo 'Views by module CarMarket created.'
