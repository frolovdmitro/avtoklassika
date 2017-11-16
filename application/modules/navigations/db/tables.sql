\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Table: navigations_tbl {{{
 * Навигационные меню
 */
drop table if exists navigations_tbl;

-- sequence: seq_nvg_id_pk {{{
drop sequence if exists seq_nvg_id_pk;
create sequence seq_nvg_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_nvg_id_pk owner to :admuser;
grant all on seq_nvg_id_pk to :mainuser;
-- }}}

create table navigations_tbl ( --{{{
  nvg_id_pk       dm_pk default nextval('seq_nvg_id_pk'::regclass),
  nvg_type        dm_smallenum_nn,
  nvg_name_ru     dm_text_medium,
  nvg_name_en     dm_text_medium,
  nvg_name_de     dm_text_medium,
  nvg_description dm_text_large,
  nvg_css_class   dm_text_small,
  nvg_enabled     dm_bool_true,

  constraint PK_NAVIGATIONS_TBL primary key (nvg_id_pk)
) tablespace :tablespace;
-- }}}

alter table navigations_tbl owner to :admuser;
grant select on navigations_tbl to :mainuser;
/* }}} Table: navigations_tbl */


/* Table: navitems_tbl {{{
 * Навигационные пункты меню
 */
drop table if exists navitems_tbl;

-- sequence: seq_nvi_id_pk {{{
drop sequence if exists seq_nvi_id_pk;
create sequence seq_nvi_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_nvi_id_pk owner to :admuser;
grant all on seq_nvi_id_pk to :mainuser;
-- }}}

create table navitems_tbl ( -- {{{
  nvi_id_pk         dm_pk default nextval('seq_nvi_id_pk'::regclass),
  nvi_rel           dm_text_medium,
  nvi_nvg_id_fk     dm_ref_nn,
  nvi_parent_id_fk  dm_ref,
  nvi_name          dm_text_medium,
  nvi_address       dm_url,
  nvi_css_class     dm_text_small,
  nvi_target_blank  dm_bool_false,
  nvi_lng_id_fk     dm_ref_nn,
  nvi_order         dm_order default currval('seq_nvi_id_pk'::regclass),
  nvi_visible       dm_bool_true,

  constraint PK_NAVITEMS_TBL primary key (nvi_id_pk)
) tablespace :tablespace;
-- }}}

alter table navitems_tbl owner to :admuser;
grant select on navitems_tbl to :mainuser;
/* }}} Table: navitems_tbl */


set client_min_messages = 'notice';
\echo 'Tables by module Navigations created.'
