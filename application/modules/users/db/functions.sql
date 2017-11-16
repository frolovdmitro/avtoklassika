\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Function: email_user_info_fn {{{
 *
 */
drop function if exists email_user_info_fn(email varchar) cascade;

create or replace function email_user_info_fn(email varchar)
returns table (
  ord_lng_id_fk int,
  ord_user_name varchar,
  ord_user_phones varchar,
  ord_user_cnt_id_fk int,
  ord_user_city varchar,
  ord_user_index varchar,
  ord_user_street varchar,
  ord_user_build varchar,
  ord_user_flat varchar
)  as
$BODY$
  select ord_lng_id_fk,
    ord_user_name,
    ord_user_phones,
    ord_user_cnt_id_fk,
    ord_user_city,
    ord_user_index,
    ord_user_street,
    ord_user_build,
    ord_user_flat
  from orders_tbl ord
  where lower(ord.ord_user_email) = lower($1)
  limit 1;
$BODY$
  language sql;
/* }}} Function: email_user_info_fn */


set client_min_messages = 'notice';
\echo 'Functions by module Users created.'
