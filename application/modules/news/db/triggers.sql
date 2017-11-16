\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Trigger: news_last_update_trg {{{
 * Описание триггера
 */
drop function if exists news_last_update_trg_fn() cascade;
create or replace function news_last_update_trg_fn()
  returns trigger as
$BODY$
declare
  _id integer;
begin
  new.nw_last_update = now();

  return new;
end
$BODY$
  language 'plpgsql' VOLATILE;

-- trigger: news_last_update_trg {{{
drop trigger if exists news_last_update_trg on news_tbl;
create trigger news_last_update_trg
  before insert or update
on news_tbl
for each row
  execute procedure news_last_update_trg_fn();
-- }}}
/* }}} Trigger: news_last_update_trg */


/* Trigger: news_categories_last_update_trg {{{
 * Описание триггера
 */
drop function if exists news_categories_last_update_trg_fn() cascade;
create or replace function news_categories_last_update_trg_fn()
  returns trigger as
$BODY$
declare
  _id integer;
begin
  new.nwc_lastmod = now();

  return new;
end
$BODY$
  language 'plpgsql' VOLATILE;

-- trigger: news_categories_last_update_trg {{{
drop trigger if exists news_categories_last_update_trg on news_categories_tbl;
create trigger news_categories_last_update_trg
  before insert or update
on news_categories_tbl
for each row
  execute procedure news_categories_last_update_trg_fn();
-- }}}
/* }}} Trigger: news_categories_last_update_trg */


set client_min_messages = 'notice';
\echo 'Triggers by module News created.'
