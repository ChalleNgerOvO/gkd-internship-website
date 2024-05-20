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

    /**
     * 处理用户注册请求。
     * 
     * @return void
     */
    public function store()
    {
        // 从 POST 请求中获取用户输入
        $name = $_POST['name'];
        $email = $_POST['email'];
        $city = $_POST['city'];
        $province = $_POST['province'];
        $password = $_POST['password'];
        $passwordConfirmation = $_POST['password_confirmation'];

        // 初始化错误数组
        $errors = [];

        // 验证电子邮箱格式
        if (!Validation::email($email)) {
            $errors['email'] = '请输入一个有效的电子邮箱地址';
        }

        // 验证姓名长度
        if (!Validation::string($name, 2, 50)) {
            $errors['name'] = '姓名长度应为2到50个字符';
        }

        // 验证密码长度
        if (!Validation::string($password, 6, 50)) {
            $errors['password'] = '密码长度应为6到50个字符';
        }

        // 验证密码确认是否匹配
        if (!Validation::match($password, $passwordConfirmation)) {
            $errors['password_confirmation'] = '两次输入的密码不匹配';
        }

        // 如果存在错误，重新加载注册页面并显示错误信息
        if (!empty($errors)) {
            loadView('users/create', [
                'errors' => $errors,
                'name' => $name,
                'email' => $email,
                'city' => $city,
                'province' => $province,
            ]);
            exit;
        } else {
            inspectAndDie('用户已注册');
        }
    }
}
