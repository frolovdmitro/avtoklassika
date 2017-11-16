\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Function: links_categories_string_fn {{{
 * Список категорий к ссылке через запятую
 */
drop function if exists links_categories_string_fn(sol_id int) cascade;

create or replace function links_categories_string_fn(sol_id int)
  returns text as
$BODY$
  select array_to_string(array_agg(scp_type::varchar), ', ')
  from seo_links_categories_page_tbl
  left join seo_categories_page_tbl on slcp_scp_id_fk = scp_id_pk
  where slcp_sol_id_fk = $1
$BODY$
  language sql;
/* }}} Function: links_categories_string_fn */


set client_min_messages = 'notice';
\echo 'Functions by module Seo created.'
