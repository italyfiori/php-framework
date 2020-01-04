## APF
A new PHP framework, just for fun and you may like it.


## 使用入门

### 添加路由
> 在`app/controller/Main.php`文件中添加以下代码
```php
class Controller_Main
{
    public static $actions = array(
        '/' => ['Service_Page_Main', 'exec']
    );
}
```

### 创建入口文件
> 创建`app/page/Main.php`文件
```php
<?php

class Service_Page_Main
{
    public function exec()
    {
        echo View::load('index', ['name' => 'World!']);
    }
}
```

### 创建视图
> 在`app/views`文件夹下创建`index.blade.php`文件
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1 style="margin:200px auto; text-align:center;">Hello, {{ $name }}</h1>
</body>
</html>
```

