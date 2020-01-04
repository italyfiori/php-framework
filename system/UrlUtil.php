<?php

/**
 * Created by PhpStorm.
 * User: louis
 * Date: 16/6/30
 * Time: 下午11:39
 */
class UrlUtil
{
    /**
     * desc 获取当前url路径, 去除index.php前缀和query
     * @return string
     */
    static function getUrlPath()
    {
        $path = preg_replace('/\?.*/', '', $_SERVER['REQUEST_URI']);
        $path = str_replace('/index.php', '', $path);
        return '/' . trim($path, '\/');
    }

    /**
     * desc 获取url根路径
     * @return string
     */
    static function getRootUrl()
    {
        $url = isset($_SERVER['https']) ? 'https://' : 'http://';
        $url .= $_SERVER['HTTP_HOST'];
        return $url;
    }

    /**
     * desc
     * date
     * @param $url
     */
    static function redirect($url)
    {
        $url = 'http://'. $_SERVER['HTTP_HOST']. $url;
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Location: $url", true, 301);
    }

    /**
     * desc
     * date
     */
    static function redirectBack()
    {
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Location: {$_SERVER['HTTP_REFERER']}");
    }

    /**
     * desc
     * date
     */
    static function show404()
    {
        header("HTTP/1.1 404 Not Found");
        header("Status: 404 Not Found");
        echo 404;
    }
}