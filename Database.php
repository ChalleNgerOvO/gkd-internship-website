<?php

class Database
{
    public $conn;

    /**
     * Database 类构造器
     *
     * @param array $config
     */
    public function __construct($config)
    {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        try {
            $this->conn = new PDO($dsn, $config['username'], $config['password'], $options);
            echo "连接成功";
        } catch (PDOException $e) {
            throw new Exception("数据库连接失败: {$e->getMessage()}");
        }
    }
}
