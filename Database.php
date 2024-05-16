<?php
class Database {
    public $conn;
    public function __construct($config) {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ];

        try {
            $this->conn = new PDO($dsn, $config['username'], $config ['password'],$options);
        } catch (PDOException $e) {
            echo "连接成功！"; 
            throw new Exception("数据库连接失败：{$e->getMessage()}");
        }
    }
    public function query($query, $params = []) {
        try {
            $sth = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                $sth->bindValue(':'.$key, $value); // 绑定参数时使用$key作为参数名
            }
            $sth->execute();
            return $sth;
        } catch (PDOException $e) {
            throw new Exception("数据请求执行失败：{$e->getMessage()}");
        }
    }
}
