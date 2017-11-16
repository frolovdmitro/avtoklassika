\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* View: last_adverts_vw {{{
 * Последние объявления
 */
drop view if exists last_adverts_vw cascade;

create or replace view last_adverts_vw as
  select adv_id_pk as id,
  case when char_length(adv_title) > 50 then
    substring(adv_name, 0, 50)||'&hellip;' else adv_name end as name,
  replace(adv_image, '-bg', '-micro') as image,
  adv_type as type,
  get_cost_by_currency_fn(adv_cost, rat_synonym, 'usd') as cost_unformat,
  replace(replace(trim(to_char(
    get_cost_by_currency_fn(adv_cost, rat_synonym, 'usd')
  ,'999 999 999.99')), '.', ','), ',00', '') as cost
  from adverts_tbl
  left join rates_tbl on rat_id_pk = adv_rat_id_fk
  where adv_enabled = true and adv_image is not null
  order by adv_date_create desc;

alter view last_adverts_vw owner to :admuser;
grant select on last_adverts_vw to :mainuser;
/* }}} View: last_adverts_vw */


/* View: adverts_vw {{{
 * Объявления
 */
drop view if exists adverts_vw cascade;

create or replace view adverts_vw as
  select adv_id_pk as id, adv_category as category, adv_type as type,
    adv_name as name,
    to_char(adv_date_create, 'DD/MM/YYYY') as date,
    adv_image as image,
    replace(adv_image, '-bg', '-md') as image_medium,
    replace(adv_image, '-bg', '-sm') as image_small,
    replace(adv_image, '-bg', '-mini') as image_mini,
    adv_text as text,
    get_cost_by_currency_fn(adv_cost, rat_synonym, 'usd') as cost_unformat,
    replace(replace(trim(to_char(
      get_cost_by_currency_fn(adv_cost, rat_synonym, 'usd')
    ,'999 999 999.99')), '.', ','), ',00', '') as cost,
    coalesce(adv_user_name, usr_name) as user_name,
    coalesce(adv_user_city, concat(cnt_name_ru, ', ', usr_city), concat(cnt_name_ru, ', ', adv_user_city)) as user_city_ru,
    coalesce(adv_user_city, concat(cnt_name_en, ', ', usr_city), concat(cnt_name_en, ', ', adv_user_city)) as user_city_en,
    coalesce(adv_user_city, concat(cnt_name_de, ', ', usr_city), concat(cnt_name_de, ', ', adv_user_city)) as user_city_de,
    coalesce(adv_user_email, usr_email) as user_email,
    coalesce(adv_user_phone, usr_phones) as user_phone,
    regexp_replace(coalesce(adv_user_phone, usr_phones), '[\s\-()]', '', 'g') as user_phone_unformat,
    rat_id_pk as rate_id, rat_short_name_ru as short_name_ru,
    rat_short_name_en as short_name_en, rat_short_name_de as short_name_de,
    coalesce(adv_title, adv_name) as title, adv_description as description, adv_keywords as keywords,
    cnt_name_ru as country_ru, cnt_name_en as country_en,
    cnt_name_de as country_de, coalesce(count_comments, 0) as count_comments,
    coalesce(adv_user_city, usr_city) as city_ru,
    coalesce(adv_user_city, usr_city) as city_en,
    coalesce(adv_user_city, usr_city) as city_de
  from adverts_tbl
  left join users_tbl on usr_id_pk = adv_usr_id_fk
  left join countries_tbl on cnt_id_pk = usr_cnt_id_fk
  left join rates_tbl on rat_id_pk = adv_rat_id_fk
  left join (select subject_id, count(id) as count_comments
    from comments_vw where type = 'advert' group by subject_id) as cmt
      on adv_id_pk = cmt.subject_id
  where adv_enabled = true
  order by adv_date_create desc;

alter view adverts_vw owner to :admuser;
grant select on adverts_vw to :mainuser;
/* }}} View: adverts_vw */


/* View: adverts_sitemap_vw {{{
 * Представление для построения sitemap.xml
 */
drop view if exists adverts_sitemap_vw cascade;

create or replace view adverts_sitemap_vw as
  select adv_id_pk as id,
  adv_id_pk as synonym,
  adv_name as name,
  adv_type::VARCHAR as type,
  adv_date_create as lastmod,
  adv_id_pk::INTEGER as ordered_field,
  '|ru|en|de|'::VARCHAR as languages
  from adverts_tbl
  where adv_enabled = true
  order by adv_date_create desc
  limit 2000;

alter view adverts_sitemap_vw owner to :admuser;
grant select on adverts_sitemap_vw to :mainuser;
/* }}} View: pages_sitemap_vw */


/* View: adverts_attachments_vw {{{
 * Дополнительные фотографии
 */
drop view if exists adverts_attachments_vw cascade;

create or replace view adverts_attachments_vw as
  select ada_adv_id_fk as advert_id, adv_name as name, ada_image as image,
    replace(ada_image, '-bg', '-thm') as image_small
  from adverts_attachments_tbl
  join adverts_tbl on adv_id_pk = ada_adv_id_fk
  where ada_enabled = true
  order by ada_id_pk;

alter view adverts_attachments_vw owner to :admuser;
grant select on adverts_attachments_vw to :mainuser;
/* }}} View: adverts_attachments_vw */


set client_min_messages = 'notice';
\echo 'Views by module Adverts created.'
