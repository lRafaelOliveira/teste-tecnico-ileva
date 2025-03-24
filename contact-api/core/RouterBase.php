<?php

namespace core;

use Exception;
use src\Middlewares\ExceptionMiddleware;
use Throwable;

class RouterBase
{
    public function run()
    {
        try {
            // ✅ Middleware CORS sempre antes de tudo
            (new \src\Middlewares\CorsMiddleware())->handle();
            
            $routes = Route::getRoutes();
            $this->handleRequest($routes);
        } catch (Throwable $e) {
            (new ExceptionMiddleware())->handle($e);
        }
    }

    protected function handleRequest($routes)
    {
        try {
            $method = Request::getMethod();
            $url = Request::getUrl();
            // 1️⃣ Verifica se a rota é estática (sem parâmetros dinâmicos)
            if (isset($routes[$method][$url])) {
                return $this->executeRoute($routes[$method][$url]);
            }

            // 2️⃣ Caso contrário, busca rotas dinâmicas
            $route = $this->matchDynamicRoute($routes[$method] ?? [], $url);

            if (!$route) {
                throw new Exception("A rota '{$url}' com methodo '{$method}' não foi encontrada.", 404);
            }

            return $this->executeRoute($route);
        } catch (Throwable $e) {
            (new ExceptionMiddleware())->handle($e);
        }
    }

    protected function matchDynamicRoute($routes, $url)
    {
        foreach ($routes as $routePattern => $route) {
            // Captura os nomes dos parâmetros {param}
            preg_match_all('/\{([^\/]+)\}/', $routePattern, $paramNames);
            $paramNames = $paramNames[1] ?? [];

            // Transforma {param} em regex para capturar valores dinâmicos
            $pattern = preg_replace('/\{([^\/]+)\}/', '([^/]+)', $routePattern);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $url, $matches)) {
                array_shift($matches); // Remove a URL completa da correspondência

                // Garante que o número de valores e chaves é o mesmo
                if (count($paramNames) === count($matches)) {
                    $params = array_combine($paramNames, $matches);
                } else {
                    $params = [];
                }

                // Adiciona os parâmetros no array da rota
                $route['params'] = $params;
                return $route;
            }
        }
        return false;
    }
    protected function executeRoute($route)
    {
        $middlewares = $route['middlewares'] ?? [];
        $action = $route['action'] ?? null;
        $params = $route['params'] ?? [];

        // 1️⃣ Executa os middlewares antes do Controller
        foreach ($middlewares as $middleware) {
            $middlewareInstance = new $middleware();
            if (method_exists($middlewareInstance, 'handle')) {
                $middlewareInstance->handle();
            }
        }

        // 2️⃣ Verifica se a ação é um Controller + Método
        if (is_array($action) && count($action) === 2) {
            [$controller, $method] = $action;

            if (!class_exists($controller)) {
                throw new Exception("Controller '{$controller}' não encontrado.");
            }

            $controllerInstance = new $controller();

            if (!method_exists($controllerInstance, $method)) {
                throw new Exception("Método '{$method}' não encontrado no controller '{$controller}'.");
            }

            // 🚀 Passa os parâmetros como um único array associativo
            return call_user_func_array([$controllerInstance, $method], [$params]);
        }

        // 3️⃣ Se for uma função anônima, chama normalmente passando o array associativo
        if (is_callable($action)) {
            return call_user_func_array($action, [$params]);
        }

        throw new Exception("Ação inválida para a rota.");
    }
}
