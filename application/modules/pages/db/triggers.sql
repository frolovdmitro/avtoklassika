\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Trigger: pages_lastmod_trg {{{
 * Описание триггера
 */
drop function if exists pages_lastmod_trg_fn() cascade;
create or replace function pages_lastmod_trg_fn()
  returns trigger as
$BODY$
declare
  _id integer;
begin
  new.pg_lastmod = now();

  return new;
end
$BODY$
  language 'plpgsql' VOLATILE;

-- trigger: pages_lastmod_trg {{{
drop trigger if exists pages_lastmod_trg on pages_tbl;
create trigger pages_lastmod_trg
  before insert or update
on pages_tbl
for each row
  execute procedure pages_lastmod_trg_fn();
-- }}}
/* }}} Trigger: pages_lastmod_trg */



set client_min_messages = 'notice';
\echo 'Triggers by module Pages created.'
