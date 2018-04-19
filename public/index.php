<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;
error_reporting(E_ALL);
use Illuminate\Database\Capsule\Manager as Capsule;
define('BASEPATH', __DIR__ . '/../');
// 加载基础文件
require __DIR__ . '/../thinkphp/base.php';

$config = require BASEPATH . 'config/database.php';

$capsule = new Capsule;

// 创建链接
$capsule->addConnection($config, 'default');


// 设置全局静态可访问
$capsule->setAsGlobal();

// 启动Eloquent
$capsule->bootEloquent();
// 支持事先使用静态方法设置Request对象和Config对象

// 执行应用并响应
Container::get('app')->run()->send();
