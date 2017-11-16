\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* View: user_orders_total_vw {{{
 * 
 */
drop view if exists user_orders_total_vw cascade;

create or replace view user_orders_total_vw as
  select ord.ord_usr_id_fk
  , sum(ord_sum+ord_sum_delivery) sum_orders
  , coalesce(count_not_success_orders, 0) as count_not_success_orders
  , coalesce(count_success_orders, 0) as count_success_orders
  from orders_tbl ord
  left
  join (
    select ord_usr_id_fk
    , count(ord_id_pk) as count_not_success_orders
    from orders_tbl
    where ord_status != any('{paid, success}')
    group by ord_usr_id_fk
  ) not_success
  on not_success.ord_usr_id_fk = ord.ord_usr_id_fk
  left
  join (
    select ord_usr_id_fk
    , count(ord_id_pk) as count_success_orders
    from orders_tbl
    where ord_status =  any('{paid, success}')
    group by ord_usr_id_fk
  ) success
  on success.ord_usr_id_fk =  ord.ord_usr_id_fk
  where ord.ord_usr_id_fk is not null
  group by ord.ord_usr_id_fk, count_not_success_orders, count_success_orders
;

alter view user_orders_total_vw owner to :admuser;
grant select on user_orders_total_vw to :mainuser;
/* }}} View: user_orders_total_vw */


/* View: email_orders_total_vw {{{
 *
 */
drop view if exists email_orders_total_vw cascade;

create or replace view email_orders_total_vw as
  select ord.ord_user_email, sum(ord_sum+ord_sum_delivery) sum_orders,
    coalesce(count_not_success_orders, 0) as count_not_success_orders,
    coalesce(count_success_orders, 0) as count_success_orders
  from orders_tbl ord
  left join (
    select ord_user_email
    , count(ord_id_pk) as count_not_success_orders
    from orders_tbl
    where ord_status != any('{paid, success}')
    and ord_usr_id_fk is null
    group by ord_user_email
  ) not_success
  on not_success.ord_user_email =  ord.ord_user_email
  left join (
    select ord_user_email
    , count(ord_id_pk) as count_success_orders
    from orders_tbl
    where ord_status =  any('{paid, success}')
    and ord_usr_id_fk is null
    group by ord_user_email
  ) success
  on success.ord_user_email =  ord.ord_user_email
  where ord_usr_id_fk is null
  group by ord.ord_user_email, count_not_success_orders, count_success_orders
;

alter view email_orders_total_vw owner to :admuser;
grant select on email_orders_total_vw to :mainuser;
/* }}} View: email_orders_total_vw */


/* View: last_date_order_by_user_vw {{{
 *
 */
drop view if exists last_date_order_by_user_vw cascade;

create or replace view last_date_order_by_user_vw as
  select ord_usr_id_fk, max(ord_datetime) as last_date_order
  from orders_tbl
  group by ord_usr_id_fk;
  ;

alter view last_date_order_by_user_vw owner to :admuser;
grant select on last_date_order_by_user_vw to :mainuser;
/* }}} View: last_date_order_by_user_vw */


/* View: last_date_order_by_email_vw {{{
 *
 */
drop view if exists last_date_order_by_email_vw cascade;

create or replace view last_date_order_by_email_vw as
  select ord_user_email, max(ord_datetime) as last_date_order
  from orders_tbl
  where ord_usr_id_fk is null
  group by ord_user_email;
  ;

alter view last_date_order_by_email_vw owner to :admuser;
grant select on last_date_order_by_email_vw to :mainuser;
/* }}} View: last_date_order_by_email_vw */


set client_min_messages = 'notice';
\echo 'Views by module Orders created.'
