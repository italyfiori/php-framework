<?php

/**
 * Created by PhpStorm.
 * User: louis
 * Date: 16/7/16
 * Time: 下午8:43
 */
class Pagination
{
    /**
     * desc   计算分页的相关信息(开始到结束)
     * @param $currentPage
     * @param $totalPage
     * @param int $buttonsLen
     * @return array
     */
    public static function getPagination($currentPage, $totalPage, $buttonsLen = 10)
    {
        $halfLen   = floor($buttonsLen / 2);
        $pageStart = $currentPage - $halfLen > 0 ? $currentPage - $halfLen : 1;
        $pageEnd   = $pageStart + $buttonsLen - 1 > $totalPage ? $totalPage : $pageStart + $buttonsLen - 1;
        $pageStart = $pageEnd - $buttonsLen + 1 > 0 ? $pageEnd - $buttonsLen + 1 : 1;

        return array(
            'current'  => $currentPage,
            'total'    => $totalPage,
            'previous' => $currentPage - 1,
            'next'     => $currentPage == $totalPage ? 0 : $currentPage + 1,
            'range'    => range($pageStart, $pageEnd),
        );
    }
}