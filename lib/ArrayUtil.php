<?php

/**
 * Created by PhpStorm.
 * User: louis
 * Date: 16/7/31
 * Time: 下午8:22
 */
class ArrayUtil
{
    public static function extract($array, $arrKeys)
    {
        $arrRet = array();
        foreach ($array as $key => $value) {
            if (in_array($key, $arrKeys)) {
                $arrRet[ $key ] = $value;
            }
        }
        return $arrRet;
    }

    public static function reIndex($aArray2D, $sIndexKey)
    {
        $arrRet = array();
        foreach ($aArray2D as $sKey => $oSubArray) {
            if (isset($oSubArray[ $sIndexKey ])) {
                $arrRet[ $oSubArray[ $sIndexKey ] ] = $oSubArray;
            }
        }
        return $arrRet;
    }

    public static function buildSqlConditions($oConds)
    {
        $aConds = array();
        foreach ($oConds as $sKey => $sValue) {
            $aConds[] = is_string($sValue) ? "$sKey '$sValue'" : "$sKey $sValue";
        }

        return implode(" and ", $aConds);
    }
}