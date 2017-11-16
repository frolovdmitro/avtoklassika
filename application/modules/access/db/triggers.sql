\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Trigger: set_administrator_password {{{
 * Триггер шифрует пароль создаваемого или редактируемого администратора
 */
drop function if exists set_administrator_password() cascade;

create or replace function set_administrator_password()
  returns trigger AS
$BODY$
begin
    new.adm_salt = generate_string(12);
    new.adm_password = md5(md5(new.adm_password)||new.adm_salt);

    return new;
end
$BODY$
  language 'plpgsql' VOLATILE;

-- trigger: set_administrator_password {{{
drop trigger if exists set_administrator_password on administrators_tbl;
create trigger set_administrator_password
  before insert or update
ON administrators_tbl
for each row
  execute procedure set_administrator_password();
-- }}}
/* }}} Trigger: set_administrator_password */


set client_min_messages = 'notice';
\echo 'Triggers by module Access created.'
