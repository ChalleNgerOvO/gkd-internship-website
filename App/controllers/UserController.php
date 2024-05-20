<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;

class UserController
{
    protected $db;

    /**
     * @return mixed
     */
    public function __construct()
    {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    /**
     * 显示登录页面
     * 此方法加载登录视图。
     *
     * @return void
     */
    public function login()
    {
        // 调用 loadView 函数来加载登录页面的视图
        loadView('users/login');
    }

    /**
     * 显示注册页面
     * 此方法加载注册视图。
     *
     * @return void
     */
    public function create()
    {
        // 调用 loadView 函数来加载创建用户（注册）页面的视图
        loadView('users/create');
    }
}
