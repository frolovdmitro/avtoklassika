<?php
/**
 * Uwin CMS
 *
 * Файл содержащий модель модуля по умолчанию, который обрабатывает:
 *  - Главную страницу
 *  - Статическую страницу
 *  - Страницы ошибок
 *  - Страницу 404
 *
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 * @version   $Id$
 */

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Model\Abstract_    as Abstract_;
use \Uwin\Registry        	 as Registry;
use \Uwin\Fs             	 as Fs;
use \Uwin\Config          	 as Config;

/**
 * Модель модуля по умолчанию, который обрабатывает:
 *  - Главную страницу
 *  - Статическую страницу
 *  - Страницы ошибок
 *  - Страницу 404
 *
 * @author    Khmelevskii Yurii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class Access extends Abstract_
{
	private function _sortMenu($item1, $item2)
	{
		if ($item1['order'] == $item2['order']) {
			return 0;
		}

		if ($item1['order'] < $item2['order']) {
			return -1;
		}

		return 1;
	}

	private function _getModules() {
		// Сканируем все модули приложения и ищем их описание админки в admin.xml
		$registry = Registry::getInstance();

		$fs = new Fs($registry['path']['modules']);
		$modulesConfigs = $fs->getFilesRecursive('admin.xml');
		unset($fs);

		// Проходимся по всем найденным описаниям модулей
		$modulesList = array();
		foreach ($modulesConfigs as $config) {
			// Получаем имя модуля
			$moduleName = basename( dirname( dirname($config) ) );

			// Получаем переменных с xml-файла модуля
			$configer = new Config(Config::XML);
			$moduleValues = $configer
				->open($config, $moduleName)->get(null, true);

			if (!array_key_exists('index', $moduleValues)) {
				continue;
			}

            if (!array_key_exists('datasources', $moduleValues['index'])) {
                continue;
            }

            $tableName = array_keys($moduleValues['index']['datasources']);
			$tableName = $tableName[0];

			// Узначем на каком языке должна быть панель управления
			$language = 'ru';
			if ( isset($registry['languageAdmin']) ) {
				$language = $registry['languageAdmin'];
			}

			// Получаем путь к языковому файлу модуля
			$langNameFile = dirname( dirname( dirname($config) ) ) . DIR_SEP
						  . $moduleName . DIR_SEP . 'languages' . DIR_SEP
						  . 'admin' . DIR_SEP . $language . '.xml';

			// Если языковый файл есть, получаем его переменные и склеиваем
			// переменные модуля с ними
			if ( file_exists($langNameFile) ) {
				$langer = new Config(Config::XML);
				$langValues = $langer
					->open($langNameFile, $moduleName)
					->get();

				if ('MAIN' == $moduleValues['type'] || 'MODULE' == $moduleValues['type']) {
					$modulesList[$moduleName] = array(
						'id'      => $moduleName,
						'table'   => $tableName,
						'caption' => $langValues['caption'],
						'order'   => $moduleValues['order'],
					);
				}
			}
		}

		// Сортировка полученного массива пунктов меню модулей
		usort($modulesList, array($this, "_sortMenu") );

		$modules = array();
		foreach ($modulesList as $values) {
			$modules[$values['id']] = array(
				'table'   => $values['table'],
				'caption' => $values['caption'],
				'order'   => $values['order'],
			);
		}

		return $modules;
	}

	private function _getSubModules($name) {
		// Сканируем все модули приложения и ищем их описание админки в admin.xml
		$registry = Registry::getInstance();

		$fs = new Fs($registry['path']['modules']);
		$modulesConfigs = $fs->getFilesRecursive('admin.xml');
		unset($fs);

		// Узначем на каком языке должна быть панель управления
		$language = 'ru';
		if ( isset($registry['languageAdmin']) ) {
			$language = $registry['languageAdmin'];
		}

		// Проходимся по всем найденным описаниям модулей
		$subModulesList = array();
		foreach ($modulesConfigs as $config) {
			// Получаем имя модуля
			$moduleName = basename( dirname( dirname($config) ) );

			if ($name != $moduleName) {
				continue;
			}

			// Получаем переменных с xml-файла модуля
			$configer = new Config(Config::XML);
			$moduleValues = $configer
				->open($config, $moduleName)->get();

			if (!array_key_exists('index', $moduleValues)) {
				continue;
			}


			// Получаем путь к языковому файлу модуля
			$langNameFile = dirname( dirname( dirname($config) ) ) . DIR_SEP
						  . $moduleName . DIR_SEP . 'languages' . DIR_SEP
						  . 'admin' . DIR_SEP . $language . '.xml';

			// Если языковый файл есть, получаем его переменные и склеиваем
			// переменные модуля с ними
			$langValues = array();
			if ( file_exists($langNameFile) ) {
				$langer = new Config(Config::XML);
				$langValues = $langer
					->open($langNameFile, $moduleName)
					->get();
			}


			foreach($moduleValues as $subname => $submodule) {
				if ( 'index' != $subname && isset($submodule['datasources']) ) {
					$tableName = array_keys($submodule['datasources']);
					$tableName = $tableName[0];

					$subModulesList[$subname] = array(
						'id'      => $subname,
						'table'   => $tableName,
						'caption' => $langValues[$subname]['caption'],
					);
				}
			}
		}

		return $subModulesList;
	}

	public function getModulesSelect($default = null) {
		// Проходимся по всем найденным описаниям модулей
		$modules = $this->_getModules();

		$modulesList = array();
		foreach ($modules as $name => $values) {
			$defaultItem = null;
			if ($name == $default) {
				$defaultItem = 'selected';
			}

			$modulesList[] = array(
				'id'      => $name,
				'caption' => $values['caption'],
				'default' => $defaultItem,
				'order'   => $values['order'],
			);
		}

		return $modulesList;
	}

	public function getSubModulesSelect($default = null) {
		$selectResult = $this->db()->query()
			->addSql('select ara_module_name from access_rules_administrators_tbl where ara_id_pk=$1')
			->addParam($this->getRequest()->get('id'))
		 	->fetchRow(0, false);

		// Проходимся по всем найденным описаниям модулей
		$modules = $this->_getSubModules($selectResult['ara_module_name']);

		$subModulesList = array();
		foreach ($modules as $name => $values) {
			$defaultItem = null;
			if ($name == $default) {
				$defaultItem = 'selected';
			}

			$subModulesList[] = array(
				'id'      => $name,
				'caption' => $values['caption'],
				'default' => $defaultItem,
			);
		}

		return $subModulesList;
	}

	public function addRule() {
		$modules = $this->_getModules();

		$admin_id = $this->getRequest()->get('id');
		$values = $this->getRequest()->post();

		if ( !isset($values['ara_enabled'])) {
			$values['ara_enabled'] = 'false';
		}
		if ( !isset($values['ara_hide_module'])) {
			$values['ara_hide_module'] = 'false';
		}
		$this->db()->query()
			->addSql('insert into access_rules_administrators_tbl')
			->addSql('(ara_adm_id_fk, ara_module_name, ara_module_caption,')
			->addSql('ara_table_name, ara_add_records, ara_edit_records,')
			->addSql('ara_delete_records, ara_enabled, ara_hide_module, ara_filter) values(')
			->addSql('$1, $2, $3, $4, $5, $6, $7, $8, $9, $10)')
			->addParam($admin_id)
			->addParam($values['ara_module_name'])
			->addParam($modules[$values['ara_module_name']]['caption'])
			->addParam($modules[$values['ara_module_name']]['table'])
			->addParam($values['ara_add_records'])
			->addParam($values['ara_edit_records'])
			->addParam($values['ara_delete_records'])
			->addParam($values['ara_enabled'])
			->addParam($values['ara_hide_module'])
			->addParam($values['ara_filter'])
			->execute();

		return $this;
	}

	public function editRule() {
		$modules = $this->_getModules();

		$values = $this->getRequest()->post();
		if ( !isset($values['ara_enabled'])) {
			$values['ara_enabled'] = 'false';
		}
		if ( !isset($values['ara_hide_module'])) {
			$values['ara_hide_module'] = 'false';
		}

		$this->db()->query()
			->addSql('update access_rules_administrators_tbl set')
			->addSql('ara_module_name=$2, ara_module_caption=$3,')
			->addSql('ara_table_name=$4, ara_add_records=$5, ara_edit_records=$6,')
			->addSql('ara_delete_records=$7, ara_enabled=$8, ara_hide_module=$9, ara_filter=$10 where')
			->addSql('ara_id_pk=$1')
			->addParam($this->getRequest()->getParam('id'))
			->addParam($values['ara_module_name'])
			->addParam($modules[$values['ara_module_name']]['caption'])
			->addParam($modules[$values['ara_module_name']]['table'])
			->addParam($values['ara_add_records'])
			->addParam($values['ara_edit_records'])
			->addParam($values['ara_delete_records'])
			->addParam($values['ara_enabled'])
			->addParam($values['ara_hide_module'])
			->addParam($values['ara_filter'])
			->execute();

		return $this;
	}

	public function addSubRule() {
		$selectResult = $this->db()->query()
			->addSql('select ara_module_name from access_rules_administrators_tbl where ara_id_pk=$1')
			->addParam($this->getRequest()->get('id'))
		 	->fetchRow(0, false);

		// Проходимся по всем найденным описаниям модулей
		$modules = $this->_getSubModules($selectResult['ara_module_name']);

		$admin_id = $this->getRequest()->get('adm_id');
		$rule_id = $this->getRequest()->get('id');
		$values = $this->getRequest()->post();

		if ( !isset($values['ara_enabled'])) {
			$values['ara_enabled'] = 'false';
		}
		if ( !isset($values['ara_hide_module'])) {
			$values['ara_hide_module'] = 'false';
		}
		$this->db()->query()
			->addSql('insert into access_rules_administrators_tbl')
			->addSql('(ara_parent_id_fk, ara_adm_id_fk, ara_module_name, ara_module_caption,')
			->addSql('ara_table_name, ara_add_records, ara_edit_records,')
			->addSql('ara_delete_records, ara_enabled, ara_hide_module, ara_filter) values(')
			->addSql('$1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11)')
			->addParam($rule_id)
			->addParam($admin_id)
			->addParam($values['ara_module_name'])
			->addParam($modules[$values['ara_module_name']]['caption'])
			->addParam($modules[$values['ara_module_name']]['table'])
			->addParam($values['ara_add_records'])
			->addParam($values['ara_edit_records'])
			->addParam($values['ara_delete_records'])
			->addParam($values['ara_enabled'])
			->addParam($values['ara_hide_module'])
			->addParam($values['ara_filter'])
			->execute();

		return $this;
	}

	public function editSubRule() {
		$selectResult = $this->db()->query()
			->addSql('select ara_module_name from access_rules_administrators_tbl where ara_id_pk=$1')
			->addParam($this->getRequest()->get('id'))
		 	->fetchRow(0, false);

		// Проходимся по всем найденным описаниям модулей
		$modules = $this->_getSubModules($selectResult['ara_module_name']);

		$values = $this->getRequest()->post();
		if ( !isset($values['ara_enabled'])) {
			$values['ara_enabled'] = 'false';
		}
		if ( !isset($values['ara_hide_module'])) {
			$values['ara_hide_module'] = 'false';
		}

		$this->db()->query()
			->addSql('update access_rules_administrators_tbl set')
			->addSql('ara_module_name=$2, ara_module_caption=$3,')
			->addSql('ara_table_name=$4, ara_add_records=$5, ara_edit_records=$6,')
			->addSql('ara_delete_records=$7, ara_enabled=$8, ara_hide_module=$9, ara_filter=$10 where')
			->addSql('ara_id_pk=$1')
			->addParam($this->getRequest()->getParam('id'))
			->addParam($values['ara_module_name'])
			->addParam($modules[$values['ara_module_name']]['caption'])
			->addParam($modules[$values['ara_module_name']]['table'])
			->addParam($values['ara_add_records'])
			->addParam($values['ara_edit_records'])
			->addParam($values['ara_delete_records'])
			->addParam($values['ara_enabled'])
			->addParam($values['ara_hide_module'])
			->addParam($values['ara_filter'])
			->execute();

		return $this;
	}

	public function getFilterHelp() {
		return 'asdasd11';
	}
}