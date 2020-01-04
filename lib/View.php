<?php

/**
 * Created by PhpStorm.
 * User: louis
 * Date: 16/7/10
 * Time: 上午11:46
 */
require BASEPATH . '/vendor/autoload.php';
use Philo\Blade\Blade;
/**
 * Created by PhpStorm.
 * User: louis
 * Date: 16/1/5
 * Time: 下午4:25
 */
class View
{
    /**
     * desc 加载视图
     * date
     * @param $view
     * @param array $data
     * @return mixed
     */
    public static function load($view, $data=array())
    {
        $views = BASEPATH. '/app/views';
        $cache = BASEPATH. '/cache';
        $blade = new Blade($views, $cache);
        return $blade->view()->make($view, $data)->render();
    }
}