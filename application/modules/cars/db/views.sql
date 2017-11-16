\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* View: cars_vw {{{
 * Список авто
 */
drop view if exists cars_vw cascade;

create or replace view cars_vw as
  select car_id_pk as id, car_name_ru as name_ru, car_name_en as name_en,
    car_name_de as name_de,
    car_synonym as synonym, car_image as image, car_image_active as image_active,
    car_languages as languages, crt_synonym as type
  from cars_tbl
  join car_types_tbl on car_crt_id_fk = crt_id_pk
  where car_enabled = true
  order by car_order;

alter view cars_vw owner to :admuser;
grant select on cars_vw to :mainuser;
/* }}} View: cars_vw */


/* View: cars_sitemap_vw {{{
 * Представление для построения sitemap.xml
 */
drop view if exists cars_sitemap_vw cascade;

create or replace view cars_sitemap_vw as
  select car_id_pk as id,
  car_synonym as synonym,
  coalesce(coalesce(car_name_ru, car_name_en), car_name_de) as name,
  car_last_update as lastmod, car_order as ordered_field,
  car_languages as languages
  from cars_tbl
  where car_enabled = true;

alter view cars_sitemap_vw owner to :admuser;
grant select on cars_sitemap_vw to :mainuser;
/* }}} View: pages_sitemap_vw */


/* View: car_info_vw {{{
 * Информация о автомобиле
 */
drop view if exists car_info_vw cascade;

create or replace view car_info_vw as
  select car_id_pk as id, car_name_ru as name_ru, car_name_en as name_en,
    car_name_de as name_de, car_synonym as synonym,
    car_title_ru as title_ru, car_title_en as title_en,
    car_title_de as title_de, car_keywords_ru as keywords_ru,
    car_keywords_en as keywords_en, car_keywords_de as keywords_de,
    car_description_ru as description_ru,
    car_description_en as description_en,
    car_description_de as description_de,

    car_docs_title_ru as doc_title_ru, car_docs_title_en as doc_title_en,
    car_docs_title_de as doc_title_de, car_docs_keywords_ru as doc_keywords_ru,
    car_docs_keywords_en as doc_keywords_en, car_docs_keywords_de as doc_keywords_de,
    car_docs_description_ru as doc_description_ru,
    car_docs_description_en as doc_description_en,
    car_docs_description_de as doc_description_de, car_docs_robots as doc_robots,

    car_price_title_ru as price_title_ru, car_price_title_en as price_title_en,
    car_price_title_de as price_title_de, car_price_keywords_ru as price_keywords_ru,
    car_price_keywords_en as price_keywords_en, car_price_keywords_de as price_keywords_de,
    car_price_description_ru as price_description_ru,
    car_price_description_en as price_description_en,
    car_price_description_de as price_description_de, car_price_robots as price_robots,

    car_seo_header_ru as seo_header_ru, car_seo_header_en as seo_header_en,
    car_seo_header_de as seo_header_de, car_seo_text_ru as seo_text_ru,
    car_seo_text_en as seo_text_en, car_seo_text_de as seo_text_de,
    car_seo_image as seo_image, car_languages as languages, car_robots as robots
  from cars_tbl
  where car_enabled = true;

alter view car_info_vw owner to :admuser;
grant select on car_info_vw to :mainuser;
/* }}} View: car_info_vw */


/* View: car_types_vw {{{
 * Типы автомобилей
 */
drop view if exists car_types_vw cascade;

create or replace view car_types_vw as
  select crt_name_ru as name_ru, crt_name_en as name_en, crt_name_de as name_de,
  crt_synonym as synonym, crt_languages as languages
  from car_types_tbl
  where crt_enabled = true
  order by crt_order;

alter view car_types_vw owner to :admuser;
grant select on car_types_vw to :mainuser;
/* }}} View: car_types_vw */


/* View: autoparts_sitemap_vw {{{
 * Представление для построения sitemap.xml
 */
drop view if exists autoparts_sitemap_vw cascade;

create or replace view autoparts_sitemap_vw as
  select apt_id_pk as id,
  apt_id_pk as synonym, car_synonym,
  coalesce(coalesce(apt_name_ru, apt_name_en), apt_name_de) as name,
  apt_lastmod as lastmod, apt_order as ordered_field,
  apt_car_id_fk as filter,
  apt_languages as languages
  from autoparts_tbl
  join cars_tbl on car_id_pk = apt_car_id_fk
  where apt_enabled = true;

alter view autoparts_sitemap_vw owner to :admuser;
grant select on autoparts_sitemap_vw to :mainuser;
/* }}} View: pages_sitemap_vw */


/* View: main_autoparts_vw {{{
 * Основные группы деталей
 */
drop view if exists main_autoparts_vw cascade;

create or replace view main_autoparts_vw as
  select apt_id_pk as id, apt_car_id_fk as car_id, apt_name_ru as name_ru,
  apt_name_en as name_en, apt_name_de as name_de, car_synonym,
  apt_image as image, apt_languages as languages, apt_order
  from autoparts_tbl
  join cars_tbl on car_id_pk = apt_car_id_fk
  where apt_parent_id_fk is null and apt_enabled = true
  order by apt_order;

alter view main_autoparts_vw owner to :admuser;
grant select on main_autoparts_vw to :mainuser;
/* }}} View: main_autoparts_vw */


/* View: autopart_info_vw {{{
 * Информация о группе автозапчастей
 */
drop view if exists autopart_info_vw cascade;

create or replace view autopart_info_vw as
  select car_id_pk as car_id, car_name_ru, car_name_en, car_name_de,
    car_synonym, apt_id_pk as id,
    apt_name_ru as name_ru, apt_name_en as name_en, apt_name_de as name_de,
    apt_title_ru as title_ru, apt_title_en as title_en,
    apt_title_de as title_de, apt_keywords_ru as keywords_ru,
    apt_keywords_en as keywords_en, apt_keywords_de as keywords_de,
    apt_description_ru as description_ru,
    apt_description_en as description_en,
    apt_description_de as description_de,
    apt_seo_header_ru as seo_header_ru, apt_seo_header_en as seo_header_en,
    apt_seo_header_de as seo_header_de, apt_seo_text_ru as seo_text_ru,
    apt_seo_text_en as seo_text_en, apt_seo_text_de as seo_text_de,
    apt_image as schema,
    apt_languages as languages, apt_robots as robots, apt_is_last as is_last
  from autoparts_tbl
  join cars_tbl on car_id_pk = apt_car_id_fk
  where car_enabled = true;

alter view autopart_info_vw owner to :admuser;
grant select on autopart_info_vw to :mainuser;
/* }}} View: autopart_info_vw */


/* View: details_sitemap_vw {{{
 * Представление для построения sitemap.xml
 */
drop view if exists details_sitemap_vw cascade;

create or replace view details_sitemap_vw as
  select dpt_id_pk as id,
  dpt_id_pk as synonym, car_synonym,
  apt_id_pk as apt_synonym,
  coalesce(coalesce(dpt_name_ru, dpt_name_en), dpt_name_de) as name,
  dpt_lastmod as lastmod, dpt_order as ordered_field,
  apt_car_id_fk as filter,
  dpt_languages as languages
  from details_autoparts_tbl
  join autoparts_tbl on apt_id_pk = dpt_apt_id_fk
  join cars_tbl on car_id_pk = apt_car_id_fk
  where apt_enabled = true and dpt_enabled = true;

alter view details_sitemap_vw owner to :admuser;
grant select on details_sitemap_vw to :mainuser;
/* }}} View: pages_sitemap_vw */


/* View: details_addition_autoparts_count_vw {{{
 * Кол-во дополнительных ссылок детали на другие автозапчасти
 */
drop view if exists details_addition_autoparts_count_vw cascade;

create or replace view details_addition_autoparts_count_vw as
  select daa_dpt_id_fk, count(daa_id_pk) as count_addition_autopars
  from details_addition_autoparts_tbl
  group by daa_dpt_id_fk;

alter view details_addition_autoparts_count_vw owner to :admuser;
grant select on details_addition_autoparts_count_vw to :mainuser;
/* }}} View: details_addition_autoparts_count_vw */


/* View: details_autoparts_vw {{{
 * Список деталей группы автозапчастей
 */
drop view if exists details_autoparts_vw cascade;

create or replace view details_autoparts_vw as
  select dpt_id_pk as id, dpt_num_detail as num_detail,
    dpt_name_ru as name_ru, dpt_name_en as name_en, dpt_name_de as name_de,
    apt_name_ru as autopart_name_ru, apt_name_en as autopart_name_en,
    apt_name_de as autopart_name_de,
    case when dpt_discount is null then dpt_cost
      else detail_new_cost_fn(dpt_id_pk) end as cost_unformat,
    case when dpt_discount is null then null
      else dpt_cost end as old_cost_unformat,
    case when dpt_discount is null then
      replace(replace(trim(to_char(dpt_cost,'999 999 999.99')), '.', ','), ',00', '')
    else replace(replace(trim(to_char(detail_new_cost_fn(dpt_id_pk),'999 999 999.99')), '.', ','), ',00', '') end as cost,
    case when dpt_discount is null then null
      else replace(replace(trim(to_char(dpt_cost,'999 999 999.99')), '.', ','), ',00', '') end as old_cost,
    case when dpt_often_buy = true then true else null end as often_buy,
    replace(dpt_image, '-bg', '-sm') as image,
    dpt_discount as discount,
    dpt_status as status, dpt_sale as sale, dpt_languages as languages,
    case when dpt_last_update > (now() + '- 20 days') then true else null end as new,
    car_synonym, apt_id_pk as autopart_id, car_id_pk as car_id
  from (
    select
      dpt_id_pk, dpt_num_detail, dpt_name_ru, dpt_name_en, dpt_name_de, apt_name_ru, apt_name_en,
      apt_name_de, dpt_cost, dpt_discount, dpt_image, dpt_enabled, dpt_presence,
      dpt_status, dpt_sale, dpt_languages, dpt_last_update, car_synonym, apt_id_pk, dpt_often_buy,
      car_id_pk
    from details_autoparts_tbl
    join autoparts_tbl on apt_id_pk = dpt_apt_id_fk
    join cars_tbl on car_id_pk = apt_car_id_fk
    union all
    select
      dpt_id_pk, dpt_num_detail, dpt_name_ru, dpt_name_en, dpt_name_de, apt_name_ru, apt_name_en,
      apt_name_de, dpt_cost, dpt_discount, dpt_image, dpt_enabled, dpt_presence,
      dpt_status, dpt_sale, dpt_languages, dpt_last_update, car_synonym, daa_apt_id_fk, dpt_often_buy,
      car_id_pk
    from
    details_autoparts_tbl
    join details_addition_autoparts_tbl on dpt_id_pk = daa_dpt_id_fk
    join autoparts_tbl on apt_id_pk = dpt_apt_id_fk
    join cars_tbl on car_id_pk = apt_car_id_fk
  ) tbl
  where dpt_enabled = true and coalesce(dpt_presence,0) > 0;

alter view details_autoparts_vw owner to :admuser;
grant select on details_autoparts_vw to :mainuser;
/* }}} View: details_autoparts_vw */


/* View: related_details_vw {{{
 * Сопутствующие товары
 */
drop view if exists related_details_vw cascade;

create or replace view related_details_vw as
  select dpt_id_pk as id, dpt_num_detail as num_detail,
    dpt_name_ru as name_ru, dpt_name_en as name_en, dpt_name_de as name_de,
    case when dpt_discount is null then dpt_cost
      else detail_new_cost_fn(dpt_id_pk) end as cost_unformat,
    case when dpt_discount is null then null
      else dpt_cost end as old_cost_unformat,
    case when dpt_discount is null then
      replace(replace(trim(to_char(dpt_cost,'999 999 999.99')), '.', ','), ',00', '')
    else replace(replace(trim(to_char(detail_new_cost_fn(dpt_id_pk),'999 999 999.99')), '.', ','), ',00', '') end as cost,
    case when dpt_discount is null then null
      else replace(replace(trim(to_char(dpt_cost,'999 999 999.99')), '.', ','), ',00', '') end as old_cost,
    case when dpt_often_buy = true then true else null end as often_buy,
    replace(dpt_image, '-bg', '-sm') as image,
    dpt_discount as discount, odd_ord_id_fk as order_id,
    dpt_status as status, dpt_sale as sale, dpt_languages as languages,
    case when dpt_last_update > (now() + '- 20 days') then true else null end as new,
    car_synonym, apt_id_pk as autopart_id, car_id_pk as car_id
  from orders_details_tbl
  join details_autoparts_tbl on dpt_id_pk = odd_dpt_id_fk
  join autoparts_tbl on apt_id_pk = dpt_apt_id_fk
  join cars_tbl on car_id_pk = apt_car_id_fk
  group by dpt_id_pk, dpt_num_detail, dpt_name_ru, dpt_name_en, dpt_name_de,
    dpt_discount, dpt_cost, dpt_often_buy, dpt_image, dpt_status, dpt_sale,
    dpt_languages, dpt_last_update, car_synonym, apt_id_pk, car_id_pk,
    odd_ord_id_fk;

alter view related_details_vw owner to :admuser;
grant select on related_details_vw to :mainuser;
/* }}} View: related_details_vw */


/* View: total_orders_by_details_vw {{{
 * Общая сумма и количество заказов в автомобиле
 */
drop view if exists total_orders_by_details_vw cascade;

create or replace view total_orders_by_details_vw as
  select odd_dpt_id_fk, count(odd_id_pk) as count_orders,
    sum(odd_count*odd_cost) as sum_orders
  from orders_details_tbl
  group by odd_dpt_id_fk;

alter view total_orders_by_details_vw owner to :admuser;
grant select on total_orders_by_details_vw to :mainuser;
/* }}} View: total_orders_by_details_vw */


/* View: detail_info_vw {{{
 * Информация о детали
 */
drop view if exists detail_info_vw cascade;

create or replace view detail_info_vw as
  select dpt_id_pk as id, dpt_num_detail as detail_num,
    dpt_name_ru as name_ru, dpt_name_en as name_en, dpt_name_de as name_de,
    dpt_title_ru as title_ru,
    dpt_title_en as title_en,
    dpt_title_de as title_de,
    dpt_keywords_ru as keywords_ru, dpt_keywords_en as keywords_en,
    dpt_keywords_de as keywords_de,
    dpt_description_ru as description_ru, dpt_description_en as description_en,
    dpt_description_de as description_de,
    dpt_info_ru as info_ru, dpt_info_en as info_en, dpt_info_de as info_de,
    apt_name_ru as autopart_name_ru, apt_name_en as autopart_name_en,
    apt_name_de as autopart_name_de, dpt_robots as robots,
    case when dpt_discount is null then dpt_cost
      else detail_new_cost_fn(dpt_id_pk) end as cost_unformat,
    case when dpt_discount is null then
      replace(replace(trim(to_char(dpt_cost,'999 999 999.99')), '.', ','), ',00', '')
    else replace(replace(trim(to_char(detail_new_cost_fn(dpt_id_pk),'999 999 999.99')), '.', ','), ',00', '') end as cost,
    case when dpt_discount is null then null
      else replace(replace(trim(to_char(dpt_cost,'999 999 999.99')), '.', ','), ',00', '') end as old_cost,
    case when dpt_often_buy = true then true else null end as often_buy,
    dpt_image as image,
    replace(dpt_image, '-bg', '-md') as image_medium,
    replace(dpt_image, '-bg', '-sm') as image_small,
    replace(dpt_image, '-bg', '-mini') as image_mini,
    apt_image as schema,
    dpt_discount as discount,
    dpt_status as status, dpt_sale as sale, dpt_languages as languages,
    case when dpt_last_update > (now() + '- 20 days') then true else null end as new,
    car_synonym, car_name_ru, car_name_en, car_name_de, apt_id_pk as autopart_id,
    car_id_pk as car_id, coalesce(count_comments, 0) as count_comments
  from details_autoparts_tbl
  join autoparts_tbl on apt_id_pk = dpt_apt_id_fk
  join cars_tbl on car_id_pk = apt_car_id_fk
  left join (select subject_id, count(id) as count_comments
    from comments_vw where type = 'detail' group by subject_id) as cmt
      on dpt_id_pk = cmt.subject_id
  where dpt_enabled = true;

alter view detail_info_vw owner to :admuser;
grant select on detail_info_vw to :mainuser;
/* }}} View: detail_info_vw */


/* View: search_details_vw {{{
 * Результат поиска
 */
drop view if exists search_details_vw cascade;

create or replace view search_details_vw as
  select dpt_id_pk as id, dpt_name_ru as name_ru, dpt_name_en as name_en,
    dpt_name_de as name_de, dpt_num_detail as num, car_synonym as carsy,
    car_name_ru, car_name_en, car_name_de, apt_id_pk as aid,
    replace(dpt_image, '-bg', '-mini') as im,
    case when coalesce(dpt_presence,0) = 0 then 0 else 1 end as pr
  from details_autoparts_tbl
  join autoparts_tbl on apt_id_pk = dpt_apt_id_fk
  join cars_tbl on car_id_pk = apt_car_id_fk
  where dpt_enabled = true and
    dpt_id_pk not in (select id from search_details_prefetch_vw);

alter view search_details_vw owner to :admuser;
grant select on search_details_vw to :mainuser;
/* }}} View: search_details_vw */


/* View: search_details_prefetch_vw {{{
 * предзагруженные детали
 */
drop view if exists search_details_prefetch_vw cascade;

create or replace view search_details_prefetch_vw as
  select dpt_id_pk as id, dpt_name_ru as name_ru, dpt_name_en as name_en,
    dpt_name_de as name_de, dpt_num_detail as num, car_synonym as carsy,
    car_name_ru, car_name_en, car_name_de, apt_id_pk as aid,
    replace(dpt_image, '-bg', '-mini') as im,
    case when coalesce(dpt_presence,0) = 0 then 0 else 1 end as pr
  from details_autoparts_tbl
  join autoparts_tbl on apt_id_pk = dpt_apt_id_fk
  join cars_tbl on car_id_pk = apt_car_id_fk
  left join (select odd_dpt_id_fk, count(odd_id_pk) as count_orders from orders_details_tbl group by odd_dpt_id_fk) odd on odd_dpt_id_fk = dpt_id_pk
  where dpt_enabled = true and coalesce(dpt_presence,0) > 0 and odd_dpt_id_fk is null and (dpt_last_update > now() + '-2 month' or coalesce(dpt_discount,0) != 0)
  union all
  select dpt_id_pk as id, dpt_name_ru as name_ru, dpt_name_en as name_en,
    dpt_name_de as name_de, dpt_num_detail as num_detail, car_synonym,
    car_name_ru, car_name_en, car_name_de, apt_id_pk as autopart_id,
    replace(dpt_image, '-bg', '-mini') as image,
    case when coalesce(dpt_presence,0) = 0 then 0 else 1 end as presence
  from details_autoparts_tbl
  join autoparts_tbl on apt_id_pk = dpt_apt_id_fk
  join cars_tbl on car_id_pk = apt_car_id_fk
  join (select odd_dpt_id_fk, count(odd_id_pk) as count_orders from orders_details_tbl group by odd_dpt_id_fk) odd on odd_dpt_id_fk = dpt_id_pk
  where dpt_enabled = true and coalesce(dpt_presence,0) > 0 and count_orders >= 5;

alter view search_details_prefetch_vw owner to :admuser;
grant select on search_details_prefetch_vw to :mainuser;
/* }}} View: search_details_prefetch_vw */


/* View: total_count_by_car_vw {{{
 * Количество деталей в автомобиле
 */
drop view if exists total_count_by_car_vw cascade;

create or replace view total_count_by_car_vw as
  select apt.apt_car_id_fk, count_presence, count(dpt_id_pk) count_total
  from details_autoparts_tbl
  left join autoparts_tbl apt on apt_id_pk = dpt_apt_id_fk
  left join (
    select apt_car_id_fk, count(dpt_id_pk) as count_presence
    from details_autoparts_tbl
    left join autoparts_tbl on apt_id_pk = dpt_apt_id_fk
    where coalesce(dpt_presence, 0) > 0
    group by apt_car_id_fk
  ) pr on apt.apt_car_id_fk = pr.apt_car_id_fk
  group by apt.apt_car_id_fk, count_presence;

alter view total_count_by_car_vw owner to :admuser;
grant select on total_count_by_car_vw to :mainuser;
/* }}} View: total_count_by_car_vw */


/* View: total_sum_by_car_vw {{{
 * Сумма заказов в магазине
 */
drop view if exists total_sum_by_car_vw cascade;

create or replace view total_sum_by_car_vw as
  select apt_car_id_fk, sum(odd_count) as count_orders,
    sum(odd_count*odd_cost) as sum_orders
  from orders_details_tbl
  left join details_autoparts_tbl on dpt_id_pk = odd_dpt_id_fk
  left join autoparts_tbl on apt_id_pk = dpt_apt_id_fk
  group by apt_car_id_fk;

alter view total_sum_by_car_vw owner to :admuser;
grant select on total_sum_by_car_vw to :mainuser;
/* }}} View: total_sum_by_car_vw */


/* View: details_pricelist_vw {{{
 * Прайслист доступных деталей
 */
drop view if exists details_pricelist_vw cascade;

create or replace view details_pricelist_vw as
  select dpt_num_detail as num_detail, dpt_name_ru as name_ru,
    dpt_name_en as name_en, dpt_name_de as name_de,
    dpt_image as image, dpt_presence as presence,
    trim(to_char(dpt_cost,'999 999 999')) as cost,
    dpt_languages as languages, apt_car_id_fk as car_id, car_synonym,
    apt_name_ru as autopart_ru, apt_name_en as autopart_en,
    apt_name_de as autopart_de
  from details_autoparts_tbl
  join autoparts_tbl on apt_id_pk = dpt_apt_id_fk
  join cars_tbl on car_id_pk = apt_car_id_fk
  where dpt_enabled = true and coalesce(dpt_presence,0) > 0
  order by apt_order, dpt_order;

alter view details_pricelist_vw owner to :admuser;
grant select on details_pricelist_vw to :mainuser;
/* }}} View: details_pricelist_vw */


/* View: details_autoparts_photos_count_vw {{{
 * Количество дополнительных фото в детали
 */
drop view if exists details_autoparts_photos_count_vw cascade;

create or replace view details_autoparts_photos_count_vw as
  select dap_dpt_id_fk, count(dap_id_pk) as count_photos
  from details_autoparts_photos_tbl
  group by dap_dpt_id_fk;

alter view details_autoparts_photos_count_vw owner to :admuser;
grant select on details_autoparts_photos_count_vw to :mainuser;
/* }}} View: details_autoparts_photos_count_vw */


/* View: details_autoparts_photos_vw {{{
 * Дополнительные фото автозапчасти
 */
drop view if exists details_autoparts_photos_vw cascade;

create or replace view details_autoparts_photos_vw as
  select dap_dpt_id_fk as detail_id, dap_name_ru as name_ru,
    dap_name_en as name_en, dap_name_de as name_de,
    dap_image as image, replace(dap_image, '-bg', '-sm') as image_small,
    dap_languages as languages
  from details_autoparts_photos_tbl
  where dap_enabled = true
  order by dap_order;

alter view details_autoparts_photos_vw owner to :admuser;
grant select on details_autoparts_photos_vw to :mainuser;
/* }}} View: details_autoparts_photos_vw */


/* View: coordinates_details_autopart_vw {{{
 * Координаты деталей автозапчастей на схеме
 */
drop view if exists coordinates_details_autopart_vw cascade;

create or replace view coordinates_details_autopart_vw as
  select car_synonym, apt_id_pk as autopart_id, dpt_id_pk as detail_id,
    dpt_num_detail as num_detail, dpt_status as status, dpt_presence as presence,
    replace(dpt_image, '-bg', '-mini') as image,
    case when dpt_discount is null then dpt_cost
      else detail_new_cost_fn(dpt_id_pk) end as cost_unformat,
    case when dpt_discount is null then
      replace(replace(trim(to_char(dpt_cost,'999 999 999.99')), '.', ','), ',00', '')
    else replace(replace(trim(to_char(detail_new_cost_fn(dpt_id_pk),'999 999 999.99')), '.', ','), ',00', '') end as cost,
    dpt_name_ru as name_ru, dpt_name_en as name_en, dpt_name_de as name_de,
    dpt_num_order as num, cda_top as _top, cda_left as _left
  from coord_detail_autoparts_tbl
  join details_autoparts_tbl on dpt_id_pk = cda_dpt_id_fk
  join autoparts_tbl on apt_id_pk = dpt_apt_id_fk
  join cars_tbl on car_id_pk = apt_car_id_fk
  where dpt_enabled = true
  order by cda_top;

alter view coordinates_details_autopart_vw owner to :admuser;
grant select on coordinates_details_autopart_vw to :mainuser;
/* }}} View: coordinates_details_autopart_vw */


/* View: car_docs_sitemap_vw {{{
 * Представление для построения sitemap.xml
 */
drop view if exists car_docs_sitemap_vw cascade;

create or replace view car_docs_sitemap_vw as
  select crd_synonym as id,
  crd_synonym as synonym, car_synonym,
  coalesce(coalesce(crd_name_ru, crd_name_en), crd_name_de) as name,
  crd_lastmod as lastmod, crd_id_pk as ordered_field,
  crd_car_id_fk as filter, crd_languages as languages
  from car_docs_tbl
  join cars_tbl on car_id_pk = crd_car_id_fk
  where crd_enabled = true and car_enabled = true;

alter view car_docs_sitemap_vw owner to :admuser;
grant select on car_docs_sitemap_vw to :mainuser;
/* }}} View: pages_sitemap_vw */


/* View: car_docs_vw {{{
 * Список документаций автомобиля
 */
drop view if exists car_docs_vw cascade;

create or replace view car_docs_vw as
  select crd_car_id_fk as car_id, crd_synonym as synonym, crd_name_ru as name_ru,
    crd_name_en as name_en, crd_name_de as name_de, crd_languages as languages,
    car_synonym
  from car_docs_tbl
  join cars_tbl on car_id_pk = crd_car_id_fk
  where crd_enabled = true;

alter view car_docs_vw owner to :admuser;
grant select on car_docs_vw to :mainuser;
/* }}} View: car_docs_vw */


/* View: car_doc_info_vw {{{
 * Список документаций автомобиля
 */
drop view if exists car_doc_info_vw cascade;

create or replace view car_doc_info_vw as
  select crd_car_id_fk as car_id, crd_synonym as synonym,
    crd_name_ru as name_ru, crd_name_en as name_en, crd_name_de as name_de,
    crd_title_ru as title_ru, crd_title_en as title_en, crd_title_de as title_de,
    crd_description_ru as description_ru, crd_description_en as description_en,
    crd_description_de as description_de,
    crd_keywords_ru as keywords_ru, crd_keywords_en as keywords_en,
    crd_keywords_de as keywords_de,
    crd_text_ru as text_ru, crd_text_en as text_en, crd_text_de as text_de,
    crd_languages as languages, crd_robots as robots
  from car_docs_tbl
  where crd_enabled = true;

alter view car_doc_info_vw owner to :admuser;
grant select on car_doc_info_vw to :mainuser;
/* }}} View: car_doc_info_vw */


/* View: details_autoparts_colors_vw {{{
 * Цвета детали
 */
drop view if exists details_autoparts_colors_vw cascade;

create or replace view details_autoparts_colors_vw as
  select dac_id_pk as id, dac_dpt_id_fk as detail_id, dac_name_ru as name_ru,
    dac_name_en as name_en, dac_name_de as name_de, dac_diff_cost as diff_cost,
    dac_available as available, dac_image as image,
    replace(dac_image, '-bg', '-md') as image_medium, dac_languages as languages
  from details_autoparts_colors_tbl
  where coalesce(dac_available, 0) > 0
  order by dac_order;

alter view details_autoparts_colors_vw owner to :admuser;
grant select on details_autoparts_colors_vw to :mainuser;
/* }}} View: details_autoparts_colors_vw */


/* View: details_colors_count_vw {{{
 * Количество дополнительных цветов детали
 */
drop view if exists details_colors_count_vw cascade;

create or replace view details_colors_count_vw as
  select dac_dpt_id_fk, count(dac_id_pk) as count_colors
  from details_autoparts_colors_tbl
  group by dac_dpt_id_fk;

alter view details_colors_count_vw owner to :admuser;
grant select on details_colors_count_vw to :mainuser;
/* }}} View: details_colors_count_vw */


/* View: details_autoparts_sizes_vw {{{
 * Размеры детали
 */
drop view if exists details_autoparts_sizes_vw cascade;

create or replace view details_autoparts_sizes_vw as
  select das_id_pk as id, das_dpt_id_fk as detail_id, das_name_ru as name_ru,
    das_name_en as name_en, das_name_de as name_de, das_diff_cost as diff_cost,
    das_available as available, das_image as image,
    replace(das_image, '-bg', '-md') as image_medium, das_languages as languages
  from details_autoparts_sizes_tbl
  where coalesce(das_available, 0) > 0
  order by das_order;

alter view details_autoparts_sizes_vw owner to :admuser;
grant select on details_autoparts_sizes_vw to :mainuser;
/* }}} View: details_autoparts_sizes_vw */


/* View: details_sizes_count_vw {{{
 * Количество дополнительных цветов детали
 */
drop view if exists details_sizes_count_vw cascade;

create or replace view details_sizes_count_vw as
  select das_dpt_id_fk, count(das_id_pk) as count_sizes
  from details_autoparts_sizes_tbl
  group by das_dpt_id_fk;

alter view details_sizes_count_vw owner to :admuser;
grant select on details_sizes_count_vw to :mainuser;
/* }}} View: details_sizes_count_vw */


/* View: details_colors_sizes_pair_count_vw {{{
 * Количество пар значений цает/размерв детали
 */
drop view if exists details_colors_sizes_pair_count_vw cascade;

create or replace view details_colors_sizes_pair_count_vw as
  select csp_dpt_id_fk, count(csp_id_pk) as count_pair_colors_sizes
  from details_autoparts_colors_sizes_pair_tbl
  group by csp_dpt_id_fk;

alter view details_colors_sizes_pair_count_vw owner to :admuser;
grant select on details_colors_sizes_pair_count_vw to :mainuser;
/* }}} View: details_colors_sizes_pair_count_vw */


/* View: sale_autoparts_mailer_vw {{{
 *
 */
drop view if exists sale_autoparts_mailer_vw cascade;

create or replace view sale_autoparts_mailer_vw as
  SELECT cars_tbl.car_synonym, autoparts_tbl.apt_id_pk AS autopart_id,
    cars_tbl.car_name_ru, cars_tbl.car_name_en, cars_tbl.car_name_de,
    autoparts_tbl.apt_name_ru AS autopart_name_ru,
    autoparts_tbl.apt_name_en AS autopart_name_en,
    autoparts_tbl.apt_name_de AS autopart_name_de,
    details_autoparts_tbl.dpt_id_pk AS detail_id,
    details_autoparts_tbl.dpt_image AS detail_image,
    details_autoparts_tbl.dpt_description_ru AS detail_description_ru,
    details_autoparts_tbl.dpt_description_en AS detail_description_en,
    details_autoparts_tbl.dpt_description_de AS detail_description_de,
    details_autoparts_tbl.dpt_num_detail AS detail_num,
    details_autoparts_tbl.dpt_last_update AS detail_last_update,
    details_autoparts_tbl.dpt_name_ru AS detail_name_ru,
    details_autoparts_tbl.dpt_name_en AS detail_name_en,
    details_autoparts_tbl.dpt_name_de AS detail_name_de,
    details_autoparts_tbl.dpt_status AS status,
    details_autoparts_tbl.dpt_presence AS presence,
    replace(replace(btrim(to_char(details_autoparts_tbl.dpt_cost::numeric,
      '999 999 999.99'::text)), '.'::text, ','::text), ',00'::text, ''::text) AS old_cost,
    details_autoparts_tbl.dpt_cost AS old_cost_unformat,
    details_autoparts_tbl.dpt_cost::numeric -
      detail_discount_diff_fn(details_autoparts_tbl.dpt_id_pk::integer) AS cost_unformat,
    replace(replace(btrim(to_char(details_autoparts_tbl.dpt_cost::numeric
      - detail_discount_diff_fn(details_autoparts_tbl.dpt_id_pk::integer), '999 999 999.99'::text)), '.'::text, ','::text), ',00'::text, ''::text) AS cost
   FROM details_autoparts_tbl
   JOIN autoparts_tbl ON autoparts_tbl.apt_id_pk::integer = details_autoparts_tbl.dpt_apt_id_fk::integer
   JOIN cars_tbl ON cars_tbl.car_id_pk::integer = autoparts_tbl.apt_car_id_fk::integer
  WHERE COALESCE(details_autoparts_tbl.dpt_discount::integer, 0) > 0
    AND COALESCE(details_autoparts_tbl.dpt_presence::integer, 0) > 0
    and dpt_last_update > ( SELECT max(mlr_datetime) AS max
      FROM mailers_tbl WHERE mlr_type = 'sale_autoparts');

alter view sale_autoparts_mailer_vw owner to :admuser;
grant select on sale_autoparts_mailer_vw to :mainuser;
/* }}} View: sale_autoparts_mailer_vw */

set client_min_messages = 'notice';
\echo 'Views by module Cars created.'
