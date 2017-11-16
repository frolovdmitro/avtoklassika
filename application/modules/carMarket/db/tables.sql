\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Table: car_market_tbl {{{
 * Продажа авто
 */
drop table if exists car_market_tbl;

-- sequence: seq_cmk_id_pk {{{
drop sequence if exists seq_cmk_id_pk;
create sequence seq_cmk_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_cmk_id_pk owner to :admuser;
grant all on seq_cmk_id_pk to :mainuser;
-- }}}

create table car_market_tbl ( --{{{
  cmk_id_pk                dm_pk default nextval('seq_cmk_id_pk'::regclass),
  cmk_car_id_fk            dm_ref_nn,  --sell|buy
  cmk_h1_ru                dm_text_xlarge,
  cmk_h1_en                dm_text_xlarge,
  cmk_h1_de                dm_text_xlarge,
  cmk_title_ru             dm_text_xlarge,
  cmk_title_en             dm_text_xlarge,
  cmk_title_de             dm_text_xlarge,
  cmk_description_ru       dm_text_xxlarge,
  cmk_description_en       dm_text_xxlarge,
  cmk_description_de       dm_text_xxlarge,
  cmk_keywords_ru          dm_text_xxlarge,
  cmk_keywords_en          dm_text_xxlarge,
  cmk_keywords_de          dm_text_xxlarge,
  cmk_general_features_ru  dm_text,
  cmk_general_features_en  dm_text,
  cmk_general_features_de  dm_text,
  cmk_year                 dm_int,
  cmk_seria                dm_text_small,
  cmk_price_eur            dm_int,
  cmk_background           dm_image,
  cmk_image                dm_image,
  cmk_small_description_ru dm_text_xxlarge,
  cmk_small_description_en dm_text_xxlarge,
  cmk_small_description_de dm_text_xxlarge,
  cmk_is_original          dm_bool_false,
  cmk_date_create          dm_datetime_now,
  cmk_enabled              dm_bool_true,

  constraint PK_CAR_MARKET_TBL primary key (cmk_id_pk)
) tablespace :tablespace;
-- }}}

alter table car_market_tbl owner to :admuser;
grant select, insert, update on car_market_tbl to :mainuser;
/* }}} Table: car_market_tbl */


/* Table: car_market_photos_tbl {{{
 * Объявления
 */
drop table if exists car_market_photos_tbl;

-- sequence: seq_cmp_id_pk {{{
drop sequence if exists seq_cmp_id_pk;
create sequence seq_cmp_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_cmp_id_pk owner to :admuser;
grant all on seq_cmp_id_pk to :mainuser;
-- }}}

create table car_market_photos_tbl ( --{{{
  cmp_id_pk     dm_pk default nextval('seq_cmp_id_pk'::regclass),
  cmp_cmk_id_fk dm_ref_nn,  --sell|buy
  cmp_name_ru   dm_text_medium,
  cmp_name_en   dm_text_medium,
  cmp_name_de   dm_text_medium,
  cmp_image     dm_image,
  cmp_order     dm_int default currval('seq_cmp_id_pk'::regclass),
  cmp_enabled   dm_bool_true,

  constraint PK_CAR_MARKET_PHOTOS_TBL primary key (cmp_id_pk)
) tablespace :tablespace;
-- }}}

alter table car_market_photos_tbl owner to :admuser;
grant select, insert, update on car_market_photos_tbl to :mainuser;
/* }}} Table: car_market_photos_tbl */


/* Table: car_market_features_tbl {{{
 * Объявления
 */
drop table if exists car_market_features_tbl;

-- sequence: seq_cmf_id_pk {{{
drop sequence if exists seq_cmf_id_pk;
create sequence seq_cmf_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_cmf_id_pk owner to :admuser;
grant all on seq_cmf_id_pk to :mainuser;
-- }}}

create table car_market_features_tbl ( --{{{
  cmf_id_pk     dm_pk default nextval('seq_cmf_id_pk'::regclass),
  cmf_cmk_id_fk dm_ref_nn,
  cmf_header_ru dm_text_medium,
  cmf_header_en dm_text_medium,
  cmf_header_de dm_text_medium,
  cmf_text_ru   dm_text,
  cmf_text_en   dm_text,
  cmf_text_de   dm_text,
  cmf_icon      dm_smallenum,
  cmf_order     dm_int default currval('seq_cmf_id_pk'::regclass),
  cmf_enabled   dm_bool_true,

  constraint PK_CAR_MARKET_FEATURES_TBL primary key (cmf_id_pk)
) tablespace :tablespace;
-- }}}

alter table car_market_features_tbl owner to :admuser;
grant select, insert, update on car_market_features_tbl to :mainuser;
/* }}} Table: car_market_features_tbl */


/* Table: car_market_descriptions_tbl {{{
 * Объявления
 */
drop table if exists car_market_descriptions_tbl;

-- sequence: seq_cmd_id_pk {{{
drop sequence if exists seq_cmd_id_pk;
create sequence seq_cmd_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_cmd_id_pk owner to :admuser;
grant all on seq_cmd_id_pk to :mainuser;
-- }}}

create table car_market_descriptions_tbl ( --{{{
  cmd_id_pk      dm_pk default nextval('seq_cmd_id_pk'::regclass),
  cmd_cmk_id_fk  dm_ref_nn,
  cmd_header_ru  dm_text_medium,
  cmd_header_en  dm_text_medium,
  cmd_header_de  dm_text_medium,
  cmd_text_ru    dm_text,
  cmd_text_en    dm_text,
  cmd_text_de    dm_text,
  cmd_image      dm_image,
  cmd_youtube_id dm_text_medium,
  cmd_order      dm_int default currval('seq_cmd_id_pk'::regclass),
  cmd_enabled    dm_bool_true,

  constraint PK_CAR_MARKET_DESCRIPTIONS_TBL primary key (cmd_id_pk)
) tablespace :tablespace;
-- }}}

alter table car_market_descriptions_tbl owner to :admuser;
grant select, insert, update on car_market_descriptions_tbl to :mainuser;
/* }}} Table: car_market_descriptions_tbl */


set client_min_messages = 'notice';
\echo 'Tables by module CarMarket created.'
