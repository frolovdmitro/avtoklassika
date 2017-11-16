\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* View: banners_vw {{{
 * Баннеры отображаемые в указанной площадке
 */
drop view if exists banners_vw cascade;

create or replace view banners_vw as
  select bnr_file_ru as file_ru, bnr_file_en as file_en, bnr_file_de as file_de,
  bnr_url as url, bnp_type as type, bnr_languages as languages,
  case when bnr_target_blank = true then 1 else null end as target_blank
  from banners_tbl
  left join banners_places_tbl on bnp_id_pk = bnr_bnp_id_fk
  where bnr_enabled = true and now() between
  coalesce(bnr_start_date, now()) and
  coalesce(bnr_finish_date, now() + '+1000 years');

alter view banners_vw owner to :admuser;
grant select on banners_vw to :mainuser;
/* }}} View: banners_vw */


/* View: payment_methods_vw {{{
 * Методы оплаты
 */
drop view if exists payment_methods_vw cascade;

create or replace view payment_methods_vw as
  select pym_type as type, pym_name_ru as name_ru, pym_name_en as name_en,
    pym_name_de as name_de, pym_description_ru as description_ru,
    pym_description_en as description_en, pym_description_de as description_de,
    pym_languages as languages
  from payment_methods_tbl
  where pym_enabled = true
  order by pym_order;

alter view payment_methods_vw owner to :admuser;
grant select on payment_methods_vw to :mainuser;
/* }}} View: payment_methods_vw */


/* View: delivery_methods_vw {{{
 * Методы доставки
 */
drop view if exists delivery_methods_vw cascade;

create or replace view delivery_methods_vw as
  select dvm_type as type, dvm_name_ru as name_ru, dvm_name_en as name_en,
    dvm_name_de as name_de, dvm_description_ru as description_ru,
    dvm_description_en as description_en, dvm_description_de as description_de,
    dvm_languages as languages
  from delivery_methods_tbl
  where dvm_enabled = true
  order by dvm_order;

alter view delivery_methods_vw owner to :admuser;
grant select on delivery_methods_vw to :mainuser;
/* }}} View: delivery_methods_vw */


/* View: currencies_vw {{{
 * Валюты
 */
drop view if exists currencies_vw cascade;

create or replace view currencies_vw as
  select rat_currency_ru as currency_ru, rat_currency_en as currency_en,
    rat_currency_de as currency_de, rat_short_name_ru as short_name_ru,
    rat_short_name_en as short_name_en, rat_short_name_de as short_name_de,
    rat_synonym as synonym, rat_value as value, rat_default as default,
    (rat_value /
      (select rat_value from rates_tbl where rat_default = true limit 1))::FLOAT
      as ratio
  from rates_tbl
  where rat_enabled = true
  order by rat_order;

alter view currencies_vw owner to :admuser;
grant select on currencies_vw to :mainuser;
/* }}} View: currencies_vw */


/* Function: get_cost_by_currency_fn {{{
 * Пересчитывает стоимость с одной валюты в другую
 */
drop function if exists get_cost_by_currency_fn(cost dm_cost, _from varchar, _to varchar) cascade;

create or replace function get_cost_by_currency_fn(_cost dm_cost, _from varchar,
  _to varchar)
  returns dm_cost as
$BODY$
  select (_cost *
    (select rat_value from rates_tbl where rat_synonym = _from) /
    (select rat_value from rates_tbl where rat_synonym = _to)
  )::dm_cost;
$BODY$
  language sql;
/* }}} Function: get_cost_by_currency_fn */


/* View: sitemap_categories_vw {{{
 * Список категорий и их параметров для карты сайта
 */
drop view if exists sitemap_categories_vw cascade;

create or replace view sitemap_categories_vw as
  select
    10 as order,
    'index' as id,
    'index' as synonym,
    '/' as root_route,
    null as route,
    null as parent,
    'Главная' as name,
    true is_last,
    null as table_name
  union all
  select
    20 as order,
    'car' as id,
    'car' as synonym,
    null as root_route,
    '/car/{synonym}/' as route,
    null as parent,
    'Автомобили' as name,
    false as is_last,
    'cars_sitemap_vw' as table_name
  union all
  select
    24 as order,
    'car_price' as id,
    'car_price' as synonym,
    null as root_route,
    '/car/{synonym}/price/' as route,
    null as parent,
    'Прайс деталей автомобиля' as name,
    false as is_last,
    'cars_sitemap_vw' as table_name
  union all
  select
    30 as order,
    'news' as id,
    'news' as synonym,
    '/news/' as root_route,
    '/news/{synonym}/' as route,
    null as parent,
    'Новости' as name,
    false as is_last,
    'news_sitemap_vw' as table_name
  union all
  select
    40 as order,
    'news_category' as id,
    'news_category' as synonym,
    null as root_route,
    '/news/category/{synonym}/' as route,
    null as parent,
    'Категории новостей' as name,
    false as is_last,
    'news_categories_sitemap_vw' as table_name
  union all
  select
    50 as order,
    'news_car_category' as id,
    'news_car_category' as synonym,
    null as root_route,
    '/news/category/{category_synonym}/{car_synonym}/' as route,
    null as parent,
    'Категория новостей конкретного автомобиля' as name,
    false as is_last,
    'news_categories_cars_sitemap_vw' as table_name
  union all
  select
    60 as order,
    'pages' as id,
    'pages' as synonym,
    null as root_route,
    '/{synonym}/' as route,
    null as parent,
    'Статические страницы' as name,
    false as is_last,
    'pages_sitemap_vw' as table_name
  union all
  select
    61 as order,
    'ads' as id,
    'ads' as synonym,
    '/ads/' as root_route,
    '/ads/{type}/{synonym}/' as route,
    null as parent,
    'Объявления' as name,
    false as is_last,
    'adverts_sitemap_vw' as table_name
  union all
  select
    220 as order,
    concat('car_autoparts', car_id_pk)::VARCHAR as id,
    concat('car_autoparts=', car_id_pk) as synonym,
    null as root_route,
    '/car/{car_synonym}/{synonym}/' as route,
    null as parent,
    concat('Группы автозапчастей ', coalesce(coalesce(car_name_ru, car_name_en), car_name_de)) as name,
    false as is_last,
    'autoparts_sitemap_vw' as table_name
  from (select * from cars_tbl order by car_order) car
  where car_enabled = true
  union all
  select
    225 as order,
    concat('car_details', car_id_pk) as id,
    concat('car_details=', car_id_pk) as synonym,
    null as root_route,
    '/car/{car_synonym}/{apt_synonym}/{synonym}/' as route,
    null as parent,
    concat('Детали ', coalesce(coalesce(car_name_ru, car_name_en), car_name_de)) as name,
    false as is_last,
    'details_sitemap_vw' as table_name
  from (select * from cars_tbl order by car_order) car
  where car_enabled = true
  union all
  select
    228 as order,
    concat('car_docs', car_id_pk) as id,
    concat('car_docs=', car_id_pk) as synonym,
    '/car/{car_synonym}/documentations/' as root_route,
    '/car/{car_synonym}/documentations/{synonym}/' as route,
    null as parent,
    concat('Документация ', coalesce(coalesce(car_name_ru, car_name_en), car_name_de)) as name,
    false as is_last,
    'car_docs_sitemap_vw' as table_name
  from (select * from cars_tbl order by car_order) car
  where car_enabled = true;

alter view sitemap_categories_vw owner to :admuser;
grant all on sitemap_categories_vw to :mainuser;
/* }}} View: sitemap_categories_vw */


/* View: sitemap_vw {{{
 * sitemap.xml
 */
drop view if exists sitemap_vw cascade;

create or replace view sitemap_vw as
  select
    id::varchar as id,
    synonym::varchar,
    coalesce(smp_enabled::boolean, true) as enabled,
    root_route::varchar as route, name::varchar,
    smp_lastmod::timestamp as lastmod,
    smp_changefreq::varchar as changefreq,
    smp_priority::float as priority,
    is_last, 0::integer as order_num,
    null::varchar as languages
  from sitemap_categories_vw scg
  left join sitemap_tbl on smp_synonym = synonym
  left join (
    select parent, count(*) as childs
    from sitemap_categories_vw
    group by parent
  ) cnt on cnt.parent = synonym
  where scg.parent is null
  union all
  select * from sitemap_all_pages_fn();

alter view sitemap_vw owner to :admuser;
grant select on sitemap_vw to :mainuser;
/* }}} View: sitemap_vw */


set client_min_messages = 'notice';
\echo 'Views by module Directories created.'
