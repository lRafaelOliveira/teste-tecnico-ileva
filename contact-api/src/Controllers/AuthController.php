<?php

namespace src\Controllers;

use \core\Controller;
use Exception;
use src\Services\AuthService;

class AuthController extends Controller
{
    function index()
    {
        $this->jsonResponse([
            'message' => 'API de Contatos'
        ]);
    }
    // Método para fazer o login
    function login()
    {
        $data = $this->request()->all();
        if (!isset($data['email'], $data['senha'])) {
            return $this->jsonResponse(['error' => 'Email e senha são obrigatórios'], 422);
        }
        try {
            $auth = new AuthService();
            $token = $auth->login($data['email'], $data['senha']);
            return $this->jsonResponse($token);
        } catch (Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()], 401);
        }
    }
}
