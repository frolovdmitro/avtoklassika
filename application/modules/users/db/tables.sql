\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Table: users_tbl {{{
 * Пользователи
 */
drop table if exists users_tbl;

-- sequence: seq_usr_id_pk {{{
drop sequence if exists seq_usr_id_pk;
create sequence seq_usr_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_usr_id_pk owner to :admuser;
grant all on seq_usr_id_pk to :mainuser;
-- }}}

create table users_tbl ( --{{{
  usr_id_pk             dm_pk default nextval('seq_usr_id_pk'::regclass),
  usr_email             dm_email_nn,
  usr_password          dm_password,
  usr_salt              dm_password,
  usr_name              dm_text_medium,
  usr_phones            dm_text_medium,
  usr_icq               dm_text_small,
  usr_skype             dm_text_small,
  usr_sex               dm_enum,         --MALE, FEMALE
  usr_birthday          dm_date,
  usr_cnt_id_fk         dm_ref,
  usr_city              dm_text_small,
  usr_index             dm_text_mini,
  usr_street            dm_text_medium,
  usr_build             dm_text_mini,
  usr_flat              dm_text_micro,
  usr_discount          dm_int,
  usr_spokesman         dm_bool_false,
  usr_lng_id_fk         dm_ref_nn,
  usr_date_registration dm_datetime_now,
  usr_date_last_visit   dm_datetime,
  usr_enabled           dm_bool_true,

  constraint PK_USERS_TBL primary key (usr_id_pk)
) tablespace :tablespace;
-- }}}

alter table users_tbl owner to :admuser;
grant select, insert, update on users_tbl to :mainuser;

create index ord_user_email_lower_idx
  on orders_tbl (lower(ord_user_email));
/* }}} Table: users_tbl */


/* Table: users_subscribes_tbl {{{
 *
 */
drop table if exists users_subscribes_tbl;

-- sequence: seq_usb_id_pk {{{
drop sequence if exists seq_usb_id_pk;
create sequence seq_usb_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_usb_id_pk owner to :admuser;
grant all on seq_usb_id_pk to :mainuser;
-- }}}

create table users_subscribes_tbl ( --{{{
  usb_id_pk       dm_pk default nextval('seq_usb_id_pk'::regclass),
  usb_usr_id_fk   dm_ref_nn,
  usb_car_id_fk   dm_ref_nn,

  constraint PK_USERS_SUBSCRIBES_TBL primary key (usb_id_pk)
) tablespace :tablespace;
-- }}}

alter table users_subscribes_tbl owner to :admuser;
grant select, insert, update on users_subscribes_tbl to :mainuser;
/* }}} Table: users_subscribes_tbl */


/* Table: users_simple_subscribes_tbl {{{
 * Подписанные пользователи
 */
drop table if exists users_simple_subscribes_tbl;

-- sequence: seq_uss_id_pk {{{
drop sequence if exists seq_uss_id_pk;
create sequence seq_uss_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_uss_id_pk owner to :admuser;
grant all on seq_uss_id_pk to :mainuser;
-- }}}

create table users_simple_subscribes_tbl ( --{{{
  uss_id_pk     dm_pk default nextval('seq_uss_id_pk'::regclass),
  uss_email     dm_email,
  uss_name      dm_text_medium,
  uss_date      dm_datetime_now,
  uss_lng_id_fk dm_ref,
  uss_enabled   dm_bool_true,

  constraint PK_USERS_SIMPLE_SUBSCRIBES_TBL primary key (uss_id_pk)
) tablespace :tablespace;
-- }}}

alter table users_simple_subscribes_tbl add column uss_unsubscribe dm_bool_false;
alter table users_simple_subscribes_tbl owner to :admuser;
grant select, insert, update on users_simple_subscribes_tbl to :mainuser;
/* }}} Table: users_subscribes_tbl */


/* Table: users_reviews_tbl {{{
 * Отзывы пользователей о магазине
 */
drop table if exists users_reviews_tbl;

-- sequence: seq_urv_id_pk {{{
drop sequence if exists seq_urv_id_pk;
create sequence seq_urv_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_urv_id_pk owner to :admuser;
grant all on seq_urv_id_pk to :mainuser;
-- }}}

create table users_reviews_tbl ( --{{{
  urv_id_pk     dm_pk default nextval('seq_urv_id_pk'::regclass),
  urv_name      dm_text_xlarge,
  urv_email     dm_email,
  urv_text      dm_text,
  urv_quality_service   dm_text_medium,
  urv_usability_site    dm_text_medium,
  urv_quality_goods     dm_text_medium,
  urv_shipping          dm_text_medium,
  urv_datetime  dm_datetime_now,
  urv_enabled   dm_bool_true,

  constraint PK_USERS_REVIEWS_TBL primary key (urv_id_pk)
) tablespace :tablespace;
-- }}}

alter table users_reviews_tbl owner to :admuser;
grant select, insert on users_reviews_tbl to :mainuser;
/* }}} Table: users_reviews_tbl */


set client_min_messages = 'notice';
\echo 'Tables by module Users created.'
