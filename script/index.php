<?php
/**
 * User: louis
 * Date: 2017/6/10
 * Time: 上午11:06
 * Desc:
 */

define('BASEPATH', dirname(__DIR__));
require BASEPATH . '/system/core/Bootstrap.php';
Core_Bootstrap::init(false, $argv);

$page = new Service_Page_Index();
$page->script();