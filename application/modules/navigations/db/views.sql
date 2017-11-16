\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* View: navitems_vw {{{
 * Пункты навигационных меню
 */
drop view if exists navitems_vw cascade;

create or replace view navitems_vw as
  select nvi_id_pk as id
    , nvg_id_pk as nav_id
    , nvg_type as nav_type
    , nvg_name_ru as nav_name_ru
    , nvg_name_en as nav_name_en
    , nvg_name_de as nav_name_de
    , nvg_description as nav_description
    , nvg_css_class as nav_css_class
    , nvg_enabled as nav_enabled
    , nvi_parent_id_fk as parent_id
    , nvi_name as name
    , nvi_address as address
    , nvi_css_class as css_class
    , nvi_target_blank as target_blank
    , nvi_order as order
    , nvi_visible as visible
    , lng_id_pk as lang_id
    , lng_name as lang_name
    , lng_short_name as short_name
    , lng_synonym as lang_synonym
    , lng_default as lang_default
    , lng_order as lang_order
    , lng_enabled as lang_enabled
  from navitems_tbl
  join navigations_tbl on nvg_id_pk = nvi_nvg_id_fk
  join languages_tbl on lng_id_pk = nvi_lng_id_fk
;

alter view navitems_vw owner to :admuser;
grant select on navitems_vw to :mainuser;
/* }}} View: navitems_vw */


set client_min_messages = 'notice';
\echo 'Views by module Navigations created.'
