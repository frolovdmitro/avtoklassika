\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';

/* View: pages_urls_vw {{{
 * Ссылки страниц сайта
 */
drop view if exists pages_urls_vw cascade;

create or replace view pages_urls_vw as
  select 'pages' as type, concat('/', pg_synonym, '/') as url,
    coalesce(pg_title_ru, pg_caption_ru) as namez_ru,
    coalesce(pg_title_en, pg_caption_en) as namez_en,
    coalesce(pg_title_de, pg_caption_de) as namez_de,
    pg_languages as languages
  from pages_tbl
  where pg_enabled = true
  union all
  select 'news' as type, concat('/news/', nw_synonym, '/') as url,
    coalesce(nw_title_ru, nw_name_ru) as namez_ru,
    coalesce(nw_title_en, nw_name_en) as namez_en,
    coalesce(nw_title_de, nw_name_de) as namez_de,
    nw_languages as languages
  from news_tbl
  where nw_enabled = true and nw_datetime <= now()
  union all
  select 'adverts' as type,
    concat('/ads/', adv_type, '/', adv_id_pk, '/') as url,
    coalesce(adv_title, adv_name) as namez_ru,
    coalesce(adv_title, adv_name) as namez_en,
    coalesce(adv_title, adv_name) as namez_de,
    '|ru|de|en|' as languages
  from adverts_tbl
  where adv_enabled = true
  union all
  select 'categories_catalogue' as type,
    concat('/car/', car_synonym, '/', apt_id_pk, '/') as url,
    apt_name_ru as namez_ru,
    apt_name_en as namez_en,
    apt_name_de as namez_de,
    apt_languages as languages
  from (
    select sq.*, count_details_in_autopart_fn(apt_id_pk, apt_car_id_fk, 'ru') as count
    from (
      WITH RECURSIVE tree AS
      (
        (
          select 1 as depth, array[apt_id_pk::int] as path,
            apt_id_pk, apt_name_ru, apt_name_en, apt_name_de, apt_car_id_fk, apt_is_last,
            apt_order, apt_parent_id_fk, car_synonym, apt_languages
          from autoparts_tbl apt
          join cars_tbl on car_id_pk = apt_car_id_fk
          where apt_parent_id_fk is null and apt_enabled = true
        )
        union all
        select tree.depth + 1, tree.path || apt.apt_order::int,
          apt.apt_id_pk, apt.apt_name_ru, apt.apt_name_en, apt.apt_name_de, apt.apt_car_id_fk, apt.apt_is_last,
          apt.apt_order, apt.apt_parent_id_fk, car.car_synonym, apt.apt_languages
        from tree
        join autoparts_tbl apt on apt.apt_parent_id_fk = tree.apt_id_pk and apt.apt_enabled = true
        join cars_tbl car on car_id_pk = apt.apt_car_id_fk
        where tree.depth < 3
      )
      select * from tree
      order by path, apt_order
    ) sq
  ) sqq where count > 0
  union all
  select 'products' as type,
    concat('/car/', car_synonym, '/', apt_id_pk, '/', dpt_id_pk, '/') as url,
    coalesce(dpt_title_ru, dpt_name_ru) as namez_ru,
    coalesce(dpt_title_en, dpt_name_en) as namez_en,
    coalesce(dpt_title_de, dpt_name_de) as namez_de,
    dpt_languages as languages
  from details_autoparts_tbl
  join autoparts_tbl on apt_id_pk = dpt_apt_id_fk and apt_enabled = true
  join cars_tbl on car_id_pk = apt_car_id_fk and car_enabled = true
  where dpt_enabled = true
  ;

alter view pages_urls_vw owner to :admuser;
grant select on pages_urls_vw to :mainuser;

create index pages_urls_idx
  on pages_tbl(('/' || pg_synonym::varchar || '/'))
  tablespace :tablespace;

/* }}} View: pages_urls_vw */


/* View: free_pages_vw {{{
 * Страницы в которых есть свободное место под ссылки
 */
drop view if exists free_pages_vw cascade;

create or replace view free_pages_vw as
  select url, namez_ru, namez_en, namez_de, languages,
    type, scp_max_count_links as max_count_links,
    count(slp_id_pk) as count_links,
    array_agg(sol_link::varchar) as links
  from pages_urls_vw
  left join seo_links_on_pages_tbl on slp_url = url
  left join seo_links_tbl on slp_sol_id_fk = sol_id_pk
  join seo_categories_page_tbl on type = scp_type
  group by url, namez_ru, namez_en, namez_de, languages,
    type, scp_max_count_links
  having count(slp_id_pk) != scp_max_count_links
  ;

alter view free_pages_vw owner to :admuser;
grant select on free_pages_vw to :mainuser;
/* }}} View: free_pages_vw */


/* View: unplaced_links_vw {{{
 * Неразмещенные ссылки
 */
drop view if exists unplaced_links_vw cascade;

create or replace view unplaced_links_vw as
  select sol_id_pk as id, sol_link as link, sol_anchor as anchor,
    sol_count - coalesce(count_links, 0) as count_unplaced,
    sol_languages as languages, array_agg(scp_type::varchar) as types
  from seo_links_tbl
  left join (
    select slp_sol_id_fk, count(slp_url) as count_links
    from seo_links_on_pages_tbl
    where slp_url in (select url from pages_urls_vw)
    group by slp_sol_id_fk
  ) slp on slp_sol_id_fk = sol_id_pk
  left join seo_links_categories_page_tbl on slcp_sol_id_fk = sol_id_pk
  left join seo_categories_page_tbl on scp_id_pk = slcp_scp_id_fk
  where coalesce(sol_count,0) > coalesce(count_links,0) and sol_enabled = true
  group by sol_id_pk, sol_link, sol_anchor, sol_count, sol_languages, count_links
  ;

alter view unplaced_links_vw owner to :admuser;
grant select on unplaced_links_vw to :mainuser;
/* }}} View: unplaced_links_vw */


/* View: links_by_page_vw {{{
 * Ссылки на странице
 */
drop view if exists links_by_page_vw cascade;

create or replace view links_by_page_vw as
  select slp_url as page_url, sol_link as link, sol_anchor as anchor
  from seo_links_on_pages_tbl
  left join seo_links_tbl on sol_id_pk = slp_sol_id_fk and sol_enabled = true
  and slp_enabled = true
  ;

alter view links_by_page_vw owner to :admuser;
grant select on links_by_page_vw to :mainuser;
/* }}} View: links_by_page_vw */


set client_min_messages = 'notice';
\echo 'Views by module Seo created.'
