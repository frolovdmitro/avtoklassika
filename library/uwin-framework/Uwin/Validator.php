<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Validator, который отвечает за валидацию
 * передаваемых ему данных
 *
 * @category  Uwin
 * @package   Uwin\Validator
 * @author    Khmelevskii Yurii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 * @version   $Id$
 */

/**
 * Объявляем пространсто имен Uwin, к которому относится класс Mail
 */
namespace Uwin;

/**
 * Класс, который отвечает за валидацию передаваемых ему данных
 *
 * @category  Uwin
 * @package   Uwin\Validator
 * @author    Khmelevskii Yurii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class Validator
{
	/**
	 * Проверка является ли значение пустым
	 *
	 * @param mixed $value - Валидируемое значение
	 *
	 * @return bool
	 */
	public function isEmpty($value) {
        $value = (string)$value;
		$value = trim($value);
        if ($value == 'null') {
            $value = null;
        }

		if ( '' != $value && null != $value && array() != $value) {
			return false;
		}

		return true;
	}

	/**
	 * Проверка является ли значение адресом электронной почты
	 *
	 * @param mixed $value - Валидируемое значение
	 *
	 * @return bool
	 */
	public function isEmail($value) {
		if ( empty($value) ) {
			return true;
		}
		$value = trim($value);
		if ( !preg_match("/^[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[a-z]{2,4}|museum|travel)$/i", $value) ) {
			return false;
		}

		return true;
	}

	public function fileExtension($ext, array $list) {
		if ( empty($ext) ) {
				return true;
		}

		if ( !in_array($ext, $list)) {
			return false;
		}
		return true;
	}

    public function isPhoneNumber($value) {
        if ( empty($value) ) {
            return true;
        }

        $value = str_replace(array('(', ')', '+', ' ', '-'), '', $value);

        if ( mb_strlen($value) < 7 || !$this->parseInt($value)) {
            return false;
        }

        return true;
    }

	public function imageWidth($file, $width) {
		if (!file_exists($file)) {
			return false;
		}

		$sizes = getimagesize($file);
		if (false === $sizes) {
			return false;
		}

		if ( $sizes[0] != $width ) {
			return false;
		}
		return true;
	}

	public function imageHeight($file, $height) {
		if (!file_exists($file)) {
			return false;
		}

		$sizes = getimagesize($file);
		if (false === $sizes) {
			return false;
		}

		if ( $sizes[1] != $height ) {
			return false;
		}
		return true;
	}

    public function imageMaxWidth($file, $width) {
   		if (!file_exists($file)) {
   			return false;
   		}

   		$sizes = getimagesize($file);
   		if (false === $sizes) {
   			return false;
   		}

   		if ( $sizes[0] > $width ) {
   			return false;
   		}
   		return true;
   	}

   	public function imageMaxHeight($file, $height) {
   		if (!file_exists($file)) {
   			return false;
   		}

   		$sizes = getimagesize($file);
   		if (false === $sizes) {
   			return false;
   		}

   		if ( $sizes[1] > $height ) {
   			return false;
   		}
   		return true;
   	}

	/**
	 * Проверка является ли значение адресом электронной почты
	 *
	 * @param mixed $value - Валидируемое значение
	 * @param string $regexp
	 *
	 * @return bool
	 */
	public function equalRegexp($value, $regexp) {
		$value = trim($value);
		if ( !preg_match("/" . $regexp . "/i", $value) ) {
			return false;
		}

		return true;
	}

	public function parseInt($value) {
		if ( empty($value) ) {
			return true;
		}

		if ( !preg_match('/^[-+]?[\d]+$/i', $value) ) {
			return false;
		}

		return true;
	}

	public function parseFloat($value) {
		if ( empty($value) ) {
			return true;
		}

		if ( !preg_match('/^[-+]?[\d]+[\.,]{0,1}[\d]*$/i', $value) ) {
			return false;
		}

		return true;
	}

	public function parseTime($value) {
		if ( empty($value) ) {
			return true;
		}

		if ( !preg_match('/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])$/i', $value) ) {
			return false;
		}

		return true;
	}

	public function parseDate($value) {
		if ( empty($value) ) {
			return true;
		}

		if ( !preg_match('#^(((((0[1-9])|(1\d)|(2[0-8]))[/.-]((0[1-9])|(1[0-2])))|((31[/.-]((0[13578])|(1[02])))|((29|30)[/.-]((0[1,3-9])|(1[0-2])))))[/.-]((20[0-9][0-9])|(19[0-9][0-9])))|((29[/.-]02[/.-](19|20)(([02468][048])|([13579][26]))))$#i', $value) ) {
			return false;
		}

		return true;
	}

	public function parseDatetime($value) {
		if ( empty($value) ) {
			return true;
		}

		$datetime = explode(' ', $value);

		if ( count($datetime) != 2) {
			return false;
		}

		if ( !$this->parseDate(trim($datetime[0])) ) {
			return false;
		}

		if ( !$this->parseTime(trim($datetime[1])) ) {
			return false;
		}

		return true;
	}

    public function minHours($start_dt, $stop_dt, $min) {
        $start = strtotime($start_dt);
        $stop  = strtotime($stop_dt);

        $diff = ($stop - $start) / 60 / 60;

        if ($diff < $min) {
            return true;
        }

        return false;
    }

	public function parseUrl($value) {
		if ( empty($value) ) {
			return true;
		}

//		if ( !preg_match('#^(http|https|ftp)\://[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,­3}(:[a-zA-Z0-9]*)?/?([a-zA-Z0-9\-\._\?\,\'/\\\+&am­p;%\$\#\=~])*$#i', $value) ) {
//			return false;
//		}

		return true;

	}

	/**
	 * Проверка существует ли значение в указанном массиве
	 *
	 * @param mixed $value - Значение, которое ищется
	 * @param array $array - Массив, в котором ищется значение
	 *
	 * @return bool
	 */
	public function isExists($value, array $array) {
		$value = trim($value);
		
		return array_key_exists($value, $array);
	}

	/**
	 * Проверка на существование указанного значения в базе данных, указанной
	 * таблице в указанном ее поле
	 * 
	 * @param mixed          $value - Валидируемое значение
	 * @param \Uwin\Db\Query $query - Экземпляр класса запроса к БД \Uwin\Db\Query
	 * @param string         $table - Имя таблицы
	 * @param string         $field - Имя поля в таблице, в котором ищется валидируемое значение
	 *
	 * @return bool
	 */
	public function isExistsInDb($value, $query, $table, $field, $pk = null, $notId = null) {
		$value = trim($value);

		$select = $query->addSql("select {$field} from {$table} where upper({$field}::VARCHAR)=upper($1::VARCHAR)")
			->addParam($value);

		if (null != $notId) {
			$select->addSql(' and ' . $pk . '!=$2')
				   ->addParam($notId);
		}
		$select = $select->fetchRow(0, false);

		if ( empty($select) ) {
			return false;
		}

		return true;
	}

	/**
	 * Проверка, находится ли валидируемое значение в пределах указанных
	 * минимального и минимального значений
	 * 
	 * @param mixed     $value - Валидируемое число
	 * @param float|int $min   - Нижняя граница
	 * @param float|int $max   - Верхняя граница
	 * @param bool      $withLimitsValue = false - ОПЦИОНАЛЬНО Включительно указанные граничные значения
	 *
	 * @return bool
	 */
	public function withinRange($value, $min, $max, $withLimitsValue = false) {
		if ($withLimitsValue) {
			$min--;
			$max++;
		}

		if ( !($value > $min && $value < $max) ) {
			return false;
		}

		return true;
	}

	/**
	 * Проверка занчений на равенство
	 * 
	 * @param mixed $value1 - Валидируемое значение №1
	 * @param mixed $value2 - Валидируемое значение №2
	 * @param mixed $value3 = null - ОПЦИОНАЛЬНО Валидируемое значение №3
	 * @param mixed $value4 = null - ОПЦИОНАЛЬНО Валидируемое значение №4
	 *
	 * @return bool
	 */
	public function equal($value1, $value2, $value3 = null, $value4 = null) {
		if ($value1 != $value2) {
			return false;
		}

		if (null !== $value3 && $value1 !== $value3) {
			return false;
		}

		if (null !== $value4 && $value1 !== $value4) {
			return false;
		}

		return true;
	}

	/**
	 * Проверка больше ли валидируемое значение указанного значения
	 * 
	 * @param mixed     $value - Валидируемое значение
	 * @param int|float $limit - Верхняя граница
	 * @param bool $orEqual = false - ОПЦИОНАЛЬНО Включительно с верхней границей
	 *
	 * @return bool
	 */
	public function moreThen($value, $limit, $orEqual = false) {
		if ($orEqual) {
			$limit--;
		}

		if ($value <= $limit) {
			return false;
		}

		return true;
	}

	/**
	 * Проверка меньше ли валидируемое значение указанного значения
	 *
	 * @param mixed     $value - Валидируемое значение
	 * @param int|float $limit - Нижняя граница
	 * @param bool $orEqual = false - ОПЦИОНАЛЬНО Включительно с нижней границей
	 *
	 * @return bool
	 */
	public function lessThen($value, $limit, $orEqual = false) {
		if ($orEqual) {
			$limit++;
		}

		if ($value >= $limit) {
			return false;
		}

		return true;
	}

	/**
	 * Проверка на равенство длины валидируемого значения. Можно проверять
	 * строки и массивы
	 *
	 * @param mixed $value - Валидируемое значение
	 * @param int $length  - Длина
	 *
	 * @return bool
	 */
	public function length($value, $length) {
		if ( is_string($value) && $length != mb_strlen($value) ) {
			return false;
		}

		if ( is_array($value) && $length != count($value) ) {
			return false;
		}

		return true;
	}

    /**
     * Метод проводит валидацию всех переменных на основе переданных правил
     *
     * @param $form
     * @param array $rules
     * @param array $variables
     * @param $errors_texts
     *
     * @return array
     */
    public function validate($form, $rules, $variables, $errors_texts) {
        $result = array();

        if ( empty($rules) ) {
            return $result;
        }

        foreach ($rules as $field_name => $field_rules) {
            // Если переменной нет, значит она равна NULL, так как ее
            // валидировать все равно нужно
            if ( !isset($variables[$field_name]) ) {
                $value = null;
            } else
            if ( !is_array($variables[$field_name]) ) {
                $value = trim($variables[$field_name]);
            } else {
                $value = $variables[$field_name];
            }

            foreach ($field_rules as $rule => $params) {
                if ( isset($result[$field_name]) ) {
                    continue;
                }

                $field_text = 'lng_validate_' . $form . '_' . $field_name . '_'
                      . $rule;
                $text = $rule;
                if ( isset($errors_texts[$field_text]) ) {
                     $text = $errors_texts[$field_text];
                }

                switch ($rule) {
                    case 'empty':
                        $this->isEmpty($value)
                            ? $result[$field_name] = $text
                            : null;
                        break;

                    case 'parseEmail':
                        !$this->isEmail($value) ? $result[$field_name] = $text : null;
                        break;

                    case 'parsePhone':
                        !$this->isPhoneNumber($value) ? $result[$field_name] = $text : null;
                        break;

                    case 'parseDateTime':
                        !$this->parseDatetime($value) ? $result[$field_name] = $text: null;
                        break;

                    case 'minHours':
                        $start_field = $params['start'];
                        $stop_field = $params['stop'];
                        $minHours = (int)$params['value'];

                        if ( isset($result[$start_field]) || isset($result[$stop_field]) ) {
                            break;
                        }

                        $start = $variables[$start_field];
                        $stop  = $variables[$stop_field];
                        if ( $this->minHours($start, $stop, $minHours) ) {
                            $result[$field_name] = $text;
                        }

                        break;

                    case 'extensions':
                        if ( empty($value) ) {
                            break;
                        }
                        $path_info = pathinfo($value['name']);
                        if ( !isset($path_info['extension']) ) {
                            break;
                        }
                        $ext = mb_strtolower($path_info['extension']);

                        if ( !$this->fileExtension($ext, explode('|', $params)) ) {
                            $result[$field_name] = $text;
                        }

                        break;

                    case 'maxSize':
                        if ( empty($value) ) {
                            break;
                        }
                        if ( (int)$params < (int)$value['size'] ) {
                            $result[$field_name] = $text;
                        }

                        break;
                }
            }
        }

        $finish_result = array();
        foreach ( array_keys($result) as $field) {
            $finish_result[] = array(
                'id'   => $field,
                'text' => $result[$field]
            );
        }

        return $finish_result;
    }
}
