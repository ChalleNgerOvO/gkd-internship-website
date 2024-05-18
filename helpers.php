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
