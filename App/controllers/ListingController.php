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
            // 没有错误信息，验证通过，准备SQL数据
            $fields = [];
            $values = [];

            // 遍历新职位数据，将字段和对应的值分别存储在数组中
            foreach ($newListingData as $field => $value) {
                // 检查是否为空字段
                if (!empty($value)) {
                    $fields[] = $field;
                    $values[] = $value;
                }
            }

            // 将字段和值分别连接为字符串，以便SQL插入语句使用
            $fields = implode(', ', $fields);
            $values = implode(', ', array_map([$this->db, 'quote'], $values));

            // 构建SQL插入语句
            $query = "INSERT INTO listing ({$fields}) VALUES ({$values})";

            // 执行SQL插入语句
            $this->db->query($query, $newListingData);

            // 重定向到列表页
            redirect('/listings');
        }
    }

    /**
     * 删除一个列表项
     *
     * 该方法首先会检查数据库中是否有该项 ID，然后，如果该 ID 在数据库中，则会将该项删除。
     * 如果该项不存在，则返回一个错误消息。删除操作完成后，返回到列表页。
     *
     * @param array $params 包含各个参数的数组，例如项的ID.
     * @return void 该方法没有返回值.
     */
    public function destroy($params)
    {
        // 从参数中获取列表项的ID
        $id = $params['id'] ?? '';

        // 准备用于数据库查询的参数数组
        $params = [
            'id' => $id
        ];

        // 查询数据库以确认该项是否存在
        $listing = $this->db->query('SELECT * FROM listing WHERE id = :id', $params)->fetch();

        // 如果该项不存在，则显示错误
        if (!$listing) {
            ErrorController::notFound('该项不存在！');
            return;
        }

        // 执行删除操作
        $this->db->query('DELETE FROM listing WHERE id = :id', $params);

        // 删除成功后重定向到列表页面
        redirect('/listings');
    }
}
