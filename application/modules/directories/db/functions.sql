\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Function: sitemap_pages_fn {{{
 * Возвращает страницы указанной группы с ее значениями
 */
drop function if exists sitemap_pages_fn(_synonym varchar) cascade;

drop type sitemap_page_type cascade;
create type sitemap_page_type as (id varchar, synonym varchar, enabled boolean,
  route varchar, name varchar, lastmod timestamp, changefreq varchar,
  priority float, is_last boolean, order_num integer, languages varchar);

create or replace function sitemap_pages_fn(_synonym varchar default '')
  returns setof sitemap_page_type as
$BODY$
declare
  _path       varchar;
  _path_root  varchar;
  _variable   varchar;
  _field      varchar;
  _join_sql   varchar;

  _id         integer;
  _name       varchar;
  _root_route varchar;
  _route      varchar;
  _table_name varchar;

  _sql varchar;

  _records sitemap_page_type;
begin
  if (_synonym = '') then
    return query
      select
        scg.id::varchar as id,
        synonym::varchar,
        coalesce(smp_enabled::boolean, true) as enabled,
        (case when root_route is not null and route is not null then null else root_route end)::varchar as route, name::varchar,
        smp_lastmod::timestamp as lastmod,
        smp_changefreq::varchar as changefreq,
        smp_priority::float as priority,
        is_last::boolean as is_last,
        scg.order::integer, '|ru|en|de|'::varchar as languages
      from sitemap_categories_vw scg
      left join sitemap_tbl on smp_synonym = synonym
      where scg.parent is null;

      return;
  end if;

  select name, root_route, route, table_name
  from sitemap_categories_vw
  where synonym = _synonym
  into _name, _root_route, _route, _table_name;

  _path = _route;
  for _variable in select (regexp_matches(_path, '{.+?}', 'g'))[1]
  loop
    _field = replace(_variable, '{', '');
    _field = replace(_field, '}', '');

    _path = replace(_path, _variable, ''' || ' || 'tbl.'||_field || ' || ''');
  end loop;

  _path_root = _root_route;
  for _variable in select (regexp_matches(_path_root, '{.+?}', 'g'))[1]
  loop
    _field = replace(_variable, '{', '');
    _field = replace(_field, '}', '');

    _path_root = replace(_path_root, _variable, ''' || ' || _field || ' || ''');
  end loop;

  _sql = '';

  _join_sql = '';
  if ( position('=' in _synonym) != 0) then
    _id = substring(_synonym from position('=' in _synonym)+1);
    _join_sql = 'left join ' || _table_name || ' tbl on tbl.filter = ' || _id;
  end if;

  if (_root_route is not null) then
  _sql = _sql || '
    select * from (select
      concat($1, ''_0'')::varchar as id,
      concat($1, ''_0'')::varchar as synonym,
      (case
        when prn.smp_enabled = false then false
      else
        coalesce(crn.smp_enabled, true) end)::boolean as enabled,
      ('''|| coalesce(_path_root,'') || ''')::varchar as route, ''<b>' || _name || '</b>''::varchar as name,
      coalesce(crn.smp_lastmod, prn.smp_lastmod)::timestamp as lastmod,
      coalesce(crn.smp_changefreq, prn.smp_changefreq)::varchar as changefreq,
      coalesce(crn.smp_priority, prn.smp_priority)::float as priority,
      true as is_last,
      0::integer as ordered_field, ''|ru|en|de|''::varchar
    from sitemap_categories_vw scg
    ' || _join_sql || '
    left join sitemap_tbl crn on crn.smp_synonym = concat($1, ''_0'')
    left join sitemap_tbl prn on prn.smp_synonym = $1
    where scg.synonym = $1 limit 1) t1 union all';
  end if;

  _sql = _sql || '
    select
      concat($1, ''_'', tbl.id)::varchar as id,
      concat($1, ''_'', tbl.id)::varchar as synonym,
      (case
        when prn.smp_enabled = false then false
      else
        coalesce(crn.smp_enabled, true) end)::boolean as enabled,
      ('''|| coalesce(_path,'') || ''')::varchar as route,
      tbl.name::varchar,
      coalesce(crn.smp_lastmod, lastmod)::timestamp as lastmod,
      coalesce(crn.smp_changefreq, prn.smp_changefreq)::varchar as changefreq,
      coalesce(crn.smp_priority, prn.smp_priority)::float as priority,
      is_last::boolean as is_last,
      ordered_field::integer, languages::varchar
    from ' || _table_name || ' tbl
    left join sitemap_categories_vw scg on scg.parent = $1
    left join sitemap_tbl crn on crn.smp_synonym = concat($1, ''_'', tbl.id)
    left join sitemap_tbl prn on prn.smp_synonym = $1';

  if ( position('=' in _synonym) != 0) then
    _id = substring(_synonym from position('=' in _synonym)+1);

    _sql = _sql || ' where filter = ' || _id;
  end if;

  return query execute _sql using _synonym;
end;
$BODY$
  language 'plpgsql';
/* }}} Function: sitemap_pages_fn */


/* Function: sitemap_all_pages_fn {{{
 * все страницы для sitemap.xml
 */
drop function if exists sitemap_all_pages_fn() cascade;

create or replace function sitemap_all_pages_fn()
  returns setof sitemap_page_type as
$BODY$
declare
  r record;
begin
  for r in select synonym, is_last from sitemap_pages_fn() loop
    if (r.is_last = false) then
      raise notice '%', r.synonym;
      return query select * from sitemap_pages_fn(r.synonym);
    end if;
  end loop;

  return;
end;
$BODY$
  language 'plpgsql';
/* }}} Function: sitemap_all_pages_fn */


set client_min_messages = 'notice';
\echo 'Functions by module Directories created.'
