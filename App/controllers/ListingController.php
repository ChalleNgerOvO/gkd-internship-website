<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;

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
        $Listings = $this->db->query('SELECT * FROM listing')->fetchAll();

        loadView('listings/index', [
            'listings' => $Listings
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
        $newListingData['user_id'] = 1;

        // 清理数据
        $newListingData = array_map('sanitize', $newListingData);

        // 定义必须的字段
        $requiredFields = ['title', 'description', 'email', 'city', 'province'];

        // 检查必填字段
        $errors = [];
        foreach ($requiredFields as $field) {
            if (empty($newListingData[$field]) || !Validation::string($newListingData[$field])) {
                $errors[$field] = ucfirst($field) . ' is required.';
            }
        }


        if (!empty($errors)) {
            loadView('listings/create', [
                'errors' => $errors,
                'listing' => $newListingData
            ]);
        } else {
            $fields = [];
            foreach ($newListingData as $field => $value) {
                $fields[] = $field;
            }
            $fields = implode(', ', $fields);
            $values = [];
            foreach ($newListingData as $field => $value) {
                if ($value === '') {
                    $newListingData[$field] = null;
                }
                $values[] = ':' . $field;
            }
            $values = implode(', ', $values);
            $query = "INSERT INTO listing ({$fields}) VALUES ({$values})";
            $this->db->query($query, $newListingData);
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
        $this->db->query('DELETE FROM listing WHERE id = :id', $params);
        $_SESSION['success_message'] = '删除职位成功';
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
        $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'address', 'city', 'state', 'phone', 'email', 'requirements', 'benefits'];
        $updateValues = array_intersect_key($_POST, array_flip($allowedFields));
        $updateValues = array_map('sanitize', $updateValues);
        $requiredFields = ['title', 'description', 'salary', 'email', 'city', 'province'];
        $error = [];
        foreach ($requiredFields as $field) {
            if (empty($updateValues[$field]) || !Validation::string($updateValues[$field])) {
                $errors[$field] = ucfirst($field) . ' 为必须项';
            }
        }

        if (!empty($errors)) {
            loadView('listings/edit', [
                'errors' => $errors,
                'listing' => $updateValues
            ]);
            exit;
        } else {
            $updateFields = [];
            foreach (array_keys($updateValues) as $field) {
                $updateFields[] = "{field} = :{$field}";
            }
            $updateFields = implode(', ', $updateFields);
            $updateQuery = "UPDATE listing SET {$updateFields} WHERE id = :id";
            $updateValues['id'] = $id;
            $this->db->query($updateQuery, $updateValues);
            $_SESSION['success_message'] = "职位信息以更新";
            redirect('/listings/' . $id);
        }
    }
}
