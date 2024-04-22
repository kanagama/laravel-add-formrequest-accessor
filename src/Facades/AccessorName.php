<?php

namespace Kanagama\FormRequestAccessor\Facades;

use Illuminate\Support\Str;

/**
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class AccessorName
{
    /**
     * メソッド名からアクセサプロパティ名を取得
     *
     * @static
     * @param  string  $method
     * @return string
     */
    public static function getProperty(string $method): string
    {
        preg_match('/(?<=get_).+(?=_attribute)/', Str::snake($method), $match);
        if (empty($match[0]) === true) {
            return '';
        }

        return $match[0];
    }

    /**
     * @static
     * @param  string  $property
     * @return string
     */
    public static function getMethod(string $property): string
    {
        if (empty($property) === true) {
            return '';
        }

        return 'get' . Str::studly($property) . 'Attribute';
    }
}
