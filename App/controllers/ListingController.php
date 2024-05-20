<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;
use Framework\Session;
use Framework\Authorisation;
use Framework\Middleware\Authorise;
use Random\Engine\Secure;

class ListingController
{
    protected $db;

    public function __construct()
    {
        $config = require basepath('config/db.php');
        $this->db = new Database($config);
    }

    public function index()
    {
        $listings = $this->db->query('SELECT * FROM listing ORDER BY created_at DESC')->fetchAll();

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
        if (!$listing) {
            ErrorController::notFound('该岗位不存在:');
            return;
        }

        loadView('listings/show', [
            'listing' => $listing
        ]);
    }

    public function store()
    {
        $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'address', 'city', 'province', 'phone', 'email', 'requirements', 'benefits'];

        // 从表单获取允许的字段
        $newListingData = array_intersect_key($_POST, array_flip($allowedFields));
        $newListingData['user_id'] = Session::get('user')['id'];

        // 清理数据
        $newListingData = array_map('sanitize', $newListingData);

        // 定义必须的字段
        $requiredFields = ['title', 'description', 'email', 'city', 'province'];

        // 检查必填字段
        $errors = [];
        foreach ($requiredFields as $field) {
            if (empty($newListingData[$field]) || !Validation::string($newListingData[$field])) {
                $errors[$field] = ucfirst($field) . ' 为必须项';
            }
        }

        if (!empty($errors)) {
            loadView('listings/create', [
                'errors' => $errors,
                'listing' => $newListingData
            ]);
            return; // 添加 return 以确保在有错误时不继续执行
        } else {
            $fields = implode(', ', array_keys($newListingData));
            $values = ':' . implode(', :', array_keys($newListingData));
            $query = "INSERT INTO listing ({$fields}) VALUES ({$values})";
            $this->db->query($query, $newListingData);
            Session::setFlashMessage('success_message', '添加职位成功');
            redirect('/listings');
        }
    }

    public function destroy($params)
    {
        $id = $params['id'];
        $params = [
            'id' => $id
        ];
        $listing = $this->db->query('SELECT * FROM listing WHERE id = :id', $params)->fetch();
        if (!$listing) {
            ErrorController::notFound("职位不存在!");
            return;
        }
        if (!Authorisation::isOwner($listing->user_id)) {
            inspect($_SESSION);
            Session::setFlashMessage('error_message', '您无权删除该职位');
            return redirect('/listings/') . $listing->id;
        }
        $this->db->query('DELETE FROM listing WHERE id = :id', $params);
        Session::setFlashMessage('success_message', '删除职位成功');
        redirect('/listings');
    }

    public function edit($params)
    {
        $id = $params['id'] ?? '';
        $params = [
            'id' => $id
        ];
        $listing = $this->db->query('SELECT * FROM listing WHERE id = :id', $params)->fetch();
        if (!$listing) {
            ErrorController::notFound("职位不存在");
            return;
        }
        //授权
        if (!Authorisation::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message', '您无权修改该职位');
            return redirect('/listings/') . $listing->id;
        }

        loadView('listings/edit', [
            'listing' => $listing
        ]);
    }

    public function update($params)
    {
        $id = $params['id'] ?? '';
        $params = [
            'id' => $id
        ];
        $listing = $this->db->query('SELECT * FROM listing WHERE id = :id', $params)->fetch();
        if (!$listing) {
            ErrorController::notFound("职位不存在");
            return;
        }
        if (!Authorisation::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message', '您无权修改该职位');
            return redirect('/listings/') . $listing->id;
        }
        $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'address', 'city', 'province', 'phone', 'email', 'requirements', 'benefits'];
        $updateValues = array_intersect_key($_POST, array_flip($allowedFields));
        $updateValues = array_map('sanitize', $updateValues);
        $requiredFields = ['title', 'description', 'salary', 'email', 'city', 'province'];
        $errors = [];
        foreach ($requiredFields as $field) {
            if (empty($updateValues[$field]) || !Validation::string($updateValues[$field])) {
                $errors[$field] = ucfirst($field) . ' 为必需项';
            }
        }

        if (!empty($errors)) {
            loadView('listings/edit', [
                'errors' => $errors,
                'listing' => $updateValues
            ]);
            return; // 添加 return 以确保在有错误时不继续执行
        } else {
            $updateFields = [];
            foreach (array_keys($updateValues) as $field) {
                $updateFields[] = "{$field} = :{$field}";
            }
            $updateFields = implode(', ', $updateFields);
            $updateQuery = "UPDATE listing SET $updateFields WHERE id = :id";
            $updateValues['id'] = $id;
            $this->db->query($updateQuery, $updateValues);
            Session::setFlashMessage('success_message', '更新职位成功');
            redirect('/listings/' . $id);
        }
    }
    /**
     * 完成搜索功能
     * 根据关键词和地点来搜索列表项
     * 此方法从请求中获取关键词和地点，然后执行数据库查询以找到匹配的列表项。
     * 查询结果用于渲染列表视图。
     *
     * @return void 无返回值。
     */
    public function search()
    {
        // 从 GET 请求中获取关键词和地点，如果没有提供则默认为空字符串
        $keywords = isset($_GET['keywords']) ? trim($_GET['keywords']) : '';
        $location = isset($_GET['location']) ? trim($_GET['location']) : '';

        // 构建 SQL 查询，搜索标题、描述、标签或公司名中包含关键词，并且城市或州包含地点的列表
        $query = "SELECT * FROM listing WHERE 
        (title LIKE :keywords OR description LIKE :keywords OR 
        tags LIKE :keywords OR company LIKE :keywords) AND 
        (city LIKE :location OR province LIKE :location)";

        // 准备查询参数，关键词和地点周围添加百分号以实现模糊匹配
        $params = [
            'keywords' => "%{$keywords}%",
            'location' => "%{$location}%"
        ];

        // 执行查询并获取所有匹配的记录
        $listings = $this->db->query($query, $params)->fetchAll();

        // 加载列表视图，将筛选查询到的列表数据及搜索条件传递
        loadView('listings/index', [
            'listings' => $listings,
            'keywords' => $keywords,
            'location' => $location
        ]);
    }
}
