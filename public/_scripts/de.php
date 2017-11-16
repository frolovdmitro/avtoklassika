<?php
require_once "PHPExcel/PHPExcel/Reader/Excel5.php";


function my_mb_ucfirst($str) {
	$fc = mb_strtoupper(mb_substr($str, 0, 1, 'UTF-8'), 'UTF-8');
	return $fc.mb_substr($str, 1, mb_strlen($str, 'UTF-8')-1, 'UTF-8');
}
$excelFileName = "de3.xls";

$objReader = new PHPExcel_Reader_Excel5();
$objPHPExcel = $objReader->load($excelFileName);
$objWorksheet = $objPHPExcel->getActiveSheet();

$highestRow = $objWorksheet->getHighestRow();

// var_dump($highestRow);
$sql="";
// $is_group = false;
// $is_subgroup = false;
// $is_first = true;
// $start_id = 2035;
for ($i=1; $i<$highestRow+1; $i++) {
	// $cell = $objWorksheet->getCellByColumnAndRow(0, $i);
	// $num = $cell->getValue();

	$cell = $objWorksheet->getCellByColumnAndRow(0, $i);
	$ru = $cell->getValue();

	$cell = $objWorksheet->getCellByColumnAndRow(2, $i);
	$de = $cell->getValue();

  if ( empty($de) ) {
    continue;
  }

	$sql .= "update autoparts_tbl set apt_name_de = '" . $de . "' where trim(lower(apt_name_ru)) = trim(lower('" . $ru . "'));\n";
	// $sql .= "update details_autoparts_tbl set dpt_name_de='" . $de . "' where trim(lower(dpt_num_detail)) = trim(lower('" . $num . "'));\n";
}

echo $sql;
/*

$detail = array(
'ДВИГАТЕЛЬ' => 'ENGINE',
'Двигатель в сборе' => 'Engine, Assy',
'Подвеска двигателя' => 'Engine Mounting',
'Блок цилиндров' => 'Cylinder Block',
'Головка блока цилиндров' => 'Cylinder Head',
'Поршни и шатуны' => ' Pistons and Connecting Rods',
'Вал коленчатый' => 'Crankshaft',
'Вал распределительный' => 'Camshaft',
'Клапаны и толкатели' => ' Valves and Valve Tappets',
'Газопровод' => 'Manifold',
'Картер масляный' => 'Oil Sump',
'Маслоприемник' => ' Oil Intake',
'Насос масляный' => 'Oil Pump',
'Вентиляция картера' => 'Crankcase Ventilation',
'Привод распределителя' => 'Distributor Drive',
'Фильтр центробежной очистки масла' => 'Centrifugal Oil Filter',
'СИСТЕМА ПИТАНИЯ' => 'FUEL SYSTEM',
'Бак бензиновый' => 'Gasoline Tank',
'Пробка бензинового бака' => 'Gasoline Tank Cap',
'Бензинопровод' => 'Fuel Line',
'Насос бензиновый' => 'Fuel Pump',
'Карбюратор' => 'Carburettor',
'Акселератор' => 'Accelerator',
'Фильтр воздушный' => 'Air Cleaner',
'Фильтр тонкой очистки топлива ' => 'Secondary Fuel Filter',
'СИСТЕМА ВЫПУСКА ГАЗА' => 'EXHAUST SYSTEM',
'Глушитель выхлопа' => 'Muffler',
'Трубы, приемные и выхлопные' => 'Inlet and Exhaust Pipes',
'СИСТЕМА ОХЛАЖДЕНИЯ' => 'COOLING SYSTEM',
'Радиатор' => 'Radiator',
'Трубопроводы и шланги' => 'Pipes and Hoses',
'Пробка радиатора' => 'Radiator Filler Cap',
'Краники сливные' => 'Drain Cock',
'Термостат системы охлаждения' => 'Cooling System Thermostat',
'Насос водяной' => 'Water Pump',
'Вентилятор' => 'Fan',
'Жалюзи радиатора' => 'Radiator Blind',
'АВТОМАТИЧЕСКАЯ КОРОБКА ПЕРЕДАЧ' => 'AUTOMATIC TRANSMISSION',
'Управление автоматической коробкой, передач' => 'Automatic Transmission Control',
'Насосы масляные автоматической передачи' => 'Automatic Transmission Oil Pumps',
'Гидротрансформатор' => 'Hydraulic Torque Converter',
'Механизм коробки передач планетарный' => 'Planetary gearbox',
'Механизм управления планетарной передачей' => 'Planetary Gearbox Control',
'Система гидравлического управления коробкой передач' => 'Transmission Hudraulic Control System',
'Радиатор охлаждения автоматической коробки передач' => 'Automatic Transmission Cooling Radiator',
'ВАЛЫ КАРДАННЫЕ' => 'PROPELLER SHAFTS',
'Вал карданный заднего моста' => 'Rear Axle Propeller Shaft',
'Вал карданный промежуточный' => 'Intermediate Propeller Shaft',
'ЗАДНИЙ МОСТ' => 'REAR AXLE',
'Задний мост в сборе' => 'Rear Axle, Assy',
'Картер и кожухи полуосей' => 'Rear Axle Housing and Axle Shaft Tubes',
'Главная передача' => ' Final Drive',
'Дифференциал и полуоси' => 'Differential and Axle Shafts ',
'РАМА' => 'FRAME',
'Рама' => ' Frame',
'Брызговики двигателя' => ' Engine Mud Guards',
'Бампер передний' => 'Front Bumper',
'Бампер задний' => 'Rear Bumper',
'Передние буксирные крюки' => 'Front Tow Hooks',
'Подвеска передняя в сборе' => 'Front Suspension, Assy',
'Пружины передней подвески' => 'Front Suspension Springs',
'Стойка и рычаги передней подвески' => 'Support and Front Suspension Arms',
'Амортизаторы передней подвески' => 'Front Suspension Shock Absorbers',
'Стабилизатор поперечной устойчивости передней подвески' => 'Front Suspension Lateral Stabilizer',
'Рессоры задние' => 'Rear Springs',
'Амортизаторы задней подвески' => 'Rear Suspension Shock Absorbers',
'ПОВОРОТНЫЕ КУЛАКИ И РУЛЕВЫЕ ТЯГИ' => 'STEERING KNUCKLES AND STEERING RODS',
'Поворотные кулаки' => 'Steering Knuckles',
'Рулевые тяги' => 'Steering Rods',
'КОЛЕСА И СТУПИЦЫ' => 'WHEELS AND HUBS',
'Колеса' => 'Wheels',
'Колпаки колес' => 'Wheel Caps',
'Ступицы передних колес' => 'Front Wheel Hubs',
'Крепление запасного колеса' => 'Spare wheel carrier',
'Шины бескамерные' => 'Tubeless Tyres',
'РУЛЕВОЕ УПРАВЛЕНИЕ' => 'STEERING',
'Механизм рулевого управления' => 'Steering Gear',
'Колесо рулевого управления' => 'Steering Wheel',
'Крепление рулевого управления' => 'Steering Gear Mounting',
'Силовой цилиндр гидроусилителя рулевого управления' => 'Hydraulic Steering Booster Cylinder',
'Насос масляный гидроусилителя руля' => 'Hydraulic Steering Booster Pump .',
'Трубопроводы и шланги гидроусилителя руля' => 'Hydraulic Steering Pipes and Hoses',
'Клапан управления гидроусилителя руля' => 'Hydraulic Steering Control Valve',
'ТОРМОЗА' => 'BRAKES',
'Передние ножные тормоза и тормозные барабаны' => 'Front Foot Brakes and Brake Drums',
'Задние ножные тормоза и тормозные барабаны' => 'Rear Foot Brakes and Brake Drums',
'Тормозная педаль и привод' => 'Brake pedal and linkage',
'Главный цилиндр тормоза' => 'Brake Master Cylinder',
'Трубопроводы гидротормозов' => 'Hydraulic Brakes Lines',
'Центральный тормоз' => 'Transmission Brake',
'Управление центральным тормозом' => 'Transmission Brake Control ',
'Усилитель тормоза' => 'Brake Booster',
'Обратный клапан усилителя тормоза' => 'Brake Booster Non-Return Valve',
'Бачок вакуумный усилителя тормоза' => 'Brake Booster Vacuum Tank',
'ЭЛЕКТРООБОРУДОВАНИЕ' => 'ELECTRIC EOUIPMENT',
'Генератор' => 'Generator',
'Реле-регулятор напряжения' => 'Current and Voltage Regulator',
'Аккумуляторная батарея' => 'Storage Battery',
'Включатель зажигания и стартера' => 'Ignition and Starter Switch',
'Катушка зажигания' => ' Ignition Coil',
'Распределитель' => 'Distributor',
'Свечи запальные и провода зажигания' => 'Spark Plugs and Ignition Wiring',
'Стартер' => 'Starter',
'Переключатель света центральный' => 'Main Lighting Switch',
'Переключатель света ножной' => 'Font Lighting Switch',
'Фары' => 'Headlamps',
'Подфарники' => 'Side Lamps',
'Освещение приборов' => 'Instrument Lighting',
'Внутреннее освещение' => 'Interior Lighting',
'Лампа переносная' => 'Inspection Lamp',
'Фонари задние' => 'Tail Lamps',
'Фонари освещения номерного знака' => 'Licence Plate Lamps',
'Включатель света «стоп»' => 'Stop Lamp Switch',
'Сигналы звуковые' => 'Horns',
'Предохранители' => 'Fuses',
'Соединители электропроводов' => 'Junction Blocks and Connectors',
'Электропровода' => 'Electric Wires',
'Прикуриватель' => 'Cigarette Lighter',
'Указатели поворота' => 'Turn Indicators',
'Включатель блокировочный' => 'Interlocking Switch',
'ПРИБОРЫ' => 'INSTRUMENTS',
'Комбинация приборов' => 'Instrument Cluster',
'Контрольные лампы' => 'Pilot Lamps',
'Часы' => 'Clock',
'ШОФЕРСКИЙ ИНСТРУМЕНТ И ПРИНАДЛЕЖНОСТИ' => 'DRIVER\\\'S KIT',
'Шоферский инструмент' => 'Driver\\\'s Tools',
'Бачок для масла' => 'Oil Can',
'Шприц для смазки' => 'Lubricating Gun',
'Домкрат реечный' => 'Rack-Type Jack',
'Насос для ручной перекачки бензина' => 'Hand-Operated Fuel Transfer Pump',
'КУЗОВ В СБОРЕ' => 'BODY ASSY',
'Крепление кузова' => 'Body Mounting',
'ОКНО ВЕТРОВОЕ' => 'WINDSHIELD',
'Стеклоочиститель и привод' => 'Windshield Wiper and Drive',
'Детали ветрового окна' => 'Windshield Parts',
'Установки опрыскивателя ветрового стекла' => 'Windshield Sprayer',
'ПЕРЕДОК' => ' BODY FRONT END',
'Детали передка' => 'Body Front End Parts',
'Ящик вещевой' => 'Instrument Panel Compartment',
'Вентиляция передка' => 'Cowl Ventilator',
'ЗАДОК' => 'BODY REAR END',
'Детали задка' => 'Body Rear End Parts',
'Окно задка' => 'Rear Window',
'Крышка багажника' => 'Luggage Compartment Lid',
'Петли крышки багажника' => 'Luggage Compartment LidHinges',
'Замок крышки багажника' => 'Luggage Compartment Lid Lock',
'ДВЕРЬ ПЕРЕДНЯЯ' => 'FRONT DOOR',
'Дверь передняя в сборе' => 'Front Door Assy',
'Детали передней двери' => 'Front Door Parts',
'Вентиляция передней двери' => 'Front Door Window Ventilator',
'Механизм перемещения стекол' => 'Window Glass Regulator',
'Замок и ручка передней двери' => 'Front Door Lock and Handle',
'Навеска передней двери' => 'Front Door Hinges',
'Уплотнение передней двери' => 'Front Door Sealing',
'ДВЕРЬ ЗАДНЯЯ' => 'REAR DOOR',
'Дверь задняя в сборе' => 'Rear Door, Assy',
'Детали задней двери' => 'Rear Door Parts',
'Окно задней двери' => 'Rear Door Window',
'Механизм перемещения стекол' => 'Rear door glass regulator',
'Замок и ручка задней двери' => ' Rear Door Lock and Handle',
'Навеска задней двери' => 'Rear Door Hinges',
'Уплотнение задней двери' => 'Rear Door Sealing',
'СИДЕНИЕ ПЕРЕДНЕЕ' => 'FRONT SEAT',
'Сидение переднее в сборе' => 'Front Seat, Assy',
'Механизм регулировки переднего сидения' => 'Front Seat Adjuster',
'СИДЕНИЕ ЗАДНЕЕ' => 'REAR SEAT',
'Подлокотник заднего сидения' => 'Rear Seat Arm Rest',
'СИДЕНИЕ ОТКИДНОЕ' => 'COLLAPSIBLE SEAT',
'Сидение откидное в сборе' => 'Collapsible Seat, Assy',
'Спинка откидного сидения' => 'Collapsible Seat Back',
'РАДИООБОРУДОВАНИЕ' => 'RADIO EQUIPMENT',
'Радиоприемник' => 'Radio Receiver',
'Антенна' => 'Antenna',
'Фильтры.' => 'Interference Suppressors',
'Провода радиооборудовании' => 'Radio Leads',
'ОТОПЛЕНИЕ' => 'HEATING',
'Отопление' => 'Heating',
'Вентилятор обдува ветрового стекла' => 'Windshield Defroster Fan',
'ПРИНАДЛЕЖНОСТИ' => 'ACCESSORIES',
'Держатели' => 'Holders',
'Пепельницы' => 'Ash Tray',
'Козырек противосолнечный' => 'Sun Visor',
'ОПЕРЕНИЕ' => 'HOOD AND FENDERS',
'Облицовка радиатора' => 'Radiator Shell',
'Капот' => 'Hood',
'Крыло переднее' => 'Front Fender',
'Замок и привода замка капота' => 'Hood Lock and Control',
'Петли капота' => 'Hood Hinges');

$sql = '';
foreach ($detail as $key=>$value) {
	$sql .= "update autoparts_tbl set apt_name_en='" . $value . "' where upper(apt_name) = upper('" . $key . "');\n";
}
echo $sql;
 */
?>
