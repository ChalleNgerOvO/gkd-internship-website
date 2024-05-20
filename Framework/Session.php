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
    /**
     * 设置闪存消息
     * 闪存消息用于在一个请求中设置消息，在下一个请求中显示后即被清除。
     *
     * @param string $key 消息的键，用于标识和检索消息。
     * @param string $message 要存储的消息内容。
     * @return void 无返回值。
     */
    public static function setFlashMessage($key, $message)
    {
        // 调用 set 方法将消息存储在会话中，键名前加上 'flash_' 前缀以区分。
        self::set('flash_' . $key, $message);
    }

    /**
     * 获取闪存消息并在获取后立即删除
     * 这确保了消息只能被读取一次，用完即焚。
     *
     * @param string $key 消息的键，与设置消息时使用的键相同。
     * @param mixed $default 如果消息不存在，返回的默认值。
     * @return string 返回存储的消息，如果消息不存在，返回默认值。
     */
    public static function getFlashMessage($key, $default = null)
    {
        // 从会话中获取消息，如果不存在，则返回默认值。
        $message = self::get('flash_' . $key, $default);
        // 删除会话中的消息，确保它只被读取一次。
        self::clear('flash_' . $key);
        return $message;
    }
}
