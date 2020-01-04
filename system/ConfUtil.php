<?php

/**
 * Created by PhpStorm.
 * User: louis
 * Date: 16/7/2
 * Time: 下午6:01
 */
class ConfUtil
{
    private static $arrConf = array();

    /**
     * desc 获取配置
     * @param $path
     * @return null
     * @throws Exception
     */
    public static function getConf($path)
    {
        if (isset(self::$arrConf[ $path ])) {
            return self::$arrConf[ $path ];
        }

        // app基本配置文件路径
        $sAppConfPath = BASEPATH . '/conf/app.php';
        if (!is_readable($sAppConfPath)) {
            throw new Exception('[system error] ' . date('Y-m-d H:i:s') . " app conf not found!");
        }

        // 查找配置环境
        $arrAppConf  = require $sAppConfPath;
        $sEnviroment = $arrAppConf['env'];
        if (!isset($sEnviroment)) {
            throw new Exception('[system error] ' . date('Y-m-d H:i:s') . " app conf has no enviroment config!");
        }

        // 确定配置文件路径
        list($sConfPrefix, $sConfName) = explode('.', $path);

        // app配置基础配置, 不区分环境
        if ($sConfPrefix == 'app') {
            $sConfPath = BASEPATH . '/conf/app.php';
        } else {
            // 优先使用环境下的配置(例如conf/dev/database), 否则使用默认配置(conf/database)
            $sConfPath = BASEPATH . '/conf/' . $sEnviroment . '/' . $sConfPrefix . '.php';
            if(!is_file($sConfPath)) {
                $sConfPath = BASEPATH . '/conf/' . $sConfPrefix . '.php';
            }
        }

        // 读取配置
        if (is_readable($sConfPath)) {
            $arrConf = require $sConfPath;
            if (is_array($arrConf) && isset($arrConf[ $sConfName ])) {
                self::$arrConf[ $path ] = $arrConf[ $sConfName ];
                return $arrConf[ $sConfName ];
            }
        }

        return null;
    }
}