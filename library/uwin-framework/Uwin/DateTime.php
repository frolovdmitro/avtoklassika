<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\DateTime, который отвечает за обработку даты и
 * времени
 *
 * @category  Uwin
 * @package   Uwin\DateTime
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 * @version   $Id$
 */

/**
 * Объявляем пространсто имен Uwin, к которому относится класс DateTime
 */
namespace Uwin;

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\DateTime\Exception as DateTimeException;

/**
 * Класс, который отвечает за обработку даты и времени
 *
 * @category  Uwin
 * @package   Uwin\DateTime
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 */
class DateTime
{
	/**
	 * Язык по умолчанию
	 * @var string
	 */
	const DEFAULT_LANGUAGE = 'ru';

	/**
	 * Текущий язык
	 * @var string
	 */
	private $_language = null;

	/**
	 * Массив языковых данных
	 * @var array
	 */
	private $_data = array();

	/**
	 * Конструктор класса, в которы передается используемый язык, если он не
	 * указан, используеться язык по умолчанию "ru", и в переменную $this->_data
	 * передается массив всех языковый значений
	 *
	 * @param string $language Язык
	 * @return bool
	 */
	public function __construct($language = self::DEFAULT_LANGUAGE)
	{
		$config = new Xml();
		$className = str_replace(__NAMESPACE__ . '\\', '', __CLASS__);
		$langFileName = __DIR__ . DIR_SEP . $className . DIR_SEP . 'languages' . DIR_SEP .  $language . '.xml';

		$config->setFileSettings($langFileName);

		$this->_data = $config->getValues();

		return true;
	}

	public function getEnglishNameDayByNum($num_day)
	{
		switch ($num_day) {
			case 1:
				$name_day = 'monday';
			break;
			case 2:
				$name_day = 'tuesday';
			break;
			case 3:
				$name_day = 'wednesday';
			break;
			case 4:
				$name_day = 'thursday';
			break;
			case 5:
				$name_day = 'friday';
			break;
			case 6:
				$name_day = 'saturday';
			break;
			case 7:
				$name_day = 'sunday';
			break;
			default:
				$name_day = null;
		}

		return $name_day;
	}

	public function getNumDayByEbglishName($name_day)
	{
		switch ($name_day) {
			case 'monday':
				$num_day = 1;
			break;
			case 'tuesday':
				$num_day = 2;
			break;
			case 'wednesday':
				$num_day = 3;
			break;
			case 'thursday':
				$num_day = 4;
			break;
			case 'friday':
				$num_day = 5;
			break;
			case 'saturday':
				$num_day = 6;
			break;
			case 'sunday':
				$num_day = 7;
			break;
			default:
				$num_day = null;
		}

		return $num_day;
	}

    /**
     * Метод преобразует полученную дату в удобный формат, например,
     * 25 февраля 2005
     *
     * @param string $datetime Дата/время
     * @param bool $useDay
     * @param bool $useMonth
     * @param bool $useYear
     *
     * @throws DateTime\Exception
     * @return string
     */
	public function getDateStr($datetime, $useDay = true, $useMonth = true, $useYear = true)
	{
		if ( !$date = strtotime($datetime) ) {
			throw new DateTimeException('DateTime error: failure datetime format "' . $datetime . '"', 801);
		}

		$month = (int)date('m', $date);
		$monthStr = $this->_data['namesMonth']['m' . $month];

		$dateFrm = null;
		if ($useDay) {
			$dateFrm .= date('d', $date);
		}

		if ($useMonth) {
			$dateFrm .= ' ' . $monthStr;
		}

		if ($useYear) {
			$dateFrm .= ' ' . date('Y', $date);
		}

		$dateFrm = trim($dateFrm);
		
		return $dateFrm;
	}

    /**
     * Метод преобразует день недели в удобный вид, например, Сегодня, Вчера,
     * Понедельник, Затра и т.д.
     *
     * @param string $datetime Дата/время
     * @param bool $with_word = true Выводить вместо дня недели такие слова как Сегодня, Вчера и т.д.
     * @param bool $short_name = false Выводить короткое имя или полное
     *
     * @throws DateTime\Exception
     * @return string
     */
	public function getDayStr($datetime, $with_word = true, $short_name = false)
	{
		if ( !is_int($datetime) ) {
			$dt = $datetime;
			if ( !$datetime = strtotime($datetime) ) {
				throw new DateTimeException('DateTime error: failure datetime format "' . $dt . '"', 801);
			}
		}

		$date = new \DateTime( date('d.m.Y', $datetime) );
		$curDate = new \DateTime( date('d.m.Y') );

		$nameDayNode = 'namesDays';
		if ($short_name) {
			$nameDayNode = 'shortNamesDays';
		}

		if ($with_word) {
			$diffDate = $date->diff($curDate);
			$sign = $diffDate->invert;
			if (0 == $sign) {
				$sign = -1;
			}

			$diffDay = $sign * $diffDate->days;


			if ( 0 === $diffDay ) {
				return $this->_data['namesDays']['today'];
			}

			if ( -1 === $diffDay ) {
				return $this->_data['namesDays']['yesterday'];
			}
/*
			if ( -2 === $diffDay ) {
				return $this->_data['namesDays']['before_yesterday'];
			}
*/
			if ( 1 === $diffDay ) {
				return $this->_data['namesDays']['tomorrow'];
			}
/*
			if ( 2 === $diffDay ) {
				return $this->_data['namesDays']['after_tomorrow'];
			}
*/
		}

		return $this->_data[$nameDayNode]['day' . strftime('%u', $datetime)];
	}

    public function getNumDayStr($num_day, $short_name = false)
   	{
        $nameDayNode = 'namesDays';
        if ($short_name) {
            $nameDayNode = 'shortNamesDays';
        }

       return $this->_data[$nameDayNode]['day' . $num_day];
    }

	/**
	 *
	 * @param $as_string
	 */
	public function getDayCurrentWeek($num_day = null)
	{
		if (null == $num_day) {
			$num_day = date('N');
		}

		$day_current_week = strtotime((date('N')-$num_day)*-1 . " days");

		return $day_current_week;
	}

	public function getMondayCurrentWeek()
	{
		return $this->getDayCurrentWeek(1);
	}

	public function getSundayCurrentWeek()
	{
		return $this->getDayCurrentWeek(7);
	}

	public function getDateDiffDay($count_day, $date = null)
	{
		if (null == $date) {
			$date = time();
		}

		$date_diff_day = strtotime($count_day . " days", $date);

		return $date_diff_day;
	}

	public function getMonthStr($datetime, $withoutEnd = false)
	{
		if ( !$date = strtotime($datetime) ) {
			throw new DateTimeException('DateTime error: failure datetime format "' . $datetime . '"', 801);
		}

		$month = (int)date('m', $date);
        $name = 'namesMonth';
        if ($withoutEnd) {
            $name = 'namesMonthWithoutEnd';
        }
		$monthStr = $this->_data[$name]['m' . $month];

		return $monthStr;
	}

    public function getMonthsList() {
        return $this->_data['namesMonthWithoutEnd'];
    }

    public function getShortMonthsList() {
        return $this->_data['shortNamesMonth'];
    }
}