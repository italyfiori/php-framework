<?php

/**
 * Created by PhpStorm.
 * User: louis
 * Date: 16/10/1
 * Time: 下午8:58
 */
class Session
{
    private static $flashKey = 'SESSION_FLASH_DATA_COUNTS_KEY'; // 保存一次性session的计数

    /**
     * desc session启动
     */
    private static function init()
    {
        if (session_id() == '') {
            session_start();
        }
    }

    /**
     * desc   校验session的键是否字符串
     * @param $key
     * @return bool
     */
    private static function checkKey($key)
    {
        return is_string($key);
    }

    /**
     * desc   设置session值
     * @param $key
     * @param $value
     * @return bool
     */
    public static function set($key, $value)
    {
        self::init();
        if (self::checkKey($key)) {
            $_SESSION[ $key ] = $value;
            return true;
        }
        return false;
    }

    /**
     * desc   获取session值
     * @param $key
     * @param null $default
     * @return null
     */
    public static function get($key, $default = null)
    {
        self::init();
        if (self::checkKey($key)) {
            self::decreaseFlashData();
            return isset($_SESSION[ $key ]) ? $_SESSION[ $key ] : $default;
        }
        return null;
    }

    /**
     * desc    删除session值
     * @param $key
     * @return bool
     */
    public static function forget($key)
    {
        self::init();
        if (self::has($key)) {
            unset($_SESSION[ $key ]);
            return true;
        }
        return false;
    }

    /**
     * desc   判断是否拥有session值
     * @param $key
     * @return bool
     */
    public static function has($key)
    {
        self::init();
        return self::checkKey($key) && isset($_SESSION[ $key ]);
    }

    /**
     * desc  清空session
     */
    public static function flush()
    {
        self::init();
        session_unset();
    }

    /**
     * desc   持续一次的session
     * @param $key
     * @param $value
     * @param int $count
     * @return bool
     */
    public static function flash($key, $value, $count = 1)
    {
        if (self::checkKey($key)) {
            self::set($key, $value);
            $arrFlashData         = self::getFlashData();
            $arrFlashData[ $key ] = $count;
            self::setFlashData($arrFlashData);
            return true;
        }
        return false;
    }

    /**
     * desc   设置flash session的计数
     * @param $arrFlashData
     */
    private static function setFlashData($arrFlashData)
    {
        self::init();
        $key              = self::$flashKey;
        $_SESSION[ $key ] = $arrFlashData;
    }

    /**
     * desc    获取flash session的计数
     * @return array
     */
    private static function getFlashData()
    {
        self::init();
        $key = self::$flashKey;
        return isset($_SESSION[ $key ]) && is_array($_SESSION[ $key ]) ? $_SESSION[ $key ] : array();
    }

    /**
     * desc 递减flash session的计数, 若为0则清空
     */
    private static function decreaseFlashData()
    {
        $arrFlashData = self::getFlashData();
        foreach ($arrFlashData as $key => $count) {
            if ($count <= 0) {
                unset($_SESSION[ $key ]);
                unset($arrFlashData[ $key ]);
            } else {
                $arrFlashData[ $key ] = $count - 1;
            }
        }
        self::setFlashData($arrFlashData);
    }

    /**
     * desc 重新生成session id
     */
    public static function regenerate()
    {
        session_regenerate_id();
    }
}