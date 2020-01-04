<?php

/**
 * Created by PhpStorm.
 * User: louis
 * Date: 16/7/3
 * Time: 上午11:50
 */
class RedisUtil
{
    private static $redis = null;

    public static function getRedisInstance()
    {
        if (empty(self::$redis)) {
            $redis = new Redis();
            $redis->connect(ConfUtil::getConf('redis.host'), ConfUtil::getConf('redis.port'));
            self::$redis = $redis;
        }
        return self::$redis;
    }

    /**
     * desc 从缓存或函数中取数据
     * date 20161114
     * @param $key
     * @param callable $func
     * @param $params
     * @param int $timeout
     * @param bool|false $refresh
     * @return array|bool|mixed|string
     */
    public static function getEx($key, callable $func, $params, $refresh = false, $timeout = 0)
    {
        // 从缓存中获取
        $oRedis = RedisUtil::getRedisInstance();
        if (false === $refresh) {
            $redisRet = $oRedis->get($key);
            if (false !== $redisRet) {
                $tmpRet = json_decode($redisRet, true);
                return is_array($tmpRet) ? $tmpRet : $redisRet;
            }
        }

        // 从函数中取
        $result = call_user_func_array($func, $params);
        if (false !== $result) {
            $result = is_array($result) || is_object($result) ? json_encode($result) : $result . '';
            $oRedis->set($key, $result . '');
            if ($timeout > 0) {
                $oRedis->expire($key, $timeout);
            }
        }

        // 返回
        $tmpRet = json_decode($result, true);
        return is_array($tmpRet) ? $tmpRet : $result;
    }

    /**
     * desc 从redis hash类型key 或 指定函数中取数据
     * date 20161114
     * @param $redisKey
     * @param $field
     * @param callable $func
     * @param $params
     * @param bool|false $refresh
     * @param int $timeout
     * @return array|mixed|string
     */
    public static function hGetEx($redisKey, $field, callable $func, $params, $refresh = false, $timeout = 0)
    {
        // 从缓存中取
        $oRedis = RedisUtil::getRedisInstance();
        if (false === $refresh) {
            $redisRet = $oRedis->hGet($redisKey, $field);
            if ($redisRet !== false) {
                return $redisRet;
            }
        }

        // 从函数中取
        $result = call_user_func_array($func, $params);
        if (false !== $result) {
            $result = is_array($result) || is_object($result) ? json_encode($result) : $result . '';
            $oRedis->hSet($redisKey, $field, $result);
            if ($timeout > 0) {
                $oRedis->expire($redisKey, $timeout);
            }
        }

        // 返回
        $tmpRet = json_decode($result, true);
        return is_array($tmpRet) ? $tmpRet : $result;
    }


    /**
     * desc 从缓存或函数中取数据
     * date 20161114
     * @param $key
     * @param callable $func
     * @param $params
     * @param int $timeout
     * @param bool|false $refresh
     * @return array|bool|mixed
     */
    public static function hGetAllEx($key, callable $func, $params, $refresh = false, $timeout = 0)
    {
        // 从缓存中取
        $oRedis = RedisUtil::getRedisInstance();
        if (false === $refresh) {
            $redisRet = $oRedis->hGetAll($key);
            if (!empty($redisRet)) {
                return $redisRet;
            }
        }

        // 从函数中取
        $result = call_user_func_array($func, $params);
        if (is_array($result)) {
            $oRedis->hMset($key, $result);
            if ($timeout > 0) {
                $oRedis->expire($key, $timeout);
            }
            return $result;
        }

        return false;
    }
}