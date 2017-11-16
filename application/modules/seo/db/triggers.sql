\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Trigger: update_seo_links_trg {{{
 * Описание триггера
 */
drop function if exists update_seo_links_trg_fn() cascade;
create or replace function update_seo_links_trg_fn()
  returns trigger as
$BODY$
declare
  _count_links integer;
  _diff_links integer;
begin
  if TG_OP = 'DELETE' then
    delete from seo_links_on_pages_tbl where slp_sol_id_fk = old.sol_id_pk;
  END IF;

  if TG_OP = 'UPDATE' and new.sol_count < old.sol_count then
    select count(slp_id_pk) from seo_links_on_pages_tbl
    where slp_sol_id_fk = old.sol_id_pk into _count_links;

    _diff_links = _count_links - new.sol_count;
    if _diff_links > 0 then
      delete from seo_links_on_pages_tbl where slp_id_pk in
        (select slp_id_pk from seo_links_on_pages_tbl
          where slp_sol_id_fk = old.sol_id_pk limit _diff_links
        );
    end if;
  END IF;

  return new;
end
$BODY$
  language 'plpgsql' VOLATILE;

-- trigger: update_seo_links_trg {{{
drop trigger if exists update_seo_links_trg on seo_links_tbl;
create trigger update_seo_links_trg
  after update or delete
on seo_links_tbl
for each row
  execute procedure update_seo_links_trg_fn();
-- }}}
/* }}} Trigger: update_seo_links_trg */


set client_min_messages = 'notice';
\echo 'Triggers by module Seo created.'
