\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Table: languages_tbl {{{
 * Языки
 */
drop table if exists languages_tbl;

-- sequence: seq_lng_id_pk {{{
drop sequence if exists seq_lng_id_pk;
create sequence seq_lng_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_lng_id_pk owner to :admuser;
grant all on seq_lng_id_pk to :mainuser;
-- }}}

create table languages_tbl ( --{{{
  lng_id_pk      dm_pk default nextval('seq_lng_id_pk'::regclass),
  lng_name       dm_text_mini_nn,
  lng_short_name dm_text_micro_nn,
  lng_synonym    dm_text_micro_nn,
  lng_default    dm_bool_false,
  lng_order      dm_order default currval('seq_lng_id_pk'::regclass),
  lng_enabled    dm_bool_true,

  constraint PK_LANGUAGES_TBL primary key (lng_id_pk)
) tablespace :tablespace;
-- }}}

alter table languages_tbl owner to :admuser;
grant select on languages_tbl to :mainuser;
/* }}} Table: languages_tbl */


/* Table: phones_tbl {{{
 * Список номеров телефонов интернет магазина
 */
drop table if exists phones_tbl;

-- sequence: seq_phn_id_pk {{{
drop sequence if exists seq_phn_id_pk;
create sequence seq_phn_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_phn_id_pk owner to :admuser;
grant all on seq_phn_id_pk to :mainuser;
-- }}}

create table phones_tbl ( --{{{
  phn_id_pk     dm_int_nn default nextval('seq_phn_id_pk'::regclass),
  phn_phones    dm_text_medium,
  phn_labor_hours dm_text_medium,
  phn_http_get  dm_text_small,
  phn_referel   dm_text_medium,
  phn_lng_id_fk dm_ref,
  phn_enabled   dm_bool_true,

  constraint PK_PHONES_TBL primary key (phn_id_pk)
) tablespace :tablespace;
-- }}}

alter table phones_tbl owner to :admuser;
grant select on phones_tbl to :mainuser;
/* }}} Table: phones_tbl */


/* Table: banners_places_tbl {{{
 * Баннерные площадки
 */
drop table if exists banners_places_tbl;

-- sequence: seq_phn_id_pk {{{
drop sequence if exists seq_bnp_id_pk;
create sequence seq_bnp_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_bnp_id_pk owner to :admavcom;
grant all on seq_bnp_id_pk to :mainuser;
-- }}}

create table banners_places_tbl ( --{{{
  bnp_id_pk   dm_int_nn default nextval('seq_bnp_id_pk'::regclass),
  bnp_type    dm_text_medium_nn,
  bnp_enabled dm_bool_true,

  constraint PK_BANNERS_PLACES_TBL primary key (bnp_id_pk)
) tablespace :tablespace;
-- }}}

ALTER TABLE banners_places_tbl OWNER TO :admuser;
GRANT SELECT ON banners_places_tbl TO :mainuser;
/* }}} Table: banners_places_tbl */


/* Table: banners_tbl {{{
 * Баннеры
 */
drop table if exists banners_tbl;

-- sequence: seq_bnr_id_pk {{{
drop sequence if exists seq_bnr_id_pk;
create sequence seq_bnr_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_bnr_id_pk owner to :admuser;
grant all on seq_bnr_id_pk to :mainuser;
-- }}}

-- sequence: seq_nwc_id_pk {{{
drop sequence if exists seq_bnr_order;
create sequence seq_bnr_order
  start with 1
  increment by 250
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_bnr_order owner to :admuser;
grant all on seq_bnr_order to :mainuser;
-- }}}

create table banners_tbl ( --{{{
  bnr_id_pk        dm_int_nn default nextval('seq_bnr_id_pk'::regclass),
  bnr_bnp_id_fk    dm_ref_nn,
  bnr_file_ru      dm_file,
  bnr_file_en      dm_file,
  bnr_file_de      dm_file,
  bnr_code_ru      dm_text_xlarge,
  bnr_code_en      dm_text_xlarge,
  bnr_code_de      dm_text_xlarge,
  bnr_url          dm_url,
  bnr_count_views  dm_int_nn,
  bnr_target_blank dm_bool_false,
  bnr_max_views    dm_int,
  bnr_start_date   dm_datetime,
  bnr_finish_date  dm_datetime,
  bnr_order        dm_int_nn default nextval('seq_bnr_order'::regclass),
  bnr_languages    dm_languages,
  bnr_enabled      dm_bool_true,

  constraint PK_BANNERS_TBL primary key (bnr_id_pk)
) tablespace :tablespace;
-- }}}

ALTER TABLE banners_tbl OWNER TO :admuser;
GRANT SELECT ON banners_tbl TO :mainuser;
/* }}} Table: banners_tbl */


/* Table: payment_methods_tbl {{{
 * Способы оплаты
 */
drop table if exists payment_methods_tbl;

-- sequence: seq_pym_id_pk {{{
drop sequence if exists seq_pym_id_pk;
create sequence seq_pym_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_pym_id_pk owner to :admuser;
grant all on seq_pym_id_pk to :mainuser;
-- }}}

-- sequence: seq_pym_id_pk {{{
drop sequence if exists seq_pym_order;
create sequence seq_pym_order
  start with 1
  increment by 250
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_pym_order owner to :admuser;
grant all on seq_pym_order to :mainuser;
-- }}}

create table payment_methods_tbl ( --{{{
  pym_id_pk     dm_pk default nextval('seq_pym_id_pk'::regclass),
  pym_name_ru      dm_text_medium,
  pym_name_en      dm_text_medium,
  pym_name_de      dm_text_medium,
  pym_description_ru dm_text_xxlarge,
  pym_description_en dm_text_xxlarge,
  pym_description_de dm_text_xxlarge,
  pym_type      dm_enum,
  pym_languages dm_languages,
  pym_order     dm_order default nextval('seq_pym_order'::regclass),
  pym_enabled   dm_bool_true,

  constraint PK_PAYMENT_METHODS_TBL primary key (pym_id_pk)
) tablespace :tablespace;
-- }}}

alter table payment_methods_tbl owner to :admuser;
grant select on payment_methods_tbl to :mainuser;
/* }}} Table: payment_methods_tbl */


/* Table: delivery_methods_tbl {{{
 * Способы доставки
 */
drop table if exists delivery_methods_tbl;

-- sequence: seq_dvm_id_pk {{{
drop sequence if exists seq_dvm_id_pk;
create sequence seq_dvm_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_dvm_id_pk owner to :admuser;
grant all on seq_dvm_id_pk to :mainuser;
-- }}}

-- sequence: seq_dvm_id_pk {{{
drop sequence if exists seq_dvm_order;
create sequence seq_dvm_order
  start with 1
  increment by 250
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_dvm_order owner to :admuser;
grant all on seq_dvm_order to :mainuser;
-- }}}

create table delivery_methods_tbl ( --{{{
  dvm_id_pk     dm_pk default nextval('seq_dvm_id_pk'::regclass),
  dvm_name_ru      dm_text_medium,
  dvm_name_en      dm_text_medium,
  dvm_name_de      dm_text_medium,
  dvm_description_ru dm_text_xxlarge,
  dvm_description_en dm_text_xxlarge,
  dvm_description_de dm_text_xxlarge,
  dvm_type         dm_enum,
  dvm_languages dm_languages,
  dvm_order     dm_order default nextval('seq_dvm_order'::regclass),
  dvm_enabled   dm_bool_true,

  constraint PK_DELIVERY_METHODS_TBL primary key (dvm_id_pk)
) tablespace :tablespace;
-- }}}

alter table delivery_methods_tbl owner to :admuser;
grant select on delivery_methods_tbl to :mainuser;
/* }}} Table: delivery_methods_tbl */


/* Table: countries_tbl {{{
 * Странцы
 */
drop table if exists countries_tbl;

create table countries_tbl ( --{{{
  cnt_id_pk          serial,
  cnt_name_ru        dm_text_small_nn,
  cnt_name_en        dm_text_small_nn,
  cnt_name_de        dm_text_small_nn,
  cnt_synonym        dm_text_small_nn,
  cnt_code           dm_text_micro_nn,
  cnt_onetime_tariff dm_float_nn,
  cnt_kg_tariff      dm_float_nn,
  cnt_languages      dm_languages,
  cnt_enabled        dm_bool_true,

  constraint PK_COUNTRIES_TBL primary key (cnt_id_pk)
) tablespace :tablespace;
-- }}}

alter table countries_tbl owner to :admuser;
grant select, insert, update, delete on countries_tbl to :mainuser;
/* }}} Table: countries_tbl */


/* Table: rates_tbl {{{
 * Курсы валют
 */
drop table if exists rates_tbl;

create table rates_tbl (
  rat_id_pk         serial,
  rat_default       dm_bool_false,
  rat_currency_ru   dm_text_mini,
  rat_currency_en   dm_text_mini,
  rat_currency_de   dm_text_mini,
  rat_short_name_ru dm_text_micro,
  rat_short_name_en dm_text_micro,
  rat_short_name_de dm_text_micro,
  rat_synonym       dm_text_mini,
  rat_value         dm_float_nn,
  rat_languages     dm_languages,
  rat_order         serial,
  rat_enabled       dm_bool_true,

  constraint PK_RATES_TBL primary key (rat_id_pk)
) tablespace :tablespace;

ALTER TABLE rates_tbl OWNER TO :admuser;
GRANT SELECT ON rates_tbl TO :mainuser;

CREATE INDEX rates_tbl_synonym_idx ON rates_tbl (rat_synonym)
  TABLESPACE :tablespace;
/* }}} Table: rates_tbl */


/* Table: sitemap_tbl {{{
 * Карта сайта
 */
drop table if exists sitemap_tbl;

-- sequence: seq_smp_id_pk {{{
drop sequence if exists seq_smp_id_pk;
create sequence seq_smp_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_smp_id_pk owner to :admuser;
grant all on seq_smp_id_pk to :mainuser;
-- }}}

create table sitemap_tbl ( --{{{
  smp_id_pk     dm_pk default nextval('seq_smp_id_pk'::regclass),
  smp_synonym   dm_text_small,
  smp_lastmod   dm_date,
  smp_changefreq dm_text_small,
  smp_priority  dm_float,
  smp_language  dm_ref,
  smp_enabled   dm_bool_true,

  constraint PK_SITEMAP_TBL primary key (smp_id_pk)
) tablespace :tablespace;
-- }}}

alter table sitemap_tbl alter column smp_priority type dm_float;
alter table sitemap_tbl owner to :admuser;
grant select on sitemap_tbl to :mainuser;
/* }}} Table: sitemap_tbl */


set client_min_messages = 'notice';
\echo 'Tables by module Directories created.'
