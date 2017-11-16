\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';

truncate table languages_tbl;

insert into languages_tbl(lng_name, lng_short_name, lng_synonym, lng_default)
values('ru', 'ru', 'ru', true),
values('en', 'en', 'en', false),
values('de', 'de', 'de', false);


set client_min_messages = 'notice';
\echo 'Directories data insert complete!'
