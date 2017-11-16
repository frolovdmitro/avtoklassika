\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* View: not_registered_users_vw {{{
 *
 */
drop view if exists not_registered_users_vw cascade;

create or replace view not_registered_users_vw as
  select sq.ord_user_email, lng_short_name, (_user).ord_user_name,
    (_user).ord_user_phones, cnt_name_ru, (_user).ord_user_city,
    (_user).ord_user_index, (_user).ord_user_street, (_user).ord_user_build,
    (_user).ord_user_flat, last_date_order, sum_orders, count_success_orders,
    count_not_success_orders
  from (
    select distinct ord_user_email, email_user_info_fn(ord_user_email) as _user
    from orders_tbl
    where ord_user_email != '' and ord_usr_id_fk is null
  ) sq
  left join languages_tbl on lng_id_pk = (_user).ord_lng_id_fk
  left join countries_tbl on cnt_id_pk = (_user).ord_user_cnt_id_fk
  left join email_orders_total_vw eot on eot.ord_user_email = sq.ord_user_email
  left join last_date_order_by_email_vw lde on lde.ord_user_email = sq.ord_user_email
  ;

alter view not_registered_users_vw owner to :admuser;
grant select on not_registered_users_vw to :mainuser;
/* }}} View: not_registered_users_vw */


/* View: user_orders_vw {{{
 * Заказы пользовталея
 */
drop view if exists user_orders_vw cascade;

create or replace view user_orders_vw as
  select ord_num as num, ord_status as status,
    dvm_name_ru as method_delivery_ru, dvm_name_en as method_delivery_en,
    dvm_name_de as method_delivery_de,
    pym_name_ru as method_payment_ru, pym_name_en as method_payment_en,
    pym_name_de as method_payment_de,
    ord_method_payment as method_payment, ord_sum as sum_unformat,
    ord_rate as rate, rat_short_name_ru as currency_ru,
    rat_short_name_en as currency_en, rat_short_name_de as currency_de,
    trim(to_char(ord_sum*ord_rate,'999 999 999')) as sum,
    to_char(ord_datetime, 'DD/MM/YYYY<br>HH24:MI') as datetime,
    usr_email as user_email,
    md5(md5(ord_num::VARCHAR)||md5(ord_datetime::VARCHAR)) as key
  from orders_tbl
  left join users_tbl on usr_id_pk = ord_usr_id_fk
  left join delivery_methods_tbl on dvm_id_pk = ord_dvm_id_fk
  left join payment_methods_tbl on pym_id_pk = ord_pym_id_fk
  left join rates_tbl on rat_id_pk = ord_rat_id_fk
  order by ord_datetime desc;

alter view user_orders_vw owner to :admuser;
grant select on user_orders_vw to :mainuser;
/* }}} View: user_orders_vw */


set client_min_messages = 'notice';
\echo 'Views by module Users created.'
