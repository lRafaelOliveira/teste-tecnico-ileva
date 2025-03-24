<?php

namespace src\controllers;

use core\Controller;
use src\Services\ContatoService;
use Exception;

class ContatoController extends Controller
{
    private ContatoService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new ContatoService();
    }

    public function index()
    {
        try {
            $usuarioId = $_SERVER['usuario_autenticado']['sub'];
            $contatos = $this->service->listar($usuarioId);
            return $this->jsonResponse($contatos);
        } catch (Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()],  400);
        }
    }

    public function show($params)
    {
        try {
            $usuarioId = $_SERVER['usuario_autenticado']['sub'];
            $contato = $this->service->buscar((int)$params['id'], $usuarioId);
            return $this->jsonResponse($contato);
        } catch (Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()],  400);
        }
    }
    public function buscarPorEmail()
    {
        try {
            $email = $this->request()->input('email');
            if (!$email) {
                throw new Exception("Informe o e-mail", 422);
            }
            $usuarioId = $_SERVER['usuario_autenticado']['sub'];
            $contato = $this->service->buscarPorEmail($email, $usuarioId);

            return $this->jsonResponse($contato);
        } catch (Exception $e) {
            return $this->jsonResponse(['error1' => $e->getMessage()],  400);
        }
    }

    public function buscarPorTelefone()
    {
        try {
            $numero = $this->request()->input('numero');
            if (!$numero) {
                throw new Exception("Informe o número", 422);
            }

            $usuarioId = $_SERVER['usuario_autenticado']['sub'];
            $contato = $this->service->buscarPorTelefone($numero, $usuarioId);

            return $this->jsonResponse($contato);
        } catch (Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()],  400);
        }
    }

    public function store()
    {
        try {
            $data = $this->request()->all();
            $data['usuario_id'] = $_SERVER['usuario_autenticado']['sub'];

            $id = $this->service->criar($data);
            return $this->jsonResponse(['id' => $id], 201);
        } catch (Exception $e) {

            return $this->jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    public function update($params)
    {
        try {
            $data = $this->request()->all();
            $usuarioId = $_SERVER['usuario_autenticado']['sub'];

            $this->service->atualizar((int)$params['id'], $usuarioId, $data);
            return $this->jsonResponse(['status' => 'atualizado']);
        } catch (Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()],  400);
        }
    }


    public function deletar($params)
    {
        try {
            $usuarioId = $_SERVER['usuario_autenticado']['sub'];

            $this->service->deletar((int)$params['id'], $usuarioId);
            return $this->jsonResponse(['status' => 'deletado']);
        } catch (Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()],  400);
        }
    }

    public function adicionarTelefone($params)
    {
        try {
            $data = $this->request()->all();
            $contatoId = (int)$params['id'];
            $usuarioId = $_SERVER['usuario_autenticado']['sub'];

            // Verifica se o contato pertence ao usuário
            $contato = $this->service->buscar($contatoId, $usuarioId); // dispara 404 se não for dele

            $this->service->adicionarNovoTelefone($contatoId, $data);

            return $this->jsonResponse(['status' => 'telefone adicionado'], 201);
        } catch (Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()],  400);
        }
    }
    public function adicionarEmail($params)
    {
        try {
            $contatoId = (int)$params['id'];
            $usuarioId = $_SERVER['usuario_autenticado']['sub'];
            $data = $this->request()->all();

            // Verifica se o contato pertence ao usuário
            $this->service->buscar($contatoId, $usuarioId);

            $this->service->adicionarNovoEmail($contatoId, $data);
            return $this->jsonResponse(['status' => 'email adicionado'], 201);
        } catch (Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()],  400);
        }
    }

    public function adicionarEndereco($params)
    {
        try {
            $contatoId = (int)$params['id'];
            $usuarioId = $_SERVER['usuario_autenticado']['sub'];
            $data = $this->request()->all();

            // Verifica se o contato pertence ao usuário
            $this->service->buscar($contatoId, $usuarioId);

            $this->service->adicionarNovoEndereco($contatoId, $data);
            return $this->jsonResponse(['status' => 'endereço adicionado'], 201);
        } catch (Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()],  400);
        }
    }
    public function atualizarTelefone($params)
    {
        try {
            $id = (int)$params['telefone_id'];
            $usuarioId = $_SERVER['usuario_autenticado']['sub'];
            $this->service->atualizarTelefone($id, $usuarioId, $this->request()->all());
            return $this->jsonResponse(['status' => 'telefone atualizado']);
        } catch (Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()],  400);
        }
    }

    public function atualizarEmail($params)
    {
        try {
            $id = (int)$params['email_id'];
            $usuarioId = $_SERVER['usuario_autenticado']['sub'];
            $this->service->atualizarEmail($id, $usuarioId, $this->request()->all());
            return $this->jsonResponse(['status' => 'email atualizado']);
        } catch (Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()],  400);
        }
    }

    public function atualizarEndereco($params)
    {
        try {
            $id = (int)$params['endereco_id'];
            $usuarioId = $_SERVER['usuario_autenticado']['sub'];
            $this->service->atualizarEndereco($id, $usuarioId, $this->request()->all());
            return $this->jsonResponse(['status' => 'endereço atualizado']);
        } catch (Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()],  400);
        }
    }

    public function deletarTelefone($params)
    {
        try {
            $id = (int)$params['telefone_id'];
            $usuarioId = $_SERVER['usuario_autenticado']['sub'];
            $this->service->deletarTelefone($id, $usuarioId);
            return $this->jsonResponse(['status' => 'telefone deletado']);
        } catch (Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()],  400);
        }
    }

    public function deletarEmail($params)
    {
        try {
            $id = (int)$params['email_id'];
            $usuarioId = $_SERVER['usuario_autenticado']['sub'];
            $this->service->deletarEmail($id, $usuarioId);
            return $this->jsonResponse(['status' => 'email deletado']);
        } catch (Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()],  400);
        }
    }

    public function deletarEndereco($params)
    {
        try {
            $id = (int)$params['endereco_id'];
            $usuarioId = $_SERVER['usuario_autenticado']['sub'];
            $this->service->deletarEndereco($id, $usuarioId);
            return $this->jsonResponse(['status' => 'endereço deletado']);
        } catch (Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()],  400);
        }
    }
}
