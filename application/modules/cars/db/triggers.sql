\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Trigger: car_update_trg {{{
 * обновляем дату последнего редактирвоания
 */
drop function if exists car_update_trg_fn() cascade;
create or replace function car_update_trg_fn()
  returns trigger as
$BODY$
declare
  _id integer;
begin
  new.car_last_update = now();

  return new;
end
$BODY$
  language 'plpgsql' VOLATILE;

-- trigger: car_update_trg {{{
drop trigger if exists car_update_trg on cars_tbl;
create trigger car_update_trg
  before insert or update
on cars_tbl
for each row
  execute procedure car_update_trg_fn();
-- }}}
/* }}} Trigger: car_update_trg */


/* Trigger: autoparts_lastmod_trg {{{
 * Описание триггера
 */
drop function if exists autoparts_lastmod_trg_fn() cascade;
create or replace function autoparts_lastmod_trg_fn()
  returns trigger as
$BODY$
declare
  _id integer;
begin
  new.apt_lastmod = now();

  return new;
end
$BODY$
  language 'plpgsql' VOLATILE;

-- trigger: autoparts_lastmod_trg {{{
drop trigger if exists autoparts_lastmod_trg on autoparts_tbl;
create trigger autoparts_lastmod_trg
  before insert or update
on autoparts_tbl
for each row
  execute procedure autoparts_lastmod_trg_fn();
-- }}}
/* }}} Trigger: autoparts_lastmod_trg */


/* Trigger: details_autoparts_lastmod_trg {{{
 * Описание триггера
 */
drop function if exists details_autoparts_lastmod_trg_fn() cascade;
create or replace function details_autoparts_lastmod_trg_fn()
  returns trigger as
$BODY$
declare
  _id integer;
begin
  new.dpt_lastmod = now();

  return new;
end
$BODY$
  language 'plpgsql' VOLATILE;

-- trigger: details_autoparts_lastmod_trg {{{
drop trigger if exists details_autoparts_lastmod_trg on details_autoparts_tbl;
create trigger details_autoparts_lastmod_trg
  before insert or update or delete
on details_autoparts_tbl
for each row
  execute procedure details_autoparts_lastmod_trg_fn();
-- }}}
/* }}} Trigger: details_autoparts_lastmod_trg */


/* Trigger: car_docs_lastmod_trg {{{
 * Описание триггера
 */
drop function if exists car_docs_lastmod_trg_fn() cascade;
create or replace function car_docs_lastmod_trg_fn()
  returns trigger as
$BODY$
declare
  _id integer;
begin
  new.crd_lastmod = now();

  return new;
end
$BODY$
  language 'plpgsql' VOLATILE;

-- trigger: car_docs_lastmod_trg {{{
drop trigger if exists car_docs_lastmod_trg on car_docs_tbl;
create trigger car_docs_lastmod_trg
  before insert or update
on car_docs_tbl
for each row
  execute procedure car_docs_lastmod_trg_fn();
-- }}}
/* }}} Trigger: car_docs_lastmod_trg */


set client_min_messages = 'notice';
\echo 'Triggers by module Cars created.'
