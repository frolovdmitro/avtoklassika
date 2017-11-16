\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Table: orders_tbl {{{
 * Заказы
 */
drop table if exists orders_tbl;

-- sequence: seq_ord_id_pk {{{
drop sequence if exists seq_ord_id_pk;
create sequence seq_ord_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_ord_id_pk owner to :admuser;
grant all on seq_ord_id_pk to :mainuser;
-- }}}

create table orders_tbl ( --{{{
  ord_id_pk           dm_pk default nextval('seq_ord_id_pk'::regclass),
  ord_num             dm_order default currval('seq_ord_id_pk'::regclass),
  ord_usr_id_fk       dm_ref,
  ord_datetime        dm_datetime_now_nn,
  ord_user_name       dm_text_medium,
  ord_user_email      dm_email,
  ord_user_phones     dm_text_medium,
  ord_user_cnt_id_fk  dm_ref,
  ord_user_city       dm_text_medium,
  ord_user_index      dm_text_small,
  ord_user_street     dm_text_medium,
  ord_user_build      dm_text_mini,
  ord_user_flat       dm_text_mini,
  ord_status          dm_enum default 'wait_payment', --     proccess, wait_payment, paid, verify_payment, cancel, success
  ord_note            dm_text_xlarge,
  ord_sum             dm_cost,
  ord_discount        dm_int,
  ord_sum_discount    dm_float,
  ord_method_delivery dm_enum,         -- pickup, post, courier, newpost, ups, conductor
  ord_sum_delivery    dm_cost,
  ord_method_payment  dm_enum,         -- cash, visa, paypal, western, privat24, cod, contact, portmone, courier
  ord_dvm_id_fk       dm_ref,
  ord_pym_id_fk       dm_ref,
  ord_lng_id_fk       dm_ref_nn,
  ord_currency        dm_enum,         -- EUR, USD,      RUR,          UAH
  ord_rate            dm_float_nn,

  constraint PK_ORDERS_TBL primary key (ord_id_pk)
) tablespace :tablespace;
-- }}}

alter table orders_tbl owner to :admuser;
grant select, insert, update on orders_tbl to :mainuser;
/* }}} Table: orders_tbl */


/* Table: orders_details_tbl {{{
 * Товары в заказе
 */
drop table if exists orders_details_tbl;

-- sequence: seq_odd_id_pk {{{
drop sequence if exists seq_odd_id_pk;
create sequence seq_odd_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_odd_id_pk owner to :admuser;
grant all on seq_odd_id_pk to :mainuser;
-- }}}

create table orders_details_tbl ( --{{{
  odd_id_pk     dm_pk default nextval('seq_odd_id_pk'::regclass),
  odd_ord_id_fk dm_ref,
  odd_dpt_id_fk dm_ref,
  odd_count     dm_int,
  odd_cost      dm_cost,
  odd_das_id_fk dm_ref,
  odd_dac_id_fk dm_ref,

  constraint PK_ORDERS_DETAILS_TBL primary key (odd_id_pk)
) tablespace :tablespace;
-- }}}

alter table orders_details_tbl owner to :admuser;
grant select, insert on orders_details_tbl to :mainuser;

create index odd_dpt_id_fk_idx
  on orders_details_tbl (odd_dpt_id_fk);
/* }}} Table: orders_details_tbl */


set client_min_messages = 'notice';
\echo 'Tables by module Orders created.'
