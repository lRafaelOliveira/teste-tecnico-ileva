<?php

namespace core;

class Route
{
    private static $routes = [];
    private static $middlewares = [];
    private static $prefix = '';
    private static $names = [];

    public static function get($uri, $action)
    {
        return self::addRoute('get', $uri, $action);
    }

    public static function post($uri, $action)
    {
        return self::addRoute('post', $uri, $action);
    }

    public static function put($uri, $action)
    {
        return self::addRoute('put', $uri, $action);
    }

    public static function delete($uri, $action)
    {
        return self::addRoute('delete', $uri, $action);
    }

    public static function middleware($middleware)
    {
        self::$middlewares[] = $middleware;
        return new static;
    }

    public static function prefix($prefix)
    {
        self::$prefix = $prefix;
        return new static;
    }

    public static function group($callback)
    {
        $callback();
        self::$middlewares = [];
        self::$prefix = '';
    }

    private static function addRoute($method, $uri, $action)
    {
        $fullUri = self::$prefix . $uri;
        self::$routes[$method][$fullUri] = [
            'action' => $action,
            'middlewares' => self::$middlewares,
            'name' => null,
        ];
        return new static;
    }

    public static function name($name)
    {
        $lastMethod = array_key_last(self::$routes);
        $lastUri = array_key_last(self::$routes[$lastMethod]);

        if ($lastMethod && $lastUri) {
            self::$routes[$lastMethod][$lastUri]['name'] = $name;
            self::$names[$name] = $lastUri;
        }

        return new static;
    }

    public static function getRoutes()
    {
        return self::$routes;
    }

    public static function route($name)
    {
        return self::$names[$name] ?? null;
    }
}
