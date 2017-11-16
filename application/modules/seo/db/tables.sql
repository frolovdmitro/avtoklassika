\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Table: seo_links_tbl {{{
 * Сео ссылки
 */
drop table if exists seo_links_tbl;

-- sequence: seq_sol_id_pk {{{
drop sequence if exists seq_sol_id_pk;
create sequence seq_sol_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_sol_id_pk owner to :admuser;
grant all on seq_sol_id_pk to :mainuser;
-- }}}

create table seo_links_tbl ( --{{{
  sol_id_pk       dm_pk default nextval('seq_sol_id_pk'::regclass),
  sol_link        dm_url_nn,
  sol_anchor      dm_text_medium_nn,
  sol_count       dm_int,
  sol_languages   dm_languages,
  sol_enabled     dm_bool_true,

  constraint PK_SEO_LINKS_TBL primary key (sol_id_pk)
) tablespace :tablespace;
-- }}}

alter table seo_links_tbl owner to :admuser;
grant select on seo_links_tbl to :mainuser;
/* }}} Table: seo_links_tbl */


/* Table: seo_categories_page_tbl {{{
 * Категории для сео ссылко
 */
drop table if exists seo_categories_page_tbl;

-- sequence: seq_scp_id_pk {{{
drop sequence if exists seq_scp_id_pk;
create sequence seq_scp_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_scp_id_pk owner to :admuser;
grant all on seq_scp_id_pk to :mainuser;
-- }}}

create table seo_categories_page_tbl ( --{{{
  scp_id_pk             dm_pk default nextval('seq_scp_id_pk'::regclass),
  scp_type              dm_enum_nn,
  scp_max_count_links   dm_int,
  scp_enabled           dm_bool_true,

  constraint PK_SEO_CATEGORIES_PAGE_TBL primary key (scp_id_pk)
) tablespace :tablespace;
-- }}}

alter table seo_categories_page_tbl owner to :admuser;
grant select on seo_categories_page_tbl to :mainuser;

create index seo_links_on_pages_tbl_slp_url_idx_scp_type_idx
  on seo_categories_page_tbl(scp_type desc nulls last)
  tablespace :tablespace;
/* }}} Table: seo_categories_page_tbl */


/* Table: seo_links_categories_page_tbl {{{
 * Ссылка сео ссылок с категориями страниц
 */
drop table if exists seo_links_categories_page_tbl;

-- sequence: seq_slcp_id_pk {{{
drop sequence if exists seq_slcp_id_pk;
create sequence seq_slcp_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_slcp_id_pk owner to :admuser;
grant all on seq_slcp_id_pk to :mainuser;
-- }}}

create table seo_links_categories_page_tbl ( --{{{
  slcp_id_pk      dm_pk default nextval('seq_slcp_id_pk'::regclass),
  slcp_sol_id_fk  dm_ref_nn,
  slcp_scp_id_fk  dm_ref_nn,
  slcp_enabled    dm_bool_true,

  constraint PK_SEO_LINKS_CATEGORIES_PAGE_TBL primary key (slcp_id_pk)
) tablespace :tablespace;
-- }}}

alter table seo_links_categories_page_tbl owner to :admuser;
grant select on seo_links_categories_page_tbl to :mainuser;
/* }}} Table: seo_links_categories_page_tbl */


/* Table: seo_links_on_pages_tbl {{{
 * Сео ссылки на страницах
 */
drop table if exists seo_links_on_pages_tbl;

-- sequence: seq_slp_id_pk {{{
drop sequence if exists seq_slp_id_pk;
create sequence seq_slp_id_pk
  start with 1
  increment by 1
  no maxvalue
  no minvalue
  cache 1;
alter sequence seq_slp_id_pk owner to :admuser;
grant all on seq_slp_id_pk to :mainuser;
-- }}}

create table seo_links_on_pages_tbl ( --{{{
  slp_id_pk      dm_pk default nextval('seq_slp_id_pk'::regclass),
  slp_url        dm_url_nn,
  slp_type_page  dm_enum,
  slp_sol_id_fk  dm_ref,
  slp_enabled    dm_bool_true,

  constraint PK_SEO_LINKS_ON_PAGES_TBL primary key (slp_id_pk)
) tablespace :tablespace;
-- }}}

alter table seo_links_on_pages_tbl owner to :admuser;
grant select, insert, update, delete on seo_links_on_pages_tbl to :mainuser;

create index seo_links_on_pages_tbl_slp_url_idx
  on seo_links_on_pages_tbl(slp_url desc nulls last)
  tablespace :tablespace;

create index seo_links_on_pages_tbl_slp_sol_id_fk_idx
  on seo_links_on_pages_tbl(slp_sol_id_fk, slp_url desc nulls last)
  tablespace :tablespace;
/* }}} Table: seo_links_on_pages_tbl */


set client_min_messages = 'notice';
\echo 'Tables by module Seo created.'
