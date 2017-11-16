\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Table: slider_tbl {{{
 * Слайдер на главной странице
 */
drop table if exists slider_tbl cascade;

-- sequence: seq_sld_id_pk {{{
drop sequence if exists seq_sld_id_pk;
create sequence seq_sld_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_sld_id_pk owner to :admuser;
grant all on seq_sld_id_pk to :mainuser;
-- }}}

-- sequence: seq_sld_id_pk {{{
drop sequence if exists seq_sld_order;
create sequence seq_sld_order
  start with 1
  increment by 250
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_sld_order owner to :admuser;
grant all on seq_sld_order to :mainuser;
-- }}}

create table slider_tbl ( --{{{
  sld_id_pk     dm_pk default nextval('seq_sld_id_pk'::regclass),
  sld_name_ru   dm_text_medium,
  sld_name_en   dm_text_medium,
  sld_name_de   dm_text_medium,
  sld_image_ru  dm_file,
  sld_image_en  dm_file,
  sld_image_de  dm_file,
  sld_link      dm_url,
  sld_target_blank  dm_bool_false,
  sld_languages dm_languages,
  sld_order     dm_order default nextval('seq_sld_order'::regclass),
  sld_enabled   dm_bool_true,

  constraint PK_SLIDER_TBL primary key (sld_id_pk)
) tablespace :tablespace;
-- }}}

alter table slider_tbl owner to :admuser;
grant select on slider_tbl to :mainuser;
/* }}} Table: slider_tbl */


set client_min_messages = 'notice';
\echo 'Tables by module Slider created.'
