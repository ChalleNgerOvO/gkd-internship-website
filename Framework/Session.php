<?php

namespace Framework;

class Session
{
    /**
     * 开启会话
     * 如果会话尚未开始，则开始一个新的会话。
     *
     * @return void 无返回值
     */
    public static function start()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * 设置会话键值对
     * 将指定的键和值存储在会话中。
     *
     * @param string $key 会话键名
     * @param mixed $value 要存储的值
     * @return void 无返回值
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * 通过键获取会话值
     * 如果指定键存在，则返回其值；如果未设置，则返回默认值。
     *
     * @param string $key 会话键名
     * @param mixed $default 未设置时返回的默认值
     * @return mixed 返回存储的值或默认值
     */
    public static function get($key, $default = null)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    /**
     * 检查会话键是否存在
     * 判断指定的键是否在会话中已设置。
     *
     * @param string $key 会话键名
     * @return bool 返回键是否存在的布尔值
     */
    public static function has($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * 清除指定的会话键
     * 如果键存在，则清除该键。
     *
     * @param string $key 会话键名
     * @return void 无返回值
     */
    public static function clear($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * 清除所有会话数据
     * 终结会话并清除所有存储的数据。
     *
     * @return void 无返回值
     */
    public static function clearAll()
    {
        session_unset(); // 清除所有会话变量
        session_destroy(); // 销毁会话
    }
}
