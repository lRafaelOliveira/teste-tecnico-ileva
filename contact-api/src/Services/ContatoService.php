<?php

namespace src\Services;

use src\Models\MysqlLocal;
use Exception;

class ContatoService
{
    private $db;

    public function __construct()
    {
        $this->db = new MysqlLocal();
    }

    public function criar(array $data)
    {
        if (!isset($data['nome'], $data['usuario_id'])) {
            throw new Exception("Campos obrigatórios: nome, usuario_id");
        }

        // Insere o contato
        $nome = addslashes($data['nome']);
        $usuario_id = intval($data['usuario_id']);
        $this->db->add('contatos', "nome = '$nome', usuario_id = $usuario_id");
        $contato_id = $this->db->ulid;

        // Telefones
        if (!empty($data['telefones']) && is_array($data['telefones'])) {
            foreach ($data['telefones'] as $tel) {
                $this->adicionarTelefone($contato_id, $tel);
            }
        }

        // E-mails
        if (!empty($data['emails']) && is_array($data['emails'])) {
            foreach ($data['emails'] as $email) {
                $this->adicionarEmail($contato_id, $email);
            }
        }

        // Endereços
        if (!empty($data['enderecos']) && is_array($data['enderecos'])) {
            foreach ($data['enderecos'] as $end) {
                $this->adicionarEndereco($contato_id, $end);
            }
        }

        return $contato_id;
    }

    public function listar(int $usuarioId): array
    {
        $this->db->consult('contatos', '*', "WHERE usuario_id = $usuarioId");
        $dados = [];

        while ($row = mysqli_fetch_assoc($this->db->mysqry)) {
            $dados[] = $row;
        }

        return $dados;
    }
    public function buscar(int $id, int $usuarioId): array
    {
        $this->db->consult('contatos', '*', "WHERE id = $id AND usuario_id = $usuarioId");

        if ($this->db->rows === 0) {
            throw new Exception("Nenhum Contato encontrado", 404);
        }

        $contato = mysqli_fetch_assoc($this->db->mysqry);

        $contato['telefones'] = $this->buscarFilhos('telefones', $id);
        $contato['emails']    = $this->buscarFilhos('emails', $id);
        $contato['enderecos'] = $this->buscarFilhos('enderecos', $id);

        return $contato;
    }
    public function buscarPorEmail(string $email, int $usuarioId): array
    {
        $email = addslashes($email);

        $sql = "
            SELECT DISTINCT c.id
            FROM contatos c
            INNER JOIN emails e ON e.contato_id = c.id
            WHERE e.email = '$email' AND c.usuario_id = $usuarioId
        ";

        $this->db->execute($sql);

        if ($this->db->rows === 0) {
            throw new Exception("Nenhum contato encontrado com esse e-mail", 404);
        }

        $ids = [];
        while ($row = mysqli_fetch_assoc($this->db->mysqry)) {
            $ids[] = (int)$row['id'];
        }

        $contatos = [];
        foreach ($ids as $id) {
            $contatos[] = $this->buscar($id, $usuarioId); // agora seguro!
        }

        return $contatos;
    }
    public function buscarPorTelefone(string $numero, int $usuarioId): array
    {
        $numero = addslashes($numero);

        $sql = "
            SELECT DISTINCT c.id
            FROM contatos c
            INNER JOIN telefones t ON t.contato_id = c.id
            WHERE t.numero = '$numero' AND c.usuario_id = $usuarioId
        ";

        $this->db->execute($sql);

        if ($this->db->rows === 0) {
            throw new Exception("Nenhum contato encontrado com esse telefone", 404);
        }

        $ids = [];
        while ($row = mysqli_fetch_assoc($this->db->mysqry)) {
            $ids[] = (int)$row['id'];
        }

        $contatos = [];
        foreach ($ids as $id) {
            $contatos[] = $this->buscar($id, $usuarioId);
        }

        return $contatos;
    }

    public function atualizar(int $id, int $usuarioId, array $data)
    {
        // Verifica se o contato pertence ao usuário
        $this->db->consult('contatos', '*', "WHERE id = $id AND usuario_id = $usuarioId");
        if ($this->db->rows === 0) {
            throw new Exception("Nenhum Contato encontrado para Atualizar", 404);
        }

        // Atualiza nome
        if (!empty($data['nome'])) {
            $nome = addslashes($data['nome']);
            $this->db->update('contatos', "nome = '$nome'", "WHERE id = $id");
        }

        // Deleta os dados antigos
        $this->db->del('telefones', "WHERE contato_id = $id");
        $this->db->del('emails', "WHERE contato_id = $id");
        $this->db->del('enderecos', "WHERE contato_id = $id");

        // Insere os novos
        foreach ($data['telefones'] ?? [] as $tel) {
            $this->adicionarTelefone($id, $tel);
        }

        foreach ($data['emails'] ?? [] as $email) {
            $this->adicionarEmail($id, $email);
        }

        foreach ($data['enderecos'] ?? [] as $end) {
            $this->adicionarEndereco($id, $end);
        }

        return true;
    }

    public function deletar(int $id, int $usuarioId)
    {
        $this->db->consult('contatos', '*', "WHERE id = $id AND usuario_id = $usuarioId");
        if ($this->db->rows === 0) {
            throw new Exception("Contato não encontrado", 404);
        }

        $this->db->del('contatos', "WHERE id = $id");
        return true;
    }
    public function adicionarTelefone($contato_id, array $data)
    {
        if (!isset($data['numero'])) return;

        $numero = addslashes($data['numero']);
        $tipo = isset($data['tipo']) ? addslashes($data['tipo']) : 'celular';
        $obs = isset($data['observacao']) ? "'" . addslashes($data['observacao']) . "'" : 'NULL';

        $this->db->add('telefones', "
            contato_id = $contato_id,
            tipo = '$tipo',
            numero = '$numero',
            observacao = $obs
        ");
    }

    public function adicionarEmail($contato_id, array $data)
    {
        if (!isset($data['email'])) return;

        $email = addslashes($data['email']);
        $tipo = isset($data['tipo']) ? addslashes($data['tipo']) : 'pessoal';
        $obs = isset($data['observacao']) ? "'" . addslashes($data['observacao']) . "'" : 'NULL';

        $this->db->add('emails', "
            contato_id = $contato_id,
            email = '$email',
            tipo = '$tipo',
            observacao = $obs
        ");
    }

    public function adicionarEndereco($contato_id, array $data)
    {
        if (!isset($data['rua'], $data['cidade'], $data['estado'])) return;

        $campos = [
            "contato_id = $contato_id",
            "tipo = '" . addslashes($data['tipo'] ?? 'outro') . "'",
            "rua = '" . addslashes($data['rua']) . "'",
            "numero = '" . addslashes($data['numero'] ?? '') . "'",
            "complemento = '" . addslashes($data['complemento'] ?? '') . "'",
            "bairro = '" . addslashes($data['bairro'] ?? '') . "'",
            "cidade = '" . addslashes($data['cidade']) . "'",
            "estado = '" . addslashes($data['estado']) . "'",
            "cep = '" . addslashes($data['cep'] ?? '') . "'",
            "observacao = " . (isset($data['observacao']) ? "'" . addslashes($data['observacao']) . "'" : "NULL")
        ];

        $this->db->add('enderecos', implode(', ', $campos));
    }
    private function buscarFilhos(string $tabela, int $contatoId): array
    {
        $this->db->consult($tabela, '*', "WHERE contato_id = $contatoId");
        $dados = [];

        while ($row = mysqli_fetch_assoc($this->db->mysqry)) {
            $dados[] = $row;
        }

        return $dados;
    }

    public function adicionarNovoTelefone(int $contatoId, array $data)
    {
        $this->validarContato($contatoId);

        if (!isset($data['numero'])) {
            throw new Exception("Número do telefone é obrigatório.");
        }

        $this->adicionarTelefone($contatoId, $data);
    }

    public function adicionarNovoEmail(int $contatoId, array $data)
    {
        $this->validarContato($contatoId);

        if (!isset($data['email'])) {
            throw new Exception("E-mail é obrigatório.");
        }

        $this->adicionarEmail($contatoId, $data);
    }

    public function adicionarNovoEndereco(int $contatoId, array $data)
    {
        $this->validarContato($contatoId);

        if (!isset($data['rua'], $data['cidade'], $data['estado'])) {
            throw new Exception("Rua, cidade e estado são obrigatórios.");
        }

        $this->adicionarEndereco($contatoId, $data);
    }
    public function atualizarTelefone(int $telefoneId, int $usuarioId, array $data)
    {
        $this->verificaDonoDoTelefone($telefoneId, $usuarioId);

        if (!isset($data['numero'])) {
            throw new Exception("Número do telefone é obrigatório.");
        }

        $tipo = isset($data['tipo']) ? addslashes($data['tipo']) : 'celular';
        $numero = addslashes($data['numero']);
        $obs = isset($data['observacao']) ? "'" . addslashes($data['observacao']) . "'" : 'NULL';

        $this->db->update('telefones', "
            tipo = '$tipo',
            numero = '$numero',
            observacao = $obs
        ", "WHERE id = $telefoneId");
    }
    public function atualizarEmail(int $emailId, int $usuarioId, array $data)
    {
        $this->verificaDonoDoEmail($emailId, $usuarioId);

        if (!isset($data['email'])) {
            throw new Exception("E-mail é obrigatório.");
        }

        $email = addslashes($data['email']);
        $tipo = isset($data['tipo']) ? addslashes($data['tipo']) : 'pessoal';
        $obs = isset($data['observacao']) ? "'" . addslashes($data['observacao']) . "'" : 'NULL';

        $this->db->update('emails', "
            email = '$email',
            tipo = '$tipo',
            observacao = $obs
        ", "WHERE id = $emailId");
    }
    public function atualizarEndereco(int $enderecoId, int $usuarioId, array $data)
    {
        $this->verificaDonoDoEndereco($enderecoId, $usuarioId);

        $campos = [];

        foreach (['tipo', 'rua', 'numero', 'complemento', 'bairro', 'cidade', 'estado', 'cep'] as $campo) {
            if (isset($data[$campo])) {
                $campos[] = "$campo = '" . addslashes($data[$campo]) . "'";
            }
        }

        if (isset($data['observacao'])) {
            $campos[] = "observacao = '" . addslashes($data['observacao']) . "'";
        }

        if (empty($campos)) {
            throw new Exception("Nenhum campo para atualizar.");
        }

        $this->db->update('enderecos', implode(', ', $campos), "WHERE id = $enderecoId");
    }
    public function deletarTelefone(int $telefoneId, int $usuarioId)
    {
        $this->verificaDonoDoTelefone($telefoneId, $usuarioId);
        $this->db->del('telefones', "WHERE id = $telefoneId");
    }
    public function deletarEmail(int $emailId, int $usuarioId)
    {
        $this->verificaDonoDoEmail($emailId, $usuarioId);
        $this->db->del('emails', "WHERE id = $emailId");
    }
    public function deletarEndereco(int $enderecoId, int $usuarioId)
    {
        $this->verificaDonoDoEndereco($enderecoId, $usuarioId);
        $this->db->del('enderecos', "WHERE id = $enderecoId");
    }


    private function validarContato(int $contatoId)
    {
        $this->db->consult('contatos', 'id', "WHERE id = $contatoId");

        if ($this->db->rows === 0) {
            throw new Exception("Contato não encontrado.", 404);
        }
    }
    private function verificaDonoDoTelefone(int $telefoneId, int $usuarioId)
    {
        $sql = "
            SELECT t.id 
            FROM telefones t
            INNER JOIN contatos c ON c.id = t.contato_id
            WHERE t.id = $telefoneId AND c.usuario_id = $usuarioId
        ";
        $this->db->execute($sql);
        if ($this->db->rows === 0) {
            throw new Exception("Telefone não encontrado ou não pertence ao usuário", 403);
        }
    }

    private function verificaDonoDoEmail(int $emailId, int $usuarioId)
    {
        $sql = "
            SELECT e.id 
            FROM emails e
            INNER JOIN contatos c ON c.id = e.contato_id
            WHERE e.id = $emailId AND c.usuario_id = $usuarioId
        ";
        $this->db->execute($sql);
        if ($this->db->rows === 0) {
            throw new Exception("Email não encontrado ou não pertence ao usuário", 403);
        }
    }

    private function verificaDonoDoEndereco(int $enderecoId, int $usuarioId)
    {
        $sql = "
            SELECT e.id 
            FROM enderecos e
            INNER JOIN contatos c ON c.id = e.contato_id
            WHERE e.id = $enderecoId AND c.usuario_id = $usuarioId
        ";
        $this->db->execute($sql);
        if ($this->db->rows === 0) {
            throw new Exception("Endereço não encontrado ou não pertence ao usuário", 403);
        }
    }
}
