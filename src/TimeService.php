<?php

namespace Times;


class TimeService
{
    /**
     * 获取间隔的 1：年数-月数-天数、2：总天数
     * @param $start
     * @param $end
     * @return \DateInterval|false
     */
    public static function getDiscTimes($start, $end)
    {
        $start_stamp = strtotime($start);
        $end_stamp = strtotime($end);
        return date_diff(date_create(date('Ymd', $start_stamp)), date_create(date('Ymd', $end_stamp)));
    }

    // 年

    /**
     * 获取年度列表
     * @param $start
     * @param $end
     * @return array
     */
    public static function getYearList($start, $end)
    {
        $temp_year = $start_year = intval(date('Y', strtotime($start)));
        $end_year = intval(date('Y', strtotime($end)));
        $list = [];
        while ($temp_year <= $end_year) {
            $list[] = $temp_year;
            $temp_year++;
        }
        return $list;
    }

    // 季

    /**
     * 获取年份季度
     * @param $date
     * @return array
     */
    public static function getDateQuarter($date)
    {
        list($year, $month) = explode("-", date('Y-n', strtotime($date)));
        $quarter = intval($month / 3);
        if ($month % 3 != 0) {
            $quarter += 1;
        }
        return [$year, $quarter];
    }

    /**
     * 季度列表
     * @param $start_time
     * @param $end_time
     * @return array
     */
    public static function getQuarterList($start_time, $end_time)
    {
        $month_list = self::getMonthList($start_time, $end_time, 1);

        foreach ($month_list as $k => $v) {
            $start_quarter = self::getDateQuarter($v);
            $q_Listr = $start_quarter[0] . '-' . $start_quarter[1];
            $Quarter_list[] = $q_Listr;
        }
        $Quarter_list = array_unique($Quarter_list);
        return $Quarter_list;
    }

    // 月

    /**
     * 季度首月
     * @param $quarter
     * @return int
     */
    public static function getQuarterStartMonth($quarter)
    {
        return intval($quarter * 3 - 2);
    }

    /**
     * 获取月度列表
     * @param $start
     * @param $end
     * @param int $withYear
     * @return array
     */
    public static function getMonthList($start, $end, $withYear = 0)
    {
        $start_date = date('Y-m', strtotime($start));
        $end_date = date('Y-m', strtotime($end));
        list($start_year, $temp_month) = explode('-', $start_date);
        $temp_year = $start_year;
        list($end_year, $end_month) = explode('-', $end_date);
        $list = [];
        while ($temp_year <= $end_year) {
            if ($temp_year == $start_year) {
                $cache_start_month = intval($temp_month);
            } else {
                $cache_start_month = 1;
            }
            if ($temp_year == $end_year) {
                $cache_end_month = intval($end_month);
            } else {
                $cache_end_month = 12;
            }
            while ($cache_start_month <= $cache_end_month) {
                if (empty($withYear)) {
                    $list[] = "{$cache_start_month}月";
                } else {
                    $list[] = "{$temp_year}-{$cache_start_month}";
                }
                $cache_start_month++;
            }
            $temp_year++;
        }
        return $list;
    }

    /**
     * 获取间隔月数
     * @param $start
     * @param $end
     * @return float|int
     */
    public static function getDiscMonth($start, $end)
    {
        $start_stamp = strtotime($start);
        $end_stamp = strtotime($end);
        list($date_start['y'], $date_start['m']) = explode("-", date('Y-m', $start_stamp));
        list($date_end['y'], $date_end['m']) = explode("-", date('Y-m', $end_stamp));
        return abs($date_start['y'] - $date_end['y']) * 12 + $date_end['m'] - $date_start['m'];
    }


    // 天

    /**
     * 获取天 列表
     * @param $start_time
     * @param $end_time
     * @return array
     */
    public static function getDaysList($start_time, $end_time)
    {
        $days = self::getDiscDays($start_time, $end_time);
        $days_list[] = date("Y-m-d", strtotime($start_time));
        for ($i = 1; $i <= $days; $i++) {
            $days_list[] = date("Y-m-d", strtotime("+" . $i . "days", strtotime($start_time)));
        }
        return $days_list;
    }


    public static function initShowDate($show_date, $method = 0)
    {
        if (empty($show_date)) {
            return "";
        }
        if ($method == 1) {
            return !empty($show_date) ? "{$show_date}-01" : '';
        }
        if ($method == 2) {
            return !empty($show_date) ? date("Y-m-d 23:59:59", strtotime($show_date)) : '';
        }
        return !empty($show_date) ? $show_date : '';
    }

    /**
     * 获取当月天数
     * @param $date
     * @return int
     */
    public static function getMonthDays($date)
    {
        list($year, $month) = explode("-", date('Y-m', strtotime($date)));
        if (in_array($month, [1, 3, 5, 7, 8, 10, 12])) {
            return 31;
        } elseif (in_array($month, [4, 6, 9, 11])) {
            return 30;
        }
        if ($year % 400 == 0 || ($year % 4 == 0 && $year % 100 > 0)) {
            return 29;
        }
        return 28;
    }

    /**
     * 获取当月最后一天
     * @param $date
     * @return false|string
     */
    public static function getMonthLastDay($date)
    {
        $last_day = self::getMonthDays($date);
        return date("Y-m-{$last_day}", strtotime($date));
    }

    /**
     * 获取下月第一天
     * @param $date
     * @return false|string
     */
    public static function getNextMonthStart($date)
    {
        $last_day = self::getMonthLastDay($date);
        return date("Y-m-01", strtotime("+1 day", strtotime($last_day)));
    }

    /**
     * 获取间隔天数
     * @param $start
     * @param $end
     * @return float|int
     */
    public static function getDiscDays($start, $end)
    {
        $start_stamp = strtotime($start);
        $end_stamp = strtotime($end);
        return intval(($end_stamp - $start_stamp) / 86400);
    }

    /**
     * 获取指定日期段内每一天的日期
     * @param $start_date 开始日期
     * @param $end_date 结束日期
     * @return array
     */
    public static function getDayList($start_date, $end_date)
    {
        $start_times_tamp = strtotime($start_date);
        $end_times_tamp = strtotime($end_date);

        // 计算日期段内有多少天
        $days = ($end_times_tamp - $start_times_tamp) / 86400 + 1;

        // 保存每天日期
        $date = array();

        for ($i = 0; $i < $days; $i++) {
            $date[] = date('Y-m-d', $start_times_tamp + (86400 * $i));
        }
        return $date;
    }

    // 小时

    /**
     * Desc: 小时列表
     * @param $start
     * @param $end
     * @return array
     */
    public static function getHours($start, $end)
    {
        $list = [];
        for ($i = $start; $i <= $end; $i++) {
            if ($i <= 9) {
                $list[] = '0' . $i . ':00';
            } else {
                $list[] = $i . ':00';
            }
        }
        return $list;
    }
}