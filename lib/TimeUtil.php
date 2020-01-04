<?php

/**
 * Created by PhpStorm.
 * User: louis
 * Date: 16/8/21
 * Time: 上午11:29
 */
class TimeUtil
{
    /**
     * desc    获取微妙
     * @return int
     */
    public static function microTime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return intval(((float)$usec + (float)$sec) * 1000000);
    }
}