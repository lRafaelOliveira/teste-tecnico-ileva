<?php

namespace src\Services;

use src\Models\MysqlLocal;
use Exception;

class UsuarioService
{
    private $db;
    private $table = 'usuarios_api';

    public function __construct()
    {
        $this->db = new MysqlLocal();
    }

    public function listarTodos()
    {
        $this->db->consult($this->table, '*', '');
        $usuarios = [];

        while ($row = mysqli_fetch_assoc($this->db->mysqry)) {
            $usuarios[] = $row;
        }

        return $usuarios;
    }

    public function buscarPorId($id)
    {
        $id = intval($id);
        $this->db->consult($this->table, '*', "WHERE id = $id");

        if ($this->db->rows == 0) {
            throw new Exception("Usuário não encontrado.");
        }

        return mysqli_fetch_assoc($this->db->mysqry);
    }

    public function criar($data)
    {
        try {


            // Defina aqui os campos obrigatórios
            $camposObrigatorios = ['nome', 'email', 'senha'];

            // Pega os campos reais da tabela
            $this->db->colunas($this->table);
            $camposTabela = [];
            while ($col = mysqli_fetch_assoc($this->db->mysqry)) {
                $camposTabela[] = $col['Field'];
            }

            // Verifica se os obrigatórios estão presentes
            foreach ($camposObrigatorios as $campo) {
                if (empty($data[$campo])) {
                    throw new Exception("Campo obrigatório ausente: $campo");
                }
            }

            $campos = [];

            foreach ($data as $key => $value) {
                if (!in_array($key, $camposTabela)) {
                    continue;
                }

                if ($key === 'senha') {
                    $value = password_hash($value, PASSWORD_DEFAULT);
                }

                $escapedValue = is_string($value) ? "'" . addslashes($value) . "'" : intval($value);
                $campos[] = "$key = $escapedValue";
            }

            // Garante campos padrão (caso não venham no $data)
            if (in_array('ativo', $camposTabela) && !isset($data['ativo'])) {
                $campos[] = "ativo = 1";
            }

            if (in_array('bloqueado', $camposTabela) && !isset($data['bloqueado'])) {
                $campos[] = "bloqueado = 0";
            }

            if (empty($campos)) {
                throw new Exception("Nenhum campo válido para inserir.");
            }

            $valores = implode(", ", $campos);
            $this->db->add($this->table, $valores);

            return $this->db->ulid;
        } catch (Exception $e) {
           throw new Exception($e->getMessage());
        }
    }


    public function atualizar($id, $data)
    {
        $id = intval($id);
        if ($id <= 0) {
            throw new Exception("ID inválido.");
        }

        // Pega os campos reais da tabela
        $this->db->colunas($this->table);
        $camposTabela = [];
        while ($col = mysqli_fetch_assoc($this->db->mysqry)) {
            $camposTabela[] = $col['Field'];
        }

        $campos = [];

        foreach ($data as $key => $value) {
            if (!in_array($key, $camposTabela)) {
                continue; // ignora campos inexistentes
            }

            if ($key === 'senha') {
                $value = password_hash($value, PASSWORD_DEFAULT);
            }

            // Aspas simples e segurança básica
            $escapedValue = is_string($value) ? "'" . addslashes($value) . "'" : intval($value);

            $campos[] = "$key = $escapedValue";
        }

        if (empty($campos)) {
            throw new Exception("Nenhum campo válido para atualizar.");
        }

        $fields = implode(', ', $campos);
        $this->db->update($this->table, $fields, "WHERE id = $id");

        return true;
    }


    public function deletar($id)
    {
        $id = intval($id);
        $this->db->del($this->table, "WHERE id = $id");
        return true;
    }
}
