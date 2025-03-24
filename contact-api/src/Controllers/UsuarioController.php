<?php

namespace src\Controllers;

use core\Controller;
use src\Services\UsuarioService;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = (new UsuarioService())->listarTodos();
        return $this->jsonResponse($usuarios);
    }

    public function store()
    {
        $input = $this->request()->all();
        $id = (new UsuarioService())->criar($input);
        return $this->jsonResponse(['id' => $id], 201);
    }

    public function update($params)
    {
        $id = $params['id'];
        $input = $this->request()->all();

        (new UsuarioService())->atualizar($id, $input);
        return $this->jsonResponse(['status' => 'atualizado']);
    }

    public function destroy($params)
    {
        $id = $params['id'];
        (new UsuarioService())->deletar($id);
        return $this->jsonResponse(['status' => 'deletado']);
    }

    public function show($params)
    {
        $id = $params['id'];
        $user = (new UsuarioService())->buscarPorId($id);
        return $this->jsonResponse($user);
    }
}
