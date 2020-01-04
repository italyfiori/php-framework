<?php

/**
 * Created by PhpStorm.
 * User: louis
 * Date: 16/6/30
 * Time: 下午11:14
 */
class Core_AutoLoader
{
    /**
     * desc 自动加载类函数
     * @param $class
     * @throws Exception
     */
    static function autoload($class)
    {
        $arrDirs = array(
            BASEPATH . '/system',
            BASEPATH . '/lib',
            BASEPATH . '/app',
        );

        foreach ($arrDirs as $dir) {
            $arrPath = explode('_', $class);
            for ($i = 0; $i < count($arrPath) - 1; $i++) {
                $arrPath[ $i ] = strtolower($arrPath[ $i ]);
            }

            $file = $dir . DIRECTORY_SEPARATOR . implode('/', $arrPath) . '.php';
            if (is_readable($file)) {
                require_once $file;
                break;
            }
        }

        if (!class_exists($class)) {
            throw new Exception('[system error] ' . date('Y-m-d H:i:s') . " class [$class] not found!");
        }
    }
}