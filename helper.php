<?php

define('LOGGER_ROOT_PATH', __DIR__);
define('LOGGER_DS', DIRECTORY_SEPARATOR);

/**
 * building random string by the param which name len
 * @param int $len default 8
 */
function randomString(int $len = 8)
{
    $string = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $stringLen = strlen($string) - 1;
    $content = '';
    for ($i = 0; $i < $len; $i++) {
        $content .= substr($string, mt_rand(0, $stringLen), 1);
    }
    return $content;
}
