<?php

namespace src\Services;

use src\Models\MysqlLocal;
use Exception;

class AuthService
{
    private string $secret = '123456789987654321'; // coloque no .env idealmente
    private string $issuer = 'http://localhost';

    public function login(string $email, string $senha)
    {
        $db = new MysqlLocal();
        $db->consult('usuarios_api', '*', "WHERE email = '" . addslashes($email) . "'");

        if ($db->rows == 0) {
            throw new Exception("Usuário não encontrado.");
        }

        $usuario = mysqli_fetch_assoc($db->mysqry);

        if (!password_verify($senha, $usuario['senha'])) {
            throw new Exception("Credenciais inválidas.");
        }

        if ((int)$usuario['ativo'] !== 1 || (int)$usuario['bloqueado'] === 1) {
            throw new Exception("Usuário inativo ou bloqueado.");
        }

        $payload = [
            'iss' => $this->issuer,
            'sub' => $usuario['id'],
            'email' => $usuario['email'],
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24)
        ];

        $token = $this->gerarJWT($payload);

        return [
            'token' => $token,
            'expira_em' => $payload['exp'],
            'usuario' => [
                'id' => $usuario['id'],
                'nome' => $usuario['nome'],
                'email' => $usuario['email']
            ]
        ];
    }

    public function gerarJWT(array $payload): string
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];

        $base64UrlHeader = rtrim(strtr(base64_encode(json_encode($header)), '+/', '-_'), '=');
        $base64UrlPayload = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');

        $signature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", $this->secret, true);
        $base64UrlSignature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

        return "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";
    }

    public function validarJWT(string $token): array
    {
        $partes = explode('.', $token);
        if (count($partes) !== 3) {
            throw new Exception("Token JWT inválido (estrutura errada)");
        }

        [$headerB64, $payloadB64, $assinaturaB64] = $partes;

        $assinaturaEsperada = rtrim(strtr(
            base64_encode(
                hash_hmac('sha256', "$headerB64.$payloadB64", $this->secret, true)
            ),
            '+/',
            '-_'
        ), '=');

        if (!hash_equals($assinaturaEsperada, $assinaturaB64)) {
            throw new Exception("Assinatura do token inválida");
        }

        $payloadJson = base64_decode(strtr($payloadB64, '-_', '+/'));
        $payload = json_decode($payloadJson, true);

        if (!$payload || time() > $payload['exp']) {
            throw new Exception("Token expirado");
        }

        return $payload;
    }
}
