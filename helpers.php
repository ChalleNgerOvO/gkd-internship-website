<?php

/**
 * 获取根路径
 *
 * @param string $path 可选参数，用于追加到基础路径之后的额外路径字符串
 * @return string 返回拼接好的完整路径
 */
function basePath($path = '')
{
    return __DIR__ . '/' . $path; // 将当前目录与传入的路径参数合并，然后返回结果
}


function loadPartial($name)
{

    $partialPath = basePath("App/views/partials/{$name}.php");
    if (file_exists($partialPath)) {
        require $partialPath;
    } else {
        echo "{$name}部分视图不存在";
    }
}


function loadView($name, $data = [])
{
    $viewPath = basePath("App/views/{$name}.view.php");
    if (file_exists($viewPath)) {
        extract($data);
        require $viewPath;
    } else {
        echo "{$path}视图不存在";
    }
}



function inspect($value)
{
    echo '<pre>';
    var_dump($value);
    echo '<pre>';
}

function inspectAndDie($value)
{
    echo '<pre>';
    die(var_dump($value));
    echo '<pre>';
}

/**
 * 清理数据
 *
 * 该函数接收一个字符串参数，首先去除字符串的前后空格，然后使用 PHP 的 filter_var 函数
 * 与 FILTER_SANITIZE_SPECIAL_CHARS 过滤器来清理特殊字符。这个过滤器会安全地处理 HTML 中的特殊字符，
 * 例如 '<' 会变成 '&lt;'，这可以防止潜在的跨站脚本 (XSS) 等安全问题。
 *
 * @param string $dirty 有潜在问题的待清理数据
 * @return string 返回清理后的安全字符串
 */
function sanitize($dirty)
{
    return filter_var(trim($dirty), FILTER_SANITIZE_SPECIAL_CHARS);
}
