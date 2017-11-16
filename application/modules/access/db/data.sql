\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


truncate table administrators_tbl;

insert into administrators_tbl(adm_username, adm_password, adm_name)
values('y@uwinart.com', 'takeiteasy!', 'Yurii Khmelevskii');


set client_min_messages = 'notice';
\echo 'Access data insert complete!'
