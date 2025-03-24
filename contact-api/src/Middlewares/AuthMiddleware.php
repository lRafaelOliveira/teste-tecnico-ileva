<?php

namespace src\Middlewares;

use Exception;
use src\Services\AuthService;

class AuthMiddleware
{
    public function handle()
    {
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            throw new Exception("Token de autenticação não fornecido.", 401);
        }

        $authorizationHeader = $headers['Authorization'];

        // Verifica se começa com 'Bearer '
        if (!str_starts_with($authorizationHeader, 'Bearer ')) {
            throw new Exception("Formato de token inválido.", 401);
        }

        // Extrai o token puro
        $token = trim(str_replace('Bearer', '', $authorizationHeader));

        // Valida o token
        $authService = new AuthService();
        $payload = $authService->validarJWT($token);

        // Se chegou aqui, o token é válido — pode guardar payload em session/global se quiser
        // Exemplo: guardar ID do usuário autenticado para uso posterior
        $_SERVER['usuario_autenticado'] = $payload;
    }
}
