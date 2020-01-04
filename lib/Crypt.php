<?php

/**
 * Created by PhpStorm.
 * User: louis
 * Date: 16/10/1
 * Time: 下午7:31
 */


define('IV_SIZE', mcrypt_get_iv_size(Crypt::CIPER, Crypt::MODE));


class Crypt
{
    const CIPER = MCRYPT_RIJNDAEL_128;
    const MODE  = MCRYPT_MODE_CBC;

    /**
     * 加密数据
     * @param $sData
     * @param $sKey
     * @return string
     */
    public static function encrypt($sData, $sKey)
    {
        $iv     = mcrypt_create_iv(IV_SIZE, MCRYPT_DEV_URANDOM);
        $crypt  = mcrypt_encrypt(self::CIPER, $sKey, $sData, self::MODE, $iv);
        $secret = $iv . $crypt;
        return base64_encode($secret);
    }

    /**
     * 解密数据
     * @param $sSecret
     * @param $sKey
     * @return string
     */
    public static function decrypt($sSecret, $sKey)
    {
        $sSecret = base64_decode($sSecret);
        $iv      = substr($sSecret, 0, IV_SIZE);
        $crypt   = substr($sSecret, IV_SIZE, strlen($sSecret));
        $plain   = mcrypt_decrypt(self::CIPER, $sKey, $crypt, self::MODE, $iv);
        return rtrim($plain, "\0");  // 去除填充的空白字符
    }
}