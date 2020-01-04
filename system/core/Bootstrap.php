<?php

/**
 * Created by PhpStorm.
 * User: louis
 * Date: 16/6/30
 * Time: 下午11:34
 */
class Core_Bootstrap
{
    /**
     * desc 程序初始化
     * date
     * @param bool|false $bWebInit
     * @param array $argv
     */
    static function init($bWebInit = true, $argv = array())
    {
        require BASEPATH . '/system/core/AutoLoader.php';

        try {
            spl_autoload_register('Core_Autoloader::autoload');
            self::setRunEnv();
            if ($bWebInit) {
                Core_Router::webRoute();
            }
        } catch (Exception $e) {
            // 浏览器模式下500信息
            if ($bWebInit) {
                header('HTTP/1.1 500 Internal Server Error');
            }

            // 调试模式输出debug信息
            if (ConfUtil::getConf('app.debug')) {
                echo $e->getMessage();
                echo $bWebInit ? '<pre>' . $e->getTraceAsString() . '</pre>' : $e->getTraceAsString();
            }
        }
    }

    /**
     * desc 设置运行环境
     */
    static function setRunEnv()
    {
        // 错误debug
        $debug = ConfUtil::getConf('app.debug');
        if ($debug) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }

        // 设置时区
        $timezone = ConfUtil::getConf('app.timezone');

        date_default_timezone_set($timezone);
        $bIsDown = ConfUtil::getConf('app.down');
        if($bIsDown) {
            echo 'site is in maintaince';
            die;
        }

        // 设置log路径
        $sLogPath = ConfUtil::getConf('log.log_path');
        // SeasLog::setBasePath($sLogPath);
    }
}