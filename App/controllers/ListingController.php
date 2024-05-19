<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;

class ListingController
{
    protected $db;

    public function __construct()
    {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    public function index()
    {
        $listings = $this->db->query('SELECT * FROM listing')->fetchAll();

        loadView('listings/index', [
            'listings' => $listings
        ]);
    }

    public function create()
    {
        loadView('listings/create');
    }

    public function show($params)
    {
        $id = $params['id'] ?? '';
        $params = [
            'id' => $id
        ];
        $listing = $this->db->query('SELECT * FROM listing WHERE id = :id', $params)->fetch();
        //检查listing是否存在
        if (!$listing) {
            ErrorController::notFound('该岗位不存在！');
            return;
        }

        loadView('listings/show', [
            'listing' => $listing
        ]);
    }
    /**
     * 存储表单中传递的数据
     *
     * 该函数首先定义了一个允许在表单中保存的字段列表，然后，它从 $_POST 全局数组中提取这些字段的数据。
     * 接着，它为这些数据添加了用户 ID，并对这些数据进行清理。最后，该函数验证这些数据。
     * 如果验证失败，它将错误消息和表单数据传递到视图中。如果验证成功，则输出成功消息。
     *
     * @return void
     */
    public function store()
    {
        $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'address', 'city', 'province', 'phone', 'email', 'requirements', 'benefits'];

        // 从 $_POST 中提取允许的字段
        $newListingData = array_intersect_key($_POST, array_flip($allowedFields));

        // 设置用户 ID
        $newListingData['user_id'] = 1;

        // 清除数据
        $newListingData = array_map('sanitize', $newListingData);

        // 定义必填字段
        $requiredFields = ['title', 'description', 'email', 'city', 'province'];

        // 检查必填字段
        $errors = [];
        foreach ($requiredFields as $field) {
            if (empty($newListingData[$field]) || !Validation::string($newListingData[$field])) {
                $errors[$field] = ucfirst($field) . " 为必填项";
            }
        }

        // 处理错误信息
        if (!empty($errors)) {
            // 如果有错误信息，将错误和表单数据传递到视图中
            loadView('listings/create', [
                'errors' => $errors,
                'Listing' => $newListingData
            ]);
        } else {
            // 否则输出成功消息
            echo "表单提交成功！";
        }
    }
}
