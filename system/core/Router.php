<?php

/**
 * Created by PhpStorm.
 * User: louis
 * Date: 16/6/30
 * Time: 下午11:35
 */
class Core_Router
{
    /**
     * desc web路由,将url路由到指定函数
     * date 2017-06-10
     * @throws Exception
     */
    public static function webRoute()
    {
        // 从WEB请求的URL获取路由
        $sPath = UrlUtil::getUrlPath();

        // 执行路由对应函数
        self::callController($sPath);
    }

    /**
     * desc   调用路由对应控制器函数
     * date   2017-09-30
     * @param string $sPath
     * @throws Exception
     * @internal param bool|true $bIsWeb
     */
    public static function callController($sPath)
    {
        // 获取控制器名称
        $sPath = trim($sPath, '\/');
        $aPath = explode('/', $sPath);
        if (count($aPath) == 1) { // url只有一段
            $sController = ConfUtil::getConf('app.default_controller');
        } else {
            $sController = ucfirst(strtolower($aPath[0]));
            $sController = 'Controller_' . $sController;
        }

        // 获取控制器actions
        if (!isset($sController::$actions) || !is_array($sController::$actions)) {
            throw new Exception('[system error] ' . date('Y-m-d H:i:s') . " controller [$sController] has no actions!");
        }
        $actions = $sController::$actions;


        // 获取路由对应action
        if (count($aPath) > 1) {
            array_shift($aPath); // 从path中把controller的名字去掉
        }
        $sPath       = '/' . implode('/', $aPath);
        $sActionName = self::getAction($actions, $sPath);
        if ($sActionName === false || !isset($actions[ $sActionName ])) {
            throw new Exception('[system error] ' . date('Y-m-d H:i:s') . " $sPath match not action in controller[$sController]!");
        }

        // 获取action对应函数
        $aClassMethod = $actions[ $sActionName ];
        if (!is_callable($aClassMethod)) {
            header('HTTP/1.1 404 Not Found');
            throw new Exception('[system error] ' . date('Y-m-d H:i:s') . " action [$sController:$sPath] not callable!");
        }

        // 调用路由对应action
        $sObject      = new $aClassMethod[0];
        $sMethod      = $aClassMethod[1];
        $aClassMethod = array($sObject, $sMethod); // 需要先实例化

        $aPathParams = self::extractPatams($sActionName, $sPath);
        call_user_func_array($aClassMethod, $aPathParams);
    }

    /**
     * desc 提取path对应action中的参数
     * date
     * @param $sActionName
     * @param $sPath
     * @return mixed
     */
    private static function extractPatams($sActionName, $sPath)
    {
        $sActionPattern = self::action2Pattern($sActionName);
        preg_match($sActionPattern, $sPath, $matches);
        array_shift($matches);
        return $matches;
    }


    /**
     * desc 找出与url对应的action
     * date
     * @param $actions
     * @param $sPath
     * @return bool|int|string
     */
    private static function getAction($actions, $sPath)
    {
        // url对应action直接存在
        if (isset($actions[ $sPath ])) {
            return $sPath;
        }

        // url与action逐个匹配
        foreach ($actions as $sActionName => $actionMethod) {
            $sActionPattern = self::action2Pattern($sActionName);
            if (preg_match($sActionPattern, $sPath)) {
                return $sActionName;
            }
        }

        return false;
    }

    /**
     * desc 将controller的action的名称转换为正则表达式
     * date
     * @param $sActionName
     * @return string
     */
    private static function action2Pattern($sActionName)
    {

        $sActionPattern = preg_replace('/{[^\/\}]+}/', '([^/\}]+)', $sActionName);
        $sActionPattern = addcslashes($sActionPattern, '/');
        return '/^' . $sActionPattern . '$/';
    }
}