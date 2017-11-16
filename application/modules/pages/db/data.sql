\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';



set client_min_messages = 'notice';
\echo 'Pages data insert complete!'
