\set variables_file :DIR '/../../../../database/variables.sql'
\i :variables_file
\c :db;
set client_min_messages = 'warning';


/* Function: count_details_in_autopart_fn {{{
 * Кол-во деталей в наличии в указанной группе автозапчатей и ее подгруппах
 */
drop function if exists count_details_in_autopart_fn(_autopart_id int, _car_id int, _lang varchar) cascade;

create or replace function count_details_in_autopart_fn(_autopart_id int, _car_id int, _lang varchar)
  returns bigint as
$BODY$
  select count(dpt_id_pk) as count
  from
  (
    select dpt_id_pk, dpt_apt_id_fk, dpt_languages, dpt_enabled, apt_car_id_fk,
      dpt_presence
    from details_autoparts_tbl
    join autoparts_tbl on apt_id_pk = dpt_apt_id_fk
    union all
    select dpt_id_pk, apt_id_pk, dpt_languages, dpt_enabled, apt_car_id_fk,
      dpt_presence
    from details_autoparts_tbl
    join details_addition_autoparts_tbl on dpt_id_pk = daa_dpt_id_fk
    join autoparts_tbl on apt_id_pk = daa_apt_id_fk
  ) tbl
  where apt_car_id_fk = _car_id and coalesce(dpt_presence,0) > 0
    and dpt_enabled = true
    and dpt_languages like '%'|| _lang ||'%' and dpt_apt_id_fk in (
    WITH RECURSIVE subautoparts AS(
      SELECT * FROM autoparts_tbl WHERE apt_id_pk = _autopart_id
      UNION ALL
      SELECT apt.* FROM autoparts_tbl AS apt JOIN subautoparts AS sapt ON apt.apt_parent_id_fk = sapt.apt_id_pk
    ) SELECT apt_id_pk FROM subautoparts where apt_is_last = true
  );
$BODY$
  language sql;
/* }}} Function: count_details_in_autopart_fn */


/* Function: detail_discount_diff_fn {{{
 * Сумма скидки на деталь
 */
drop function if exists detail_discount_diff_fn(detail_id int) cascade;

create or replace function detail_discount_diff_fn(detail_id int)
  returns numeric as
$BODY$
  select round(dpt_cost / 100 * dpt_discount, 2)
  from details_autoparts_tbl
  where dpt_id_pk = detail_id;
$BODY$
  language sql;
/* }}} Function: detail_discount_diff_fn */


/* Function: detail_new_cost_fn {{{
 * Новая стоимость детали с учетом скидки
 */
drop function if exists detail_new_cost_fn(detail_id int) cascade;

create or replace function detail_new_cost_fn(detail_id int)
  returns numeric as
$BODY$
  select dpt_cost - coalesce(round(dpt_cost / 100 * dpt_discount, 2),0)
  from details_autoparts_tbl
  where dpt_id_pk = detail_id;
$BODY$
  language sql;
/* }}} Function: detail_new_cost_fn */


/* Function: color_size_cost_count_fn {{{
 * Получение цены и кол-ва детали в цвете и размере
 */
drop function if exists color_size_cost_count_fn(_detail_id int, _color_id int, _size_id int) cascade;

drop type cost_available_color_size_type;
create type cost_available_color_size_type as (cost varchar, cost_unformat numeric, count int);

create or replace function color_size_cost_count_fn(_detail_id int, _color_id int, _size_id int)
  returns cost_available_color_size_type as
$BODY$
  select
    replace(replace(trim(to_char(
      case
        when coalesce(csp_cost, 0) = 0 then
          coalesce(detail_new_cost_fn(dpt_id_pk),0) + coalesce(dac_diff_cost,0) + coalesce(das_diff_cost,0)
        else csp_cost
      end::NUMERIC
    ,'999 999 999.99')), '.', ','), ',00', '') as cost,
    case
      when coalesce(csp_cost, 0) = 0
        then coalesce(detail_new_cost_fn(dpt_id_pk),0) + coalesce(dac_diff_cost,0) + coalesce(das_diff_cost,0)
      else csp_cost
    end::NUMERIC as cost_unformat,
    coalesce(case
      when coalesce(_color_id,0) = 0 and coalesce(_size_id,0) = 0 then dpt_presence
      when csp_available = 0 then 0
      when coalesce(csp_available,0) != 0 then csp_available
      when dac_available = 0 or das_available = 0 then 0
      when dac_available is null and das_available is not null then das_available
      when das_available is null and dac_available is not null then dac_available
      when dac_available < das_available then dac_available
      else das_available
    end,0)::INT as count
  from details_autoparts_tbl
  left join details_autoparts_colors_tbl
    on dpt_id_pk = dac_dpt_id_fk and dac_id_pk = _color_id
  left join details_autoparts_sizes_tbl
    on dpt_id_pk = das_dpt_id_fk and das_id_pk = _size_id
  left join details_autoparts_colors_sizes_pair_tbl
    on dpt_id_pk = csp_dpt_id_fk and csp_dac_id_fk = dac_id_pk
      and csp_das_id_fk = das_id_pk
  where dpt_id_pk = _detail_id;
$BODY$
  language sql;
/* }}} Function: color_size_cost_count_fn */


/* Function: set_dpt_date_update {{{
 * Меняем дату последнего обновления наличия или скидки
 */
CREATE OR REPLACE FUNCTION set_dpt_date_update()
RETURNS trigger
AS $function$
begin
  if (TG_OP = 'INSERT') then
    new.dpt_last_update := now();
  elseif (TG_OP = 'UPDATE') then
    if (coalesce(old.dpt_presence,0) = 0) and (new.dpt_presence > 0) then
      new.dpt_last_update := now();
    end if;
    if (coalesce(old.dpt_discount,0) != coalesce(new.dpt_discount,0)) then
      new.dpt_last_update := now();
    end if;
  end if;

  return new;
end
$function$
language 'plpgsql' VOLATILE;
/* }}} Function: set_dpt_date_update */


set client_min_messages = 'notice';

\echo 'Functions by module Cars created.'
