<?php

/**
 * Created by PhpStorm.
 * User: louis
 * Date: 16/7/31
 * Time: 下午6:43
 */

require BASEPATH . '/vendor/autoload.php';
use Medoo\Medoo;


class Database
{
    // 参考文档 https://medoo.lvtao.net/1.2/doc.php
    private static $dbs = array();

    /**
     * desc 获取数据库连接
     * date 2017-07-22
     * @param  string $name
     * @return Medoo
     */
    public static function getInstance($name = 'default')
    {
        if (!isset(self::$dbs[ $name ])) {
            $oDbConfig          = ConfUtil::getConf('database.' . $name);
            self::$dbs[ $name ] = new Medoo($oDbConfig);
        }
        return self::$dbs[ $name ];
    }
}