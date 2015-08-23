<?php

/**
 * Shortcut for isset($var) ? $var : $default
 * @param array $arr
 * @param string|integer $key
 * @param mixed $default optional default value
 * @return mixed
 */
function nvl($arr, $key, $default = null)
{
    return isset($arr[$key]) ? $arr[$key] : $default;
}

/**
 * Returns app's root path
 * @param string $path
 * @return string
 */
function base_path($path = "")
{
    return __DIR__ . "/../../" . $path; //TODO
}

/**
 * Returns value from config file by 'dot' notation path
 * @param string $dotPath
 * @return mixed
 */
function config($dotPath)
{
    $path = explode(".", $dotPath);
    $cnt = count($path);

    $config = include base_path("config/{$path[0]}.php");

    if (isset($config) && is_array($config)) {
        for ($i = 1; $i < $cnt; $i++) {
            $config = $config[$path[$i]];
        }
    }

    return $config;
}

/**
 * Returns random string of asked length
 * @param int $length optional
 * @return string
 */
function str_random($length = 32)
{
    return substr(str_shuffle(md5(time())), 0, $length);
}