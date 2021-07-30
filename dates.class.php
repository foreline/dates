<?php
/**
 * Работа с датами
 *
 * @package dates
 * @author dima@foreline.ru
 */

class Dates {

    /** @var array $months Месяцы */
    static public array $months = [
        '01'    => 'января',
        '02'    => 'февраля',
        '03'    => 'марта',
        '04'    => 'апреля',
        '05'    => 'мая',
        '06'    => 'июня',
        '07'    => 'июля',
        '08'    => 'августа',
        '09'    => 'сентября',
        '10'    => 'октября',
        '11'    => 'ноября',
        '12'    => 'декабря',
    ];

    /** @var array $arMonths Месяцы в именительном падеже */
    static public array $arMonths = [
        '1'     => 'январь',
        '01'    => 'январь',
        '2'     => 'февраль',
        '02'    => 'февраль',
        '3'     => 'март',
        '03'    => 'март',
        '4'     => 'апрель',
        '04'    => 'апрель',
        '5'     => 'май',
        '05'    => 'май',
        '6'     => 'июнь',
        '06'    => 'июнь',
        '7'     => 'июль',
        '07'    => 'июль',
        '8'     => 'август',
        '08'    => 'август',
        '9'     => 'сентябрь',
        '09'    => 'сентябрь',
        '10'    => 'октябрь',
        '11'    => 'ноябрь',
        '12'    => 'декабрь',
    ];

    /** @var array $weekDays Дни недели */
    public static array $weekDays = [
        0   => 'вс',
        1   => 'пн',
        2   => 'вт',
        3   => 'ср',
        4   => 'чт',
        5   => 'пт',
        6   => 'сб',
    ];

    /**
     * Парсит строку с датой и возвращает число,
     * представляющее собой количество секунд,
     * истекших с полуночи 1 января 1970 года GMT+0 до даты, указанной в строке
     *
     * @param string $date Строка с датой
     * @param bool $recursion
     * @return int|bool $timestamp
     */

    static public function parse(string $date = '', bool $recursion = false)
    {
        if ( 8 > strlen($date) ) {
            return false;
        }

        $date = trim($date);

        /// 31.01/2001
        //$pattern = '#^[0-3]{1}[1-9]{1}.[0-9]{2}.[0-9]{4}#';
        $pattern = '#[0-9]{2}.[0-9]{2}.[0-9]{4}#';
        //$pattern = '#^[0-9]{2}.[0-9]{2}.[0-9]{4}$#xismU';
        //$pattern = '#^[0-3]{1}[0-9]{1}.[0-1]{1}[0-9]{1}.[0-9]{4}#';

        /// 2001-01.31
        //$pattern1 = '#^[0-9]{4}.[0-9]{2}.[0-3]{1}[1-9]{1}#';
        $pattern1 = '#[0-9]{4}.[0-9]{2}.[0-9]{2}#';
        //$pattern1 = '#^[0-9]{4}.[0-9]{2}.[0-9]{2}$#ximsU';
        //$pattern1 = '#^[0-9]{4}.[0-1]{1}[0-9]{1}.[0-3]{1}[0-9]{1}#';

        //$pattern3 = '#^([0-9]{2,4}).([0-9]{2}).([0-9]{2,4})#';
        $pattern3 = '#^([0-9]{1,4}).([0-9]{1,2}).([0-9]{1,4})#';


        // 5/16/2013 2:58:18 PM
        $pattern4 = '#[0-9]{1,4}[./-][0-9]{1,2}[./-][0-9]{2,4}([^0-9][0-9]{1,2}:[0-9]{2}:[0-9]{2})*#';

        if ( preg_match($pattern, $date) ) {
            $day    = substr($date,0,2);
            $month  = substr($date,3,2);
            $year   = substr($date,6,4);
        } else if ( preg_match($pattern1, $date) ) {
            $year   = substr($date,0,4);
            $month  = substr($date,5,2);
            $day    = substr($date,8,2);
        } else if ( preg_match($pattern3, $date, $matches) && !$recursion ) {

            $nDate =    ( 10 > intval($matches[1]) ? '0' . intval($matches[1]) : intval($matches[1]) )
                . '.' .
                ( 10 > intval($matches[2]) ? '0' . intval($matches[2]) : intval($matches[2]) )
                . '.' .
                ( 10 > intval($matches[3]) ? '0' . intval($matches[3]) : intval($matches[3]) );
            return Dates::parse($nDate, true);
        } else if ( preg_match($pattern4, $date, $matches)) {
            /** @TODO */
            return false;
        } else {
            return false;
        }

        if ( 19 == strlen($date) ) {
            $hour   = substr($date, 11, 2);
            $min    = substr($date, 14,2);
            $sec    = substr($date, 17,2);
        } else {
            $hour   = 0;
            $min    = 0;
            $sec    = 0;
        }

        return mktime($hour, $min, $sec, $month, $day, $year);
    }

    /**
     * Возвращает разницу в днях между двумя датами
     *
     * @param string $dateFrom Дата "с"
     * @param string $dateTo Дата "по"
     *
     * @return int $fullDays Количество полных дней между датами
     */

    static public function dateDiff(string $dateFrom, string $dateTo): int
    {
        $timeStampFrom  = Dates::parse($dateFrom);
        $timeStampTo    = Dates::parse($dateTo);

        $dateDiff = $timeStampTo - $timeStampFrom;

        return floor($dateDiff/(60*60*24));
    }

    /**
     * Возвращает разницу в днях между заданными датами. Разницей считается количество переходов через полночь.
     *
     * @param string $dateFrom Дата с
     * @param string $dateTo Дата по
     *
     * @return int $daysDiff
     */

    static public function daysDiff(string $dateFrom = '', string $dateTo = ''): int
    {
        $tsDateFrom = dates::parse($dateFrom);
        $tsDateTo   = dates::parse($dateTo);

        if ( date('z', $tsDateFrom) == date('z', $tsDateTo) && (86400 > $tsDateTo - $tsDateFrom) ) {
            return 0;
        }

        return ceil( abs(($tsDateTo - $tsDateFrom)/(24*60*60)) );
    }

    /**
     * Возвращает разницу в минутах между датами
     *
     * @param string $dateFrom дата "с"
     * @param string $dateTo дата "по"
     *
     * @return int $minDiff Разница в минутах между датами
     */

    static public function minDiff(string $dateFrom, string $dateTo): int
    {
        $timeStampFrom  = Dates::parse($dateFrom);
        $timeStampTo    = Dates::parse($dateTo);

        $dateDiff = $timeStampTo - $timeStampFrom;

        return floor($dateDiff/60);
    }

    /**
     * Возвращает разницу в секундах между двумя датами
     *
     * @param string $dateFrom дата "с"
     * @param string $dateTo дата "по"
     *
     * @return int $secDiff Разница в секундах между датами
     */

    static public function secDiff(string $dateFrom, string $dateTo): int
    {
        $timeStampFrom  = Dates::parse($dateFrom);
        $timeStampTo    = Dates::parse($dateTo);

        return (int) ( $timeStampTo - $timeStampFrom );
    }

    /**
     * Выводит отформатированную дату "сегодня в 7:00 | вчера в 19:35 | 1 сентября"
     * Время выводится только для "сегодня" и "вчера", если задано
     *
     * @param string $date исходная дата
     * @param bool $today [optional] Заменять ли дату на "сегодня" и "вчера", по умолчанию true
     * @param bool $weekDays [optional] Выводить ли дополнительно дни недели: 14 января, пн, по умолчанию false
     * @param bool $showMonth [optional] Выводить ли название месяца, по умолчанию true
     *
     * @return string $formatedDate отформатированная дата
     */

    static public function dateFormat(string $date, bool $today = true, bool $weekDays = false, bool $showMonth = true): string
    {
        if ( empty($date) ) {
            return '';
        }

        $timeStamp = dates::parse($date);

        $day    = date('j', $timeStamp);
        $month  = date('m', $timeStamp);
        $year   = date('Y', $timeStamp);

        // Определяем сегодняшнее ли это дата/время
        $isToday = false;
        $todayTimeStampStart = dates::parse(date('Y.m.d 00:00:00'));
        $todayTimeStampEnd = dates::parse(date('Y.m.d 23:59:59'));

        if ( $todayTimeStampStart < $timeStamp && $timeStamp < $todayTimeStampEnd ) {
            $isToday = true;
        }

        // Определяем вчерашние ли это дата/время
        $isYesterday = false;
        $yesterdayTimeStampStart = ( dates::parse(date('Y.m.d 00:00:00')) - 86400 );
        $yesterdayTimeStampEnd = ( dates::parse(date('Y.m.d 23:59:59')) - 86400 );

        if ( $yesterdayTimeStampStart < $timeStamp && $timeStamp < $yesterdayTimeStampEnd ) {
            $isYesterday = true;
        }

        /*
         * Форматируем вывод
         */

        if ( TRUE === $today && TRUE === $isToday ) {
            // сегодня
            $formatedDate = '<span>сегодня</span>';
        } else if ( TRUE === $today && TRUE === $isYesterday ) {
            // вчера, в 18:16
            $formatedDate = '<span>вчера</span>';
        } else {
            $formatedDate = $day . ($showMonth ? '&nbsp;' . dates::$months[$month] : '') . (date('Y') != $year ? ' ' . $year : '');
        }

        if ( TRUE === $weekDays ) {
            $formatedDate .= ', ' . dates::$weekDays[date('w', $timeStamp)];
        }

        return $formatedDate;
    }

    /**
     * Выводит отформатированную дату "сегодня в 7:00 | вчера в 19:35 | 1 сентября, 11:19"
     * Время выводится, если задано
     *
     * @param string $date исходная дата
     * @param bool $today Заменять ли дату на "сегодня" и "вчера"
     * @param bool $weekDays Выводить ли дополнительно дни недели: 14 января 15:35, пн
     *
     * @return string $formatedDate
     */

    static public function dateTimeFormat(string $date, bool $today = true, bool $weekDays = false): string
    {
        if ( empty($date) ) {
            return '';
        }

        $timeStamp = dates::parse($date);

        $day    = date('j', $timeStamp);
        $month  = date('m', $timeStamp);
        $year   = date('Y', $timeStamp);
        $hour   = date('H', $timeStamp);
        $min    = date('i', $timeStamp);
        //$sec    = date('s', $timeStamp);

        /*
         *
         */

        //$dateDiff = dates::dateDiff($date, date('d.m.Y H:i:s'));

        // Определяем сегодняшнее ли это дата/время
        $isToday = false;
        $todayTimeStampStart = dates::parse(date('Y.m.d 00:00:00'));
        $todayTimeStampEnd = dates::parse(date('Y.m.d 23:59:59'));

        if ( $todayTimeStampStart < $timeStamp && $timeStamp < $todayTimeStampEnd ) {
            $isToday = true;
        }

        // Определяем вчерашние ли это дата/время
        $isYesterday = false;
        $yesterdayTimeStampStart = ( dates::parse(date('Y.m.d 00:00:00')) - 86400 );
        $yesterdayTimeStampEnd = ( dates::parse(date('Y.m.d 23:59:59')) - 86400 );

        if ( $yesterdayTimeStampStart < $timeStamp && $timeStamp < $yesterdayTimeStampEnd ) {
            $isYesterday = true;
        }

        /*
         * Форматируем вывод
         */

        if ( TRUE === $today && TRUE === $isToday ) {
            // сегодня, в 18:16
            $formatedDate = '<span>сегодня</span>' . ( 0 < strlen($hour) && 0 < strlen($min) ? ' в&nbsp;' . $hour . ':' . $min : '');
        } else if ( TRUE === $today && TRUE === $isYesterday ) {
            // вчера, в 18:16
            $formatedDate = '<span>вчера</span>' . ( 0 < strlen($hour) && 0 < strlen($min) ? ' в&nbsp;' . $hour . ':' . $min : '');
        } else {
            $formatedDate = $day . '&nbsp;' . Dates::$months[$month] . (date('Y') != $year ? ' ' . $year : '') . ( !empty($hour)&&!empty($min) ? ', '  . $hour . ':' . $min : '');
        }

        if ( TRUE === $weekDays ) {
            $formatedDate .= ', ' . dates::$weekDays[date('w', $timeStamp)];
        }

        return $formatedDate;
    }

    /**
     * Возвращает время в формате чч:мм из минут
     *
     * @param int $minutes минуты
     * @return string $timeFormat
     */

    static public function timeFromMinutes(int $minutes, $showDays = false): string
    {
        if ( 0 >= $minutes ) {
            return '00:00';
        }

        $hours  = floor($minutes/60);
        $min    = round($minutes - ($hours * 60));

        $output = '';

        if ( TRUE === $showDays ) {

            if ( 24 <= $hours ) {
                $days = floor($hours/24);
                $hours = $hours - 24*$days;
            } else {
                $days = 0;
            }

            if ( 0 < $days ) {
                $output .= $days . 'дн.&nbsp;';
            }
        }

        $output .= (10 > $hours ? '0' : '') . $hours . ':' . (10 > $min ? '0' : '') . $min;

        return $output;
    }

    /**
     * Возвращает время в формате мм:сс из секунд
     *
     * @param int $seconds минуты
     * @return string $timeFormat
     */

    static public function timeFromSeconds(int $seconds, $showHours = false): string
    {
        if ( 0 >= $seconds ) {
            return '00:00';
        }

        $min    = floor($seconds/60);
        $sec    = round($seconds - ($min * 60));

        $output = '';

        if ( TRUE === $showHours ) {

            if ( 60 <= $min ) {
                $hours = floor($min/60);
                $min = $min - floor($hours*60);
            } else {
                $hours = 0;
            }

            if ( 0 < $hours ) {
                $output .= $hours . 'ч.&nbsp;';
            }
        }

        $output .= '<span title="ч. мм:сс">' . (10 > $min ? '0' : '') . $min . ':' . (10 > $sec ? '0' : '') . $sec . '</span>';

        return $output;
    }

    /**
     * Тестирование различных форматов дат
     */
    static public function test() {

        /*
        // ДД.ММ.ГГГГ
        $date = '31.01.2001';
        $date = '31/01/2001';
        $date = '31-01-2001';

        // ДД.М.ГГГГ
        $date = '31/1/2001';
        $date = '31.1.2001';
        $date = '31-1-2001';

        // Д.М.ГГГГ
        $date = '1/1/2001';
        $date = '1.1.2001';
        $date = '1-1-2001';

        // ГГГГ.ММ.ДД
        $date = '1991.12.31';
        $date = '1991/12/31';
        $date = '1991-12-31';

        // ГГГГ.ММ.Д
        $date = '1991.12.1';
        $date = '1991/12/1';
        $date = '1991-12-1';

        // ГГГГ.М.Д
        $date = '1991.2.1';
        $date = '1991/2/1';
        $date = '1991-2-1';

        // ГГГГ.М.ДД
        $date = '1991.2.28';
        $date = '1991/2/28';
        $date = '1991-2-28';

        // ДД.ММ.ГГ
        $date = '31.01.98';
        $date = '31/01/98';
        $date = '31-01-98';
        */
    }

}
