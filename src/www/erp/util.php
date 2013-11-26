<?

namespace ZippyERP\ERP;

use \ZCL\DB\DB;

/**
 * Класс   со  вспомагательными   функциями
 *   для  работы с  БД 
 */
class Util
{

        public function num2str($num)
        {
                $nul = 'ноль';
                $ten = array(
                    array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
                    array('', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
                );
                $a20 = array('десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать');
                $tens = array(2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
                $hundred = array('', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот');
                $unit = array(// Units
                    array('копейка', 'копейки', 'копеек', 1),
                    array('рубль', 'рубля', 'рублей', 0),
                    array('тысяча', 'тысячи', 'тысяч', 1),
                    array('миллион', 'миллиона', 'миллионов', 0),
                    array('миллиард', 'милиарда', 'миллиардов', 0),
                );
                //
                list($rub, $kop) = explode('.', sprintf("%015.2f", floatval($num)));
                $out = array();
                if (intval($rub) > 0) {
                        foreach (str_split($rub, 3) as $uk => $v) { // by 3 symbols
                                if (!intval($v))
                                        continue;
                                $uk = sizeof($unit) - $uk - 1; // unit key
                                $gender = $unit[$uk][3];
                                list($i1, $i2, $i3) = array_map('intval', str_split($v, 1));
                                // mega-logic
                                $out[] = $hundred[$i1]; # 1xx-9xx
                                if ($i2 > 1)
                                        $out[] = $tens[$i2] . ' ' . $ten[$gender][$i3];# 20-99
                                else
                                        $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3];# 10-19 | 1-9
                                // units without rub & kop
                                if ($uk > 1)
                                        $out[] = morph($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
                        } //foreach
                }
                else
                        $out[] = $nul;
                $out[] = morph(intval($rub), $unit[1][0], $unit[1][1], $unit[1][2]); // rub
                $out[] = $kop . ' ' . morph($kop, $unit[0][0], $unit[0][1], $unit[0][2]); // kop
                return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
        }

        /**
         * Склоняем словоформу
         * @ author runcore
         */
        function morph($n, $f1, $f2, $f5)
        {
                $n = abs(intval($n)) % 100;
                if ($n > 10 && $n < 20)
                        return $f5;
                $n = $n % 10;
                if ($n > 1 && $n < 5)
                        return $f2;
                if ($n == 1)
                        return $f1;
                return $f5;
        }

}

// Convert digital Russian currency representation
// (Russian rubles and copecks) to the verbal one
// Copyright 2008 Sergey Kurakin
// Licensed under LGPL version 3 or later

define('M2S_KOPS_DIGITS', 0x01);    // digital copecks
define('M2S_KOPS_MANDATORY', 0x02);    // mandatory copecks
define('M2S_KOPS_SHORT', 0x04);    // shorten copecks

function money2str_ru($money, $options = 0)
{

        $money = preg_replace('/[\,\-\=]/', '.', $money);

        $numbers_m = array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь',
            'восемь', 'девять', 'десять', 'одиннадцать', 'двенадцать', 'тринадцать',
            'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать',
            'девятнадцать', 'двадцать', 30 => 'тридцать', 40 => 'сорок', 50 => 'пятьдесят',
            60 => 'шестьдесят', 70 => 'семьдесят', 80 => 'восемьдесят', 90 => 'девяносто',
            100 => 'сто', 200 => 'двести', 300 => 'триста', 400 => 'четыреста',
            500 => 'пятьсот', 600 => 'шестьсот', 700 => 'семьсот', 800 => 'восемьсот',
            900 => 'девятьсот');

        $numbers_f = array('', 'одна', 'две');

        $units_ru = array(
            (($options & M2S_KOPS_SHORT) ? array('коп.', 'коп.', 'коп.') : array('копейка', 'копейки', 'копеек')),
            array('рубль', 'рубля', 'рублей'),
            array('тысяча', 'тысячи', 'тысяч'),
            array('миллион', 'миллиона', 'миллионов'),
            array('миллиард', 'миллиарда', 'миллиардов'),
            array('триллион', 'триллиона', 'триллионов'),
        );

        $ret = '';

        // enumerating digit groups from left to right, from trillions to copecks
        // $i == 0 means we deal with copecks, $i == 1 for roubles,
        // $i == 2 for thousands etc.
        for ($i = sizeof($units_ru) - 1; $i >= 0; $i--) {

                // each group contais 3 digits, except copecks, containing of 2 digits
                $grp = ($i != 0) ? dec_digits_group($money, $i - 1, 3) :
                        dec_digits_group($money, -1, 2);

                // process the group if not empty
                if ($grp != 0) {

                        // digital copecks
                        if ($i == 0 && ($options & M2S_KOPS_DIGITS)) {
                                $ret .= sprintf('%02d', $grp) . ' ';
                                $dig = $grp;

                                // the main case
                        }
                        else
                                for ($j = 2; $j >= 0; $j--) {
                                        $dig = dec_digits_group($grp, $j);
                                        if ($dig != 0) {

                                                // 10 to 19 is a special case
                                                if ($j == 1 && $dig == 1) {
                                                        $dig = dec_digits_group($grp, 0, 2);
                                                        $ret .= $numbers_m[$dig] . ' ';
                                                        break;
                                                }

                                                // thousands and copecks are Feminine gender in Russian
                                                elseif (($i == 2 || $i == 0) && $j == 0 && ($dig == 1 || $dig == 2))
                                                        $ret .= $numbers_f[$dig] . ' ';

                                                // the main case
                                                else
                                                        $ret .= $numbers_m[(int) ($dig * pow(10, $j))] . ' ';
                                        }
                                }
                        $ret .= $units_ru[$i][sk_plural_form($dig)] . ' ';
                }

                // roubles should be named in case of empty roubles group too
                elseif ($i == 1 && $ret != '')
                        $ret .= $units_ru[1][2] . ' ';

                // mandatory copecks
                elseif ($i == 0 && ($options & M2S_KOPS_MANDATORY))
                        $ret .= (($options & M2S_KOPS_DIGITS) ? '00' : 'ноль') .
                                ' ' . $units_ru[0][2];
        }

        return trim($ret);
}

// service function to select the group of digits
function dec_digits_group($number, $power, $digits = 1)
{
        return (int) bcmod(bcdiv($number, bcpow(10, $power * $digits, 8)), bcpow(10, $digits, 8));
}

// service function to get plural form for the number
function sk_plural_form($d)
{
        $d = $d % 100;
        if ($d > 20)
                $d = $d % 10;
        if ($d == 1)
                return 0;
        elseif ($d > 0 && $d < 5)
                return 1;
        else
                return 2;
}

class Num2rub
{

        public $def = array(
            'form' => array('1' => 0, '2' => 1, '1f' => 0, '2f' => 1, '3' => 1, '4' => 1),
            'rank' => array(
                0 => array('рубль', 'рубля', 'рублей', 'f' => ''),
                1 => array('тысяча', 'тысячи', 'тысяч', 'f' => 'f'),
                2 => array('миллион', 'миллиона', 'миллионов', 'f' => ''),
                3 => array('миллиард', 'миллиарда', 'миллиардов', 'f' => ''),
                'k' => array('копейка', 'копейки', 'копеек', 'f' => 'f')
            ),
            'words' => array(
                '0' => array('', 'десять', '', ''),
                '1' => array('один', 'одиннадцать', '', 'сто'),
                '2' => array('два', 'двенадцать', 'двадцать', 'двести'),
                '1f' => array('одна', '', '', ''),
                '2f' => array('две', '', '', ''),
                '3' => array('три', 'тринадцать', 'тридцать', 'триста'),
                '4' => array('четыре', 'четырнадцать', 'сорок', 'четыреста'),
                '5' => array('пять', 'пятнадцать', 'пятьдесят', 'пятьсот'),
                '6' => array('шесть', 'шестнадцать', 'шестьдесят', 'шестьсот'),
                '7' => array('семь', 'семнадцать', 'семьдесят', 'семьсот'),
                '8' => array('восемь', 'восемнадцать', 'восемьдесят', 'восемьсот'),
                '9' => array('девять', 'девятнадцать', 'девяносто', 'девятьсот')
            )
        );

        public static function doit($str)
        {
                $num2rub = new Num2rub();

                $str = number_format($str, 2, '.', ',');
                $rubkop = explode('.', $str);
                $rub = $rubkop[0];
                $kop = (isset($rubkop[1])) ? $rubkop[1] : '00';
                $rub = (strlen($rub) == 1) ? '0' . $rub : $rub;
                $rub = explode(',', $rub);
                $rub = array_reverse($rub);

                $word = array();
                $word[] = $num2rub->dvig($kop, 'k', false);
                foreach ($rub as $key => $value) {
                        if (intval($value) > 0 || $key == 0) //подсказал skrabus
                                $word[] = $num2rub->dvig($value, $key);
                }

                $word = array_reverse($word);
                return ucfirst(trim(implode(' ', $word)));
        }

        public function dvig($str, $key, $do_word = true)
        {
                $def = & $this->def;
                $words = $def['words'];
                $form = $def['form'];

                if (!isset($def['rank'][$key]))
                        return '!razriad';
                $rank = $def['rank'][$key];
                $sotni = '';
                $word = '';
                $num_word = '';

                $str = (strlen($str) == 1) ? '0' . $str : $str;
                $dig = str_split($str);
                $dig = array_reverse($dig);

                if (1 == $dig[1]) {
                        $num_word = ($do_word) ? $words[$dig[0]][1] : $dig[1] . $dig[0];
                        $word = $rank[2];
                } else {
                        //$rank[3] - famale
                        if ($dig[0] != 1 && $dig[0] != 2)
                                $rank['f'] = '';
                        $num_word = ($do_word) ? $words[$dig[1]][2] . ' ' . $words[$dig[0] . $rank['f']][0] : $dig[1] . $dig[0];
                        $key = (isset($form[$dig[0]])) ? $form[$dig[0]] : false;
                        $word = ($key !== false) ? $rank[$key] : $rank[2];
                }

                $sotni = (isset($dig[2])) ? (($do_word) ? $words[$dig[2]][3] : $dig[2]) : '';
                if ($sotni && $do_word)
                        $sotni .= ' ';

                return $sotni . $num_word . ' ' . $word;
        }

//function dvig
}

//class Num2rub() 