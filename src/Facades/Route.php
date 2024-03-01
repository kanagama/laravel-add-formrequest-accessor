<?php

namespace Kanagama\FormRequestAccessor\Facades;

use Illuminate\Support\Facades\Route as RouteFacade;

/**
 * controller 名と action 名を取得する
 *
 * @author k-nagama <k.nagama0632@gmail.com>
 */
class Route
{
    /**
     * コントローラー名を取得
     *
     * @static
     * @return string
     */
    public static function getController(): string
    {
        $route = RouteFacade::currentRouteAction();
        if (empty($route) || strpos($route, '@') === false) {
            return '';
        }

        $namespace_controller = explode("@", $route)[0];
        $namespaces = explode("\\", $namespace_controller);

        return end($namespaces);
    }

    /**
     * アクション名を取得
     *
     * @static
     * @return string
     */
    public static function getAction(): string
    {
        //
        $route = RouteFacade::currentRouteAction();
        if (empty($route) || strpos($route, '@') === false) {
            return '';
        }

        return explode("@", $route)[1];
    }
}
