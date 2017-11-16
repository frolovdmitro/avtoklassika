\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Trigger: update_main_image_trg {{{
 * Описание триггера
 */
drop function if exists update_main_image_trg_fn() cascade;
create or replace function update_main_image_trg_fn()
  returns trigger as
$BODY$
declare
  _id integer;
  _image varchar;
begin
  if (TG_OP = 'DELETE') then
    _id = old.ada_adv_id_fk;
  else
    _id = new.ada_adv_id_fk;
  end if;

  select ada_image from adverts_attachments_tbl
    where ada_adv_id_fk = _id into _image
    order by ada_id_pk limit 1;

  update adverts_tbl set adv_image = _image where adv_id_pk = _id;

  return new;
end
$BODY$
  language 'plpgsql' VOLATILE;

-- trigger: update_main_image_trg {{{
drop trigger if exists update_main_image_trg on adverts_attachments_tbl;
create trigger update_main_image_trg
  after insert or update or delete
on adverts_attachments_tbl
for each row
  execute procedure update_main_image_trg_fn();
-- }}}
/* }}} Trigger: update_main_image_trg */


set client_min_messages = 'notice';
\echo 'Triggers by module Adverts created.'
