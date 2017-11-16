\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Trigger: set_user_password_trg {{{
 * Установка пароля пользователя
 */
drop function if exists set_user_password_trg_fn() cascade;
create or replace function set_user_password_trg_fn()
  returns trigger as
$BODY$
declare
  _id integer;
begin
  new.usr_salt = generate_string(12);
  new.usr_password = md5(md5(new.usr_password)||new.usr_salt);

  return new;
end
$BODY$
  language 'plpgsql' VOLATILE;

-- trigger: nameset_user_password_trg {{{
drop trigger if exists set_user_password_trg on users_tbl;
create trigger set_user_password_trg
  before insert or update
on users_tbl
for each row
  execute procedure set_user_password_trg_fn();
-- }}}
/* }}} Trigger: set_user_password_trg */


set client_min_messages = 'notice';
\echo 'Triggers by module Users created.'
