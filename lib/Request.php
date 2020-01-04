<?php

/**
 * Created by PhpStorm.
 * User: louis
 * Date: 16/8/14
 * Time: 下午3:57
 */
class Request
{
    /**
     * desc    获取请求方式
     * @return mixed
     */
    public static function getMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * desc   判断是否指定请求方式
     * @param $method
     * @return bool
     */
    public static function isMethod($method)
    {
        return self::getMethod() == $method;
    }

    /**
     * desc    判断请求是否来自ajax
     * @return bool
     */
    public static function fromAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }



}