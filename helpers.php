<?php

/**
 * 获取根路径
 * 
 * @param string $path
 * @return string
 */
function basePath($path = '')
{
    return __DIR__ . '/' . $path;
}

/**
 * 加载部分视图
 * 
 * @param string $name
 * @returen void
 */
function loadPartial($name)
{
    $partialPath = basePath("views/partials/{$name}.php");
    if (file_exists($partialPath)) {
        require $partialPath;
    } else {
        echo "{$name}部分视图不存在！";
    }
}

/**
 * 加载视图
 * 
 * @param string $name
 * @returen void
 *  */
function loadView($name)
{
    $viewPath = basePath("views/{$name}.view.php");
    if (file_exists($viewPath)) {
        require $viewPath;
    } else {
        echo "{$path}视图文件不存在！";
    }
}

/**
 * 检查某个值
 * 
 * @param mixed $value
 * @return void
 */
function inspect($value)
{
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
}
