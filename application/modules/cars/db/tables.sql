\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Table: cars_tbl {{{
 * Список автомобилей
 */
drop table if exists cars_tbl;

-- sequence: seq_car_id_pk {{{
drop sequence if exists seq_car_id_pk;
create sequence seq_car_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_car_id_pk owner to :admuser;
grant all on seq_car_id_pk to :mainuser;
-- }}}

-- sequence: seq_car_id_pk {{{
drop sequence if exists seq_car_order;
create sequence seq_car_order
  start with 1
  increment by 250
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_car_order owner to :admuser;
grant all on seq_car_order to :mainuser;
-- }}}

create table cars_tbl ( --{{{
  car_id_pk     dm_pk default nextval('seq_car_id_pk'::regclass),
  car_name_ru        dm_text_medium,
  car_name_en        dm_text_medium,
  car_name_de        dm_text_medium,
  car_synonym        dm_text_medium_nn,
  car_crt_id_fk      dm_ref,
  car_title_ru       dm_text_large,
  car_title_en       dm_text_large,
  car_title_de       dm_text_large,
  car_keywords_ru    dm_text_large,
  car_keywords_en    dm_text_large,
  car_keywords_de    dm_text_large,
  car_description_ru dm_text_large,
  car_description_en dm_text_large,
  car_description_de dm_text_large,
  car_image          dm_image,
  car_image_active   dm_image,
  car_seo_header_ru  dm_text_large,
  car_seo_header_en  dm_text_large,
  car_seo_header_de  dm_text_large,
  car_seo_image      dm_image,
  car_seo_text_ru    dm_text,
  car_seo_text_en    dm_text,
  car_seo_text_de    dm_text,
  car_languages dm_languages,
  car_order     dm_order default nextval('seq_car_order'::regclass),
  car_enabled   dm_bool_true,

  constraint PK_CARS_TBL primary key (car_id_pk)
) tablespace :tablespace;
-- }}}

alter table cars_tbl add column car_robots dm_text_small;
alter table cars_tbl add column car_docs_robots dm_text_small;
alter table cars_tbl add column car_price_robots dm_text_small;

alter table cars_tbl add column car_docs_title_ru dm_text_large;
alter table cars_tbl add column car_docs_title_en dm_text_large;
alter table cars_tbl add column car_docs_title_de dm_text_large;
alter table cars_tbl add column car_docs_keywords_ru dm_text_large;
alter table cars_tbl add column car_docs_keywords_en dm_text_large;
alter table cars_tbl add column car_docs_keywords_de dm_text_large;
alter table cars_tbl add column car_docs_description_ru dm_text_large;
alter table cars_tbl add column car_docs_description_en dm_text_large;
alter table cars_tbl add column car_docs_description_de dm_text_large;

alter table cars_tbl add column car_price_title_ru dm_text_large;
alter table cars_tbl add column car_price_title_en dm_text_large;
alter table cars_tbl add column car_price_title_de dm_text_large;
alter table cars_tbl add column car_price_keywords_ru dm_text_large;
alter table cars_tbl add column car_price_keywords_en dm_text_large;
alter table cars_tbl add column car_price_keywords_de dm_text_large;
alter table cars_tbl add column car_price_description_ru dm_text_large;
alter table cars_tbl add column car_price_description_en dm_text_large;
alter table cars_tbl add column car_price_description_de dm_text_large;

alter table cars_tbl add column car_last_update dm_datetime_now;

alter table cars_tbl owner to :admuser;
grant select on cars_tbl to :mainuser;
grant all on cars_tbl to :mainuser;
/* }}} Table: cars_tbl */


/* Table: car_types_tbl {{{
 * Группы автомобилей
 */
drop table if exists car_types_tbl;

-- sequence: seq_crt_id_pk {{{
drop sequence if exists seq_crt_id_pk;
create sequence seq_crt_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_crt_id_pk owner to :admuser;
grant all on seq_crt_id_pk to :mainuser;
-- }}}

-- sequence: seq_crt_id_pk {{{
drop sequence if exists seq_crt_order;
create sequence seq_crt_order
  start with 1
  increment by 250
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_crt_order owner to :admuser;
grant all on seq_crt_order to :mainuser;
-- }}}

create table car_types_tbl ( --{{{
  crt_id_pk     dm_pk default nextval('seq_crt_id_pk'::regclass),
  crt_name_ru   dm_text_small,
  crt_name_en   dm_text_small,
  crt_name_de   dm_text_small,
  crt_synonym   dm_synonym,
  crt_languages dm_languages,
  crt_order     dm_order default nextval('seq_crt_order'::regclass),
  crt_enabled   dm_bool_true,

  constraint PK_CAR_TYPES_TBL primary key (crt_id_pk)
) tablespace :tablespace;
-- }}}

alter table car_types_tbl owner to :admuser;
grant select on car_types_tbl to :mainuser;
/* }}} Table: car_types_tbl */


/* Table: autoparts_tbl {{{
 * Группы автозапчастей автомобилей
 */
drop table if exists autoparts_tbl;

-- sequence: seq_apt_id_pk {{{
drop sequence if exists seq_apt_id_pk;
create sequence seq_apt_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_apt_id_pk owner to :admuser;
grant all on seq_apt_id_pk to :mainuser;
-- }}}

-- sequence: seq_apt_id_pk {{{
drop sequence if exists seq_apt_order;
create sequence seq_apt_order
  start with 1
  increment by 250
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_apt_order owner to :admuser;
grant all on seq_apt_order to :mainuser;
-- }}}

create table autoparts_tbl ( --{{{
  apt_id_pk     dm_pk default nextval('seq_apt_id_pk'::regclass),
  apt_car_id_fk      dm_ref_nn,
  apt_parent_id_fk   dm_ref,
  apt_name_ru        dm_text_medium,
  apt_name_en        dm_text_medium,
  apt_name_de        dm_text_medium,
  apt_title_ru       dm_text_large,
  apt_title_en       dm_text_large,
  apt_title_de       dm_text_large,
  apt_keywords_ru    dm_text_large,
  apt_keywords_en    dm_text_large,
  apt_keywords_de    dm_text_large,
  apt_description_ru dm_text_large,
  apt_description_en dm_text_large,
  apt_description_de dm_text_large,
  apt_image          dm_image,
  apt_childs         dm_int_nn,
  apt_is_last        dm_bool_false,
  apt_count_details  dm_int,
  apt_seo_header_ru  dm_text_large,
  apt_seo_header_en  dm_text_large,
  apt_seo_header_de  dm_text_large,
  apt_seo_text_ru    dm_text,
  apt_seo_text_en    dm_text,
  apt_seo_text_de    dm_text,
  apt_languages dm_languages,
  apt_order     dm_order default nextval('seq_apt_order'::regclass),
  apt_enabled   dm_bool_true,

  constraint PK_AUTOPARTS_TBL primary key (apt_id_pk)
) tablespace :tablespace;
-- }}}

alter table autoparts_tbl add column apt_robots dm_text_small;
alter table autoparts_tbl add column apt_lastmod dm_datetime_now;

alter table autoparts_tbl owner to :admuser;
grant select on autoparts_tbl to :mainuser;
/* }}} Table: autoparts_tbl */


/* Table: details_autoparts_tbl {{{
 * Детали автомобилей
 */
drop table if exists details_autoparts_tbl cascade;

-- sequence: seq_dpt_id_pk {{{
drop sequence if exists seq_dpt_id_pk;
create sequence seq_dpt_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_dpt_id_pk owner to :admuser;
grant all on seq_dpt_id_pk to :mainuser;
-- }}}

-- sequence: seq_dpt_id_pk {{{
drop sequence if exists seq_dpt_order;
create sequence seq_dpt_order
  start with 1
  increment by 250
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_dpt_order owner to :admuser;
grant all on seq_dpt_order to :mainuser;
-- }}}

create table details_autoparts_tbl ( --{{{
  dpt_id_pk     dm_pk default nextval('seq_dpt_id_pk'::regclass),
  dpt_apt_id_fk      dm_ref,
  dpt_num_order      dm_text_medium,
  dpt_num_detail     dm_text_medium,
  dpt_name_ru        dm_text_large,
  dpt_name_en        dm_text_large,
  dpt_name_de        dm_text_large,
  dpt_title_ru       dm_text_large,
  dpt_title_en       dm_text_large,
  dpt_title_de       dm_text_large,
  dpt_description_ru dm_text_xlarge,
  dpt_description_en dm_text_xlarge,
  dpt_description_de dm_text_xlarge,
  dpt_keywords_ru    dm_text_xlarge,
  dpt_keywords_en    dm_text_xlarge,
  dpt_keywords_de    dm_text_xlarge,
  dpt_image          dm_image,
  dpt_presence       dm_int,
  dpt_cost           dm_cost,
  dpt_weight         dm_float,
  dpt_status         dm_enum,
  dpt_often_buy      dm_bool_false,
  dpt_last_update    dm_datetime_now,
  dpt_sale           dm_bool_false,
  dpt_top            dm_bool_false,
  dpt_languages dm_languages,
  dpt_enabled   dm_bool_true,
  dpt_order     dm_order default nextval('seq_dpt_order'::regclass),
  dpt_image_temp    dm_image,
  dpt_image_txt    dm_image,

  constraint PK_DETAILS_AUTOPARTS_TBL primary key (dpt_id_pk)
) tablespace :tablespace;
-- }}}

alter table details_autoparts_tbl add column dpt_discount dm_int;
alter table details_autoparts_tbl add column dpt_info_ru dm_text;
alter table details_autoparts_tbl add column dpt_info_en dm_text;
alter table details_autoparts_tbl add column dpt_info_de dm_text;
alter table details_autoparts_tbl add column dpt_robots dm_text_small;
alter table details_autoparts_tbl add column dpt_lastmod dm_datetime_now;

alter table details_autoparts_tbl owner to :admuser;
grant select on details_autoparts_tbl to :mainuser;
/* }}} Table: details_autoparts_tbl */


/* Table: details_addition_autoparts_tbl {{{
 * Ссылки на дополнительные разделы автозапчастей, где расположена деталь
 */
drop table if exists details_addition_autoparts_tbl;

-- sequence: seq_daa_id_pk {{{
drop sequence if exists seq_daa_id_pk;
create sequence seq_daa_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_daa_id_pk owner to :admuser;
grant all on seq_daa_id_pk to :mainuser;
-- }}}

create table details_addition_autoparts_tbl ( --{{{
  daa_id_pk     dm_pk default nextval('seq_daa_id_pk'::regclass),
  daa_dpt_id_fk dm_ref,
  daa_apt_id_fk dm_ref,
  daa_enabled   dm_bool_true,

  constraint PK_DETAILS_ADDITION_AUTOPARTS_TBL primary key (daa_id_pk)
) tablespace :tablespace;
-- }}}

alter table details_addition_autoparts_tbl owner to :admuser;
grant select on details_addition_autoparts_tbl to :mainuser;
/* }}} Table: details_addition_autoparts_tbl */


/* Table: details_autoparts_photos_tbl {{{
 * Фотографии автозапчастей
 */
drop table if exists details_autoparts_photos_tbl;

-- sequence: seq_dap_id_pk {{{
drop sequence if exists seq_dap_id_pk;
create sequence seq_dap_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_dap_id_pk owner to :admuser;
grant all on seq_dap_id_pk to :mainuser;
-- }}}

-- sequence: seq_dap_id_pk {{{
drop sequence if exists seq_dap_order;
create sequence seq_dap_order
  start with 1
  increment by 250
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_dap_order owner to :admuser;
grant all on seq_dap_order to :mainuser;
-- }}}

create table details_autoparts_photos_tbl ( --{{{
  dap_id_pk     dm_pk default nextval('seq_dap_id_pk'::regclass),
  dap_dpt_id_fk dm_ref_nn,
  dap_name_ru   dm_text_medium,
  dap_name_en   dm_text_medium,
  dap_name_de   dm_text_medium,
  dap_image     dm_image,
  dap_languages dm_languages,
  dap_order     dm_order default nextval('seq_dap_order'::regclass),
  dap_enabled   dm_bool_true,

  constraint PK_DETAILS_AUTOPARTS_PHOTOS_TBL primary key (dap_id_pk)
) tablespace :tablespace;
-- }}}

alter table details_autoparts_photos_tbl alter column dap_image type dm_image;
alter table details_autoparts_photos_tbl owner to :admuser;
grant select on details_autoparts_photos_tbl to :mainuser;
/* }}} Table: details_autoparts_photos_tbl */


/* Table: coord_detail_autoparts_tbl {{{
 * Координаты номеров на схеме детали
 */
drop table if exists coord_detail_autoparts_tbl;

-- sequence: seq_cda_id_pk {{{
drop sequence if exists seq_cda_id_pk;
create sequence seq_cda_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_cda_id_pk owner to :admuser;
grant all on seq_cda_id_pk to :mainuser;
-- }}}

create table coord_detail_autoparts_tbl ( --{{{
  cda_id_pk     dm_pk default nextval('seq_cda_id_pk'::regclass),
  cda_dpt_id_fk dm_ref_nn,
  cda_top       dm_int_nn,
  cda_left      dm_int_nn,

  constraint PK_COORD_DETAIL_AUTOPARTS_TBL primary key (cda_id_pk)
) tablespace :tablespace;
-- }}}

alter table coord_detail_autoparts_tbl owner to :admuser;
grant select on coord_detail_autoparts_tbl to :mainuser;
/* }}} Table: coord_detail_autoparts_tbl */


/* Table: car_docs_tbl {{{
 * Документация к автомобилям
 */
drop table if exists car_docs_tbl cascade;

-- sequence: seq_crd_id_pk {{{
drop sequence if exists seq_crd_id_pk;
create sequence seq_crd_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_crd_id_pk owner to :admuser;
grant all on seq_crd_id_pk to :mainuser;
-- }}}

create table car_docs_tbl ( --{{{
  crd_id_pk           dm_pk default nextval('seq_crd_id_pk'::regclass),
  crd_synonym         dm_synonym,
  crd_car_id_fk       dm_ref_nn,
  crd_name_ru         dm_text_medium,
  crd_name_en         dm_text_medium,
  crd_name_de         dm_text_medium,
  crd_title_ru        dm_text_medium,
  crd_title_en        dm_text_medium,
  crd_title_de        dm_text_medium,
  crd_keywords_ru     dm_text_xlarge,
  crd_keywords_en     dm_text_xlarge,
  crd_keywords_de     dm_text_xlarge,
  crd_description_ru  dm_text_xxlarge,
  crd_description_en  dm_text_xxlarge,
  crd_description_de  dm_text_xxlarge,
  crd_text_ru         dm_text,
  crd_text_en         dm_text,
  crd_text_de         dm_text,
  crd_languages dm_languages,
  crd_enabled   dm_bool_true,

  constraint PK_CAR_DOCS_TBL primary key (crd_id_pk)
) tablespace :tablespace;
-- }}}

alter table car_docs_tbl add column crd_robots dm_text_small;
alter table car_docs_tbl add column crd_lastmod dm_datetime_now;

alter table car_docs_tbl owner to :admuser;
grant select on car_docs_tbl to :mainuser;
/* }}} Table: car_docs_tbl */


/* Table: details_autoparts_colors_tbl {{{
 * Цвета автозапчасти
 */
drop table if exists details_autoparts_colors_tbl cascade;

-- sequence: seq_dac_id_pk {{{
drop sequence if exists seq_dac_id_pk cascade;
create sequence seq_dac_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_dac_id_pk owner to :admuser;
grant all on seq_dac_id_pk to :mainuser;
-- }}}

-- sequence: seq_dac_order {{{
drop sequence if exists seq_dac_order;
create sequence seq_dac_order
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_dac_order owner to :admuser;
grant all on seq_dac_order to :mainuser;
-- }}}

create table details_autoparts_colors_tbl ( --{{{
  dac_id_pk       dm_int_nn default nextval('seq_dac_id_pk'::regclass),
  dac_dpt_id_fk   dm_ref,
  dac_name_ru     dm_text_medium,
  dac_name_en     dm_text_medium,
  dac_name_de     dm_text_medium,
  dac_diff_cost    dm_cost,
  dac_available   dm_int,
  dac_image       dm_file,
  dac_languages   dm_languages,
  dac_order       dm_int_nn default nextval('seq_dac_order'::regclass),

  constraint PK_DETAILS_AUTOPARTS_COLORS_TBL primary key (dac_id_pk)
) tablespace :tablespace;
-- }}}

alter table details_autoparts_colors_tbl owner to :admuser;
grant select on details_autoparts_colors_tbl to :mainuser;
/* }}} Table: details_autoparts_colors_tbl */


/* Table: details_autoparts_sizes_tbl {{{
 * Размеры автозапчасти
 */
drop table if exists details_autoparts_sizes_tbl cascade;

-- sequence: seq_dac_id_pk {{{
drop sequence if exists seq_das_id_pk;
create sequence seq_das_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_das_id_pk owner to :admuser;
grant all on seq_das_id_pk to :mainuser;
-- }}}

-- sequence: seq_das_order {{{
drop sequence if exists seq_das_order;
create sequence seq_das_order
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_das_order owner to :admuser;
grant all on seq_das_order to :mainuser;
-- }}}

create table details_autoparts_sizes_tbl ( --{{{
  das_id_pk       dm_int_nn default nextval('seq_das_id_pk'::regclass),
  das_dpt_id_fk   dm_ref,
  das_name_ru     dm_text_medium,
  das_name_en     dm_text_medium,
  das_name_de     dm_text_medium,
  das_diff_cost   dm_cost,
  das_available   dm_int,
  das_image       dm_file,
  das_languages   dm_languages,
  das_order       dm_int_nn default nextval('seq_das_order'::regclass),

  constraint PK_DETAILS_AUTOPARTS_SIZES_TBL primary key (das_id_pk)
) tablespace :tablespace;
-- }}}

alter table details_autoparts_sizes_tbl owner to :admuser;
grant select on details_autoparts_sizes_tbl to :mainuser;
/* }}} Table: details_autoparts_sizes_tbl */


/* Table: details_autoparts_colors_sizes_pair_tbl {{{
 * Существующие пары цвет - размер
 */
drop table if exists details_autoparts_colors_sizes_pair_tbl cascade;

-- sequence: seq_csp_id_pk {{{
drop sequence if exists seq_csp_id_pk;
create sequence seq_csp_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_csp_id_pk owner to :admuser;
grant all on seq_csp_id_pk to :mainuser;
-- }}}

create table details_autoparts_colors_sizes_pair_tbl ( --{{{
  csp_id_pk       dm_int_nn default nextval('seq_csp_id_pk'::regclass),
  csp_dpt_id_fk   dm_ref,
  csp_dac_id_fk   dm_ref,
  csp_das_id_fk   dm_ref,
  csp_cost        dm_cost,
  csp_available   dm_int,

  constraint PK_DETAILS_AUTOPARTS_COLORS_SIZES_PAIR_TBL primary key (csp_id_pk)
) tablespace :tablespace;
-- }}}

alter table details_autoparts_colors_sizes_pair_tbl owner to :admuser;
grant select on details_autoparts_colors_sizes_pair_tbl to :mainuser;
/* }}} Table: details_autoparts_colors_sizes_pair_tbl */


set client_min_messages = 'notice';
\echo 'Tables by module Cars created.'
