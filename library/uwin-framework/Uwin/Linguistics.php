<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Linguistics, который отвечает за работу с текстом
 *
 * @category  Uwin
 * @package   Uwin\Layout
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 * @version   $Id$
 */

/**
 * Объявляем пространсто имен Uwin, к которому относится класс Linguistics
 */
namespace Uwin;

/**
 * Класс, который отвечает за работу с текстом
 *
 * @category  Uwin
 * @package   Uwin\Linguistics
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 */
class Linguistics
{
	/**
	 *
	 * @param unknown_type $string
	 */
	public function getTranslit($string)
	{
		$translit = array(
			'А'=>'A', 'Б'=>'B', 'В'=>'V', 'Г'=>'G',
			'Д'=>'D', 'Е'=>'E', 'Ё'=>'JO', 'Ж'=>'ZH', 'З'=>'Z', 'И'=>'I',
			'Й'=>'J', 'К'=>'K', 'Л'=>'L', 'М'=>'M', 'Н'=>'N',
			'О'=>'O', 'П'=>'P', 'Р'=>'R', 'С'=>'S', 'Т'=>'T',
			'У'=>'U', 'Ф'=>'F', 'Х'=>'KH', 'Ц'=>'C', 'Ч'=>'CH',
			'Ш'=>'SH', 'Щ'=>'SHH', 'Ъ'=>'', 'Ы'=>'Y', 'Ь'=>'',
			'Э'=>'E', 'Ю'=>'YU', 'Я'=>'YU',
			'а'=>'a', 'б'=>'b', 'в'=>'v', 'г'=>'g',
			'д'=>'d', 'е'=>'e', 'ё'=>'jo', 'ж'=>'zh', 'з'=>'z', 'и'=>'i',
			'й'=>'j', 'к'=>'k', 'л'=>'l', 'м'=>'m', 'н'=>'n',
			'о'=>'o', 'п'=>'p', 'р'=>'r', 'с'=>'s', 'т'=>'t',
			'у'=>'u', 'ф'=>'f', 'х'=>'kh', 'ц'=>'c', 'ч'=>'ch',
			'ш'=>'sh', 'щ'=>'ssh', 'ъ'=>'', 'ы'=>'y', 'ь'=>'',
			'э'=>'e', 'ю'=>'yu', 'я'=>'ya',
	    );

	    $result = strtr($string, $translit);

	    return $result;
	}

	public function replaceSpecSymbol($string)
	{
		$translit = array(
			'&quot;'=>'"', '&lt;'=>'<', '&larr;'=>'←',
			'&darr;'=>'↓', '&sect;'=>'§', '&laquo;'=>'«', '&ldquo;'=>'“',
			'&mdash;'=>'—', '&nbsp;'=>' ', '&copy;'=>'©', '&trade;'=>'™',
			'&deg;'=>'°', '&frac14;'=>'¼', '&frac12;'=>'½', '&Alpha;'=>'Α',
			'&Omega;'=>'Ω', '&prime;'=>'′', '&permil;'=>'‰', '&sup2;'=>'²',
			'&euro;'=>'€', '&yen;'=>'¥', '&amp;'=>'&', '&gt;'=>'>',
			'&rarr;'=>'→', '&uarr;'=>'↑', '&hellip;'=>'…', '&raquo;'=>'»',
			'&rdquo;'=>'”', '&ndash'=>'–', '&reg;'=>'®', '&plusmn;'=>'±',
			'&frac34;'=>'¾', '&times;'=>'×', '&alpha;'=>'α', '&omega;'=>'ω',
			'&Prime;'=>'″', '&divide;'=>'÷', '&asymp;'=>'≈', '&pound;'=>'£',
			'&cent;'=>'¢', '&lsquo;'=>'‘', '&rsquo;'=>'’', '&sbquo;'=>'‚',
			'&ldquo;'=>'“', '&rdquo;'=>'”', '&bdquo;'=>'„',
		);
	    $result = strtr($string, $translit);

	    return $result;
	}


	/**
	 *
	 * @param unknown_type $string
	 */
	public function getWebTranslit($string)
	{
		$result = $this->getTranslit($string);
		$result = strip_tags($result);
		$translit = array(
			' '=>'-', '/'=>'-', '_'=>'-', ', '=>'-', '. '=>'-',
			','=>'-', '.'=>'-',
			'&quot;'=>'', '&lt;'=>'-', '&larr;'=>'-',
			'&darr;'=>'-', '&sect;'=>'', '&laquo;'=>'', '&ldquo;'=>'',
			'&mdash;'=>'-', '&nbsp;'=>'-', '&copy;'=>'', '&trade;'=>'',
			'&deg;'=>'', '&frac14;'=>'', '&frac12;'=>'', '&Alpha;'=>'',
			'&Omega;'=>'', '&prime;'=>'', '&permil;'=>'', '&sup2;'=>'',
			'&euro;'=>'', '&yen;'=>'', '&amp;'=>'-', '&gt;'=>'-',
			'&rarr;'=>'-', '&uarr;'=>'-', '&hellip;'=>'-', '&raquo;'=>'',
			'&rdquo;'=>'', '&ndash'=>'-', '&reg;'=>'', '&plusmn;'=>'-',
			'&frac34;'=>'', '&times;'=>'-', '&alpha;'=>'', '&omega;'=>'',
			'&Prime;'=>'', '&divide;'=>'-', '&asymp;'=>'-', '&pound;'=>'',
			'&cent;'=>'',
			'"'=>'', '<'=>'-', '←'=>'-',
			'↓'=>'-', '§'=>'', '«'=>'', '“'=>'',
			'—'=>'-', ' '=>'-', '©'=>'', '™'=>'',
			'°'=>'', '¼'=>'', '½'=>'', 'Α'=>'',
			'Ω'=>'', '′'=>'', '‰'=>'', '²'=>'',
			'€'=>'', '¥'=>'', '&'=>'-', '>'=>'-',
			'→'=>'-', '↑'=>'-', '…'=>'-', '»'=>'',
			'”'=>'', '–'=>'-', '®'=>'', '±'=>'-',
			'¾'=>'', '×'=>'-', 'α'=>'', 'ω'=>'',
			'″'=>'', '÷'=>'-', '≈'=>'-', '£'=>'',
			'¢'=>'', '+'=>'plus'
		);
	    $result = strtr($result, $translit);
		$result = trim($result, " \t.,!?:-");
		if ( preg_match('/[^A-Za-z0-9_\-]/', $result) ) {
		    $result = preg_replace('/[^A-Za-z0-9_\-]/', '', $result);
		}
		$result = strtr( $result, array('--'=>'-', '---'=>'-', '----'=>'-', '-----'=>'-') );
		$result = trim($result, " \t.,!?:-");
		$result = strtolower($result);

		return $result;
	}

	public function shortedText($text, $length=2, $strip_tags = false) {
        if ($strip_tags) {
            $text = strip_tags($text);
        }
		$length = (int)$length;
		$lengthText = mb_strlen($text);

		if ( $length < $lengthText) {
			$positions = array(
				mb_strpos($text, ' ', $length),
				mb_strpos($text, ',', $length),
				mb_strpos($text, '.', $length),
				mb_strpos($text, '!', $length),
				mb_strpos($text, '?', $length),
				mb_strpos($text, ';', $length),
				mb_strpos($text, '-', $length),
				mb_strpos($text, ';', $length),
				mb_strpos($text, '&', $length),
				mb_strpos($text, "\n", $length),
				);

			foreach ($positions as $key => $value) {
				if (false === $value) {
					unset($positions[$key]);
				}
			}

      		if ( empty($positions) ) {
        		return $text;
      		}

			$length = min($positions);
			if (false !== $length) {
				$text = mb_substr($text, 0, $length) . '...';
			}
		}

		return $text;
	}

/**
 * Сумма прописью
 *
 * @param float $inn
 * @param bool $stripkop
 *
 * @return closure
 */
public function num2str($inn, $stripkop = false) {
    $nol = 'нуль';

    $str[100]= array('','сто','двісті','триста','чотириста','п&#39;ятсот','шістсот', 'сімсот', 'вісімсот','дев&#39;ятсот');
    $str[11] = array('','десять','одинадцять','дванадцять','тринадцять', 'чотирнадцять','п&#39;ятнадцять','шістнадцять','сімнадцять', 'вісімнадцять','дев&#39;ятнадцять','двадцять');
    $str[10] = array('','десять','двадцять','тридцять','сорок','п&#39;ятьдесят', 'шістдесят','сімдесят','вісімдесят','дев&#39;яносто');

    $sex = array(
		array('','одна','дві','три','чотири','п&#39;ять','шість','сім', 'вісім','дев&#39;ять'), // f
        array('','одна','дві','три','чотири','п&#39;ять','шість','сім', 'вісім','дев&#39;ять') // f
    );

    $forms = array(
        array('копійка', 'копійки', 'копійок', 1), // 10^-2
        array('гривня', 'гривні', 'гривень',  0), // 10^ 0
        array('тисяча', 'тисячі', 'тисяч', 1), // 10^ 3
        array('мільйон', 'мільйони', 'мільйонів',  0), // 10^ 6
        array('мільярд', 'мільярди', 'мільярдів',  0), // 10^ 9
        array('трильйон', 'трильйони', 'трильйонів',  0), // 10^12
    );

    $out = $tmp = array();

    // Поехали!
    $tmp = explode('.', str_replace(',','.', $inn));
    $rub = number_format($tmp[ 0], 0,'','-');
    if ($rub== 0) $out[] = $nol;

    // нормализация копеек
    $kop = isset($tmp[1]) ? substr(str_pad($tmp[1], 2, '0', STR_PAD_RIGHT), 0,2) : '00';
    $segments = explode('-', $rub);
    $offset = sizeof($segments);

    if ((int)$rub== 0) { // если 0 рублей
        $o[] = $nol;
        $o[] = $this->_morph( 0, $forms[1][ 0],$forms[1][1],$forms[1][2]);
    } else {
        foreach ($segments as $k=>$lev) {
            $sexi= (int) $forms[$offset][3]; // определяем род
            $ri = (int) $lev; // текущий сегмент
            if ($ri== 0 && $offset>1) {// если сегмент==0 & не последний уровень(там Units)
                $offset--;
                continue;
            }
            // нормализация
            $ri = str_pad($ri, 3, '0', STR_PAD_LEFT);
            // получаем циферки для анализа
            $r1 = (int)substr($ri, 0,1); //первая цифра
            $r2 = (int)substr($ri,1,1); //вторая
            $r3 = (int)substr($ri,2,1); //третья
            $r22= (int)$r2.$r3; //вторая и третья

            // разгребаем порядки
            if ($ri>99) $o[] = $str[100][$r1]; // Сотни
            if ($r22>20) {// >20
                $o[] = $str[10][$r2];
                $o[] = $sex[ $sexi ][$r3];
            } else { // <=20
                if ($r22>9) $o[] = $str[11][$r22-9]; // 10-20
                elseif($r22> 0) $o[] = $sex[ $sexi ][$r3]; // 1-9
            }

            // Рубли
            $o[] = $this->_morph($ri, $forms[$offset][ 0],$forms[$offset][1],$forms[$offset][2]);
            $offset--;
        }
    }

    // Копейки
    if (!$stripkop) {
        $o[] = $kop;
        $o[] = $this->_morph($kop,$forms[ 0][ 0],$forms[ 0][1],$forms[ 0][2]);
    }

    return preg_replace("/\s{2,}/",' ',implode(' ',$o));
}


/**
 * Склоняем словоформу
 *
 * @param $n
 * @param $f1
 * @param $f2
 * @param $f5
 * @return
 */
private function _morph($n, $f1, $f2, $f5) {
    $n = abs($n) % 100;
    $n1= $n % 10;

    if ($n>10 && $n<20) return $f5;
    if ($n1>1 && $n1<5) return $f2;
    if ($n1==1) return $f1;

    return $f5;
}
}
