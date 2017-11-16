\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* View: news_sitemap_vw {{{
 * Представление для построения sitemap.xml
 */
drop view if exists news_sitemap_vw cascade;

create or replace view news_sitemap_vw as
  select nw_id_pk as id,
  nw_synonym as synonym,
  coalesce(coalesce(nw_name_ru, nw_name_en), nw_name_de) as name,
  nw_last_update as lastmod,
  to_char(nw_datetime, 'YYDMMDDHH24')::INTEGER as ordered_field,
  nw_languages as languages
  from news_tbl
  where nw_enabled = true;

alter view news_sitemap_vw owner to :admuser;
grant select on news_sitemap_vw to :mainuser;
/* }}} View: pages_sitemap_vw */


/* View: last_news_vw {{{
 * Посление новости
 */
drop view if exists last_news_vw cascade;

create or replace view last_news_vw as
  select nw_image as image, nw_name_ru as name_ru, nw_name_en as name_en,
    nw_name_de as name_de, nw_synonym as synonym,
    to_char(nw_datetime, 'DD/MM/YYYY') as date, nw_languages as languages
  from news_tbl
  where nw_enabled = true and nw_datetime <= now()
  order by nw_datetime desc
  limit 4;

alter view last_news_vw owner to :admuser;
grant select on last_news_vw to :mainuser;
/* }}} View: last_news_vw */


/* View: news_categories_sitemap_vw {{{
 * Представление для построения sitemap.xml
 */
drop view if exists news_categories_sitemap_vw cascade;

create or replace view news_categories_sitemap_vw as
  select nwc_id_pk as id,
  nwc_synonym as synonym,
  coalesce(coalesce(nwc_name_ru, nwc_name_en), nwc_name_de) as name,
  nwc_lastmod as lastmod,
  nwc_order as ordered_field, nwc_languages as languages
  from news_categories_tbl
  where nwc_enabled = true;

alter view news_categories_sitemap_vw owner to :admuser;
grant select on news_categories_sitemap_vw to :mainuser;
/* }}} View: pages_sitemap_vw */


/* View: news_categories_cars_sitemap_vw {{{
 * Представление для построения sitemap.xml
 */
drop view if exists news_categories_cars_sitemap_vw cascade;

create or replace view news_categories_cars_sitemap_vw as
  select concat(car_synonym, nwc_synonym) as id,
  car_synonym as car_synonym,
  nwc_synonym as category_synonym,
  coalesce(nwc_name_ru, nwc_name_en, nwc_name_de) || ' ' || coalesce(car_name_ru, car_name_en, car_name_de) as name,
  nwc.nwc_lastmod as lastmod,
  nwc_order as ordered_field,
  nwc_languages as languages
  from news_tbl
  join cars_tbl on car_id_pk = nw_car_id_fk
  join news_categories_tbl as nwc on nwc_id_pk = nw_nwc_id_fk
  where nwc_enabled = true
  group by coalesce(nwc_name_ru, nwc_name_en, nwc_name_de) || ' ' || coalesce(car_name_ru, car_name_en, car_name_de),
  nwc_lastmod, car_synonym, nwc_synonym, nwc_order, nwc_languages;

alter view news_categories_cars_sitemap_vw owner to :admuser;
grant select on news_categories_cars_sitemap_vw to :mainuser;
/* }}} View: pages_sitemap_vw */


/* View: news_categories_tree_vw {{{
 * Список категорий новостей с автомобилями
 */
drop view if exists news_categories_tree_vw cascade;

create or replace view news_categories_tree_vw as
  select nwc_id_pk as id, nwc_synonym, nwc_name_ru as name_ru, nwc_name_en as name_en, nwc_name_de as name_de,
  nwc_title_ru as title_ru, nwc_title_en as title_en, nwc_title_de as title_de,
  nwc_description_ru as description_ru, nwc_description_en as description_en, nwc_description_de as description_de,
  nwc_keywords_ru as keywords_ru, nwc_keywords_en as keywords_en,
  nwc_keywords_de as keywords_de, count, cars, nwc_languages as languages,
  nwc_synonym as synonym, nwc_robots as robots
  from news_categories_tbl
  left join (select nw_nwc_id_fk, count(nw_id_pk) as count from news_tbl where nw_enabled=true group by nw_nwc_id_fk) cnt on nw_nwc_id_fk = nwc_id_pk

  left join
    (select nw_nwc_id_fk as category_id, array_to_json((array_agg(row_to_json(car)))) as cars from (
      select nw_nwc_id_fk, car_synonym, nwc_synonym as category_synonym,
      car_name_ru as name_ru, car_name_en as name_en, car_name_de as name_de, car_languages as languages
      from news_tbl join cars_tbl on nw_car_id_fk = car_id_pk
      left join news_categories_tbl on nw_nwc_id_fk = nwc_id_pk
      where car_enabled = true
      group by nw_nwc_id_fk, car_synonym, nwc_synonym, car_name_ru,
        car_name_en, car_name_de, car_languages, car_order
      order by car_order
    ) car
    group by nw_nwc_id_fk) car on category_id = nwc_id_pk
  order by nwc_order;


alter view news_categories_tree_vw owner to :admuser;
grant select on news_categories_tree_vw to :mainuser;
/* }}} View: news_categories_tree_vw */


/* View: news_list_vw {{{
 * Список новостей
 */
drop view if exists news_list_vw cascade;

create or replace view news_list_vw as
  select nw_id_pk as id, nw_name_ru as name_ru, nw_name_en as name_en,
    nw_name_de as name_de, nw_synonym as synonym,
    nw_description_ru as description_ru, nw_description_en as description_en,
    nw_description_de as description_de, nw_image as image,
    nw_text_ru as text_ru, nw_text_en as text_en, nw_text_de as text_de,
    to_char(nw_datetime, 'DD/MM/YYYY') as date,
    nw_datetime as date_unformat,
    nwc_synonym as category_synonym, car_synonym,
    nw_languages as languages, coalesce(count_comments, 0) as count_comments
  from news_tbl
  left join news_categories_tbl on nwc_id_pk = nw_nwc_id_fk
  left join cars_tbl on car_id_pk = nw_car_id_fk
  left join (select subject_id, count(id) as count_comments
    from comments_vw where type = 'news' group by subject_id) as cmt
      on nw_id_pk = cmt.subject_id
  where nw_enabled = true  and nw_datetime <= now()
  order by nw_datetime desc;

alter view news_list_vw owner to :admuser;
grant select on news_list_vw to :mainuser;
/* }}} View: news_list_vw */


/* View: news_info_vw {{{
 * Новость
 */
drop view if exists news_info_vw cascade;

create or replace view news_info_vw as
  select nw_id_pk as id, nw_synonym as synonym, nw_name_ru as name_ru,
    nw_name_en as name_en, nw_name_de as name_de,
    nw_title_ru as title_ru, nw_title_en as title_en, nw_title_de as title_de,
    nw_description_ru as description_ru, nw_description_en as description_en,
    nw_description_de as description_de, nw_keywords_ru as keywords_ru,
    nw_keywords_en as keywords_en, nw_keywords_de as keywords_de,
    nw_text_ru as text_ru, nw_text_en as text_en, nw_text_de as text_de,
    to_char(nw_datetime, 'DD/MM/YYYY') as date, nw_robots as robots,
    nw_languages as languages, coalesce(count_comments, 0) as count_comments
  from news_tbl
  left join (select subject_id, count(id) as count_comments
    from comments_vw where type = 'news' group by subject_id) as cmt
      on nw_id_pk = cmt.subject_id
  where nw_enabled = true  and nw_datetime <= now();

alter view news_info_vw owner to :admuser;
grant select on news_info_vw to :mainuser;
/* }}} View: news_info_vw */


set client_min_messages = 'notice';
\echo 'Views by module News created.'
