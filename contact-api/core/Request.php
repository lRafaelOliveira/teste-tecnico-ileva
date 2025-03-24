<?php

namespace core;

use Exception;

class Request
{
    private array $data;
    private array|bool $files;

    public function __construct()
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        $this->data = [];
        $this->files = false;
        switch ($method) {
            case 'GET':
                $this->data = $_GET;
                $r = file_get_contents('php://input');
                $decodedData = json_decode($r, true);
                // Verificar se o retorno do json_decode é um array
                if (is_array($decodedData)) {
                    $this->data = [...$this->data, ...$decodedData];
                }
                break;
            case 'POST':
                $this->data = $_POST;
                $r = file_get_contents('php://input');
                $decodedData = json_decode($r, true);
                // Verificar se o retorno do json_decode é um array
                if (is_array($decodedData)) {
                    $this->data = [...$this->data, ...$decodedData];
                }
                break;

            case 'PUT':
                $this->data = $_POST;
                $r = file_get_contents('php://input');
                $decodedData = json_decode($r, true);
                // Verificar se o retorno do json_decode é um array
                if (is_array($decodedData)) {
                    $this->data = [...$this->data, ...$decodedData];
                }
                break;
            case 'DELETE':
            case 'PATCH':
                parse_str(file_get_contents('php://input'), $this->data);
                break;

            default:
                break;
        }
        unset($this->data['request']);
        // Processa os arquivos enviados (se houver)
        $this->processFiles();
    }
    /**
     * Processa os arquivos enviados na requisição e organiza a estrutura.
     */
    private function processFiles()
    {
        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $file) {
                // Se for um único arquivo ou múltiplos arquivos
                if (is_array($file['name'])) {
                    // Caso sejam múltiplos arquivos
                    $this->files[$key] = [];
                    $fileCount = count($file['name']);
                    for ($i = 0; $i < $fileCount; $i++) {
                        if ($file['error'][$i] === UPLOAD_ERR_OK) {
                            // Se o arquivo foi enviado com sucesso
                            $this->files[$key][] = [
                                'name' => $file['name'][$i],
                                'full_path' => $file['name'][$i],  // Para o caminho, se necessário
                                'type' => $file['type'][$i],
                                'tmp_name' => $file['tmp_name'][$i],
                                'error' => $file['error'][$i],
                                'size' => $file['size'][$i]
                            ];
                        }
                    }
                    if (count($this->files[$key]) === 0) {
                        unset($this->files[$key]);
                    }
                } else {
                    // Caso seja um único arquivo
                    if ($file['error'] === UPLOAD_ERR_OK) {
                        $this->files[$key] = [
                            'name' => $file['name'],
                            'full_path' => $file['name'],  // Para o caminho, se necessário
                            'type' => $file['type'],
                            'tmp_name' => $file['tmp_name'],
                            'error' => $file['error'],
                            'size' => $file['size']
                        ];
                    }
                }
            }
        }
    }
    /**
     * Retorna a URL da requisição.
     */
    public static function getUrl()
    {
        try {
            // Pegar a URL completa ou definir uma string vazia caso seja null
            $url = $_SERVER['PATH_INFO'] ?? $_SERVER['REQUEST_URI'] ?? '/';
            if (!$url) {
                throw new Exception("Erro ao pegar a URL da requisição ", 500);
            }
            return $url;
        } catch (\Exception $e) {
            throw new Exception("Erro ao pegar a URL da requisição", 500);
        }
    }

    /**
     * Retorna todos os dados da requisição.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Retorna um dado específico da requisição.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function input(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Verifica se a chave existe na requisição.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Retorna apenas os dados especificados.
     *
     * @param array $keys
     * @return array
     */
    public function only(array $keys): array
    {
        return array_intersect_key($this->data, array_flip($keys));
    }

    /**
     * Exclui os dados especificados e retorna o restante.
     *
     * @param array $keys
     * @return array
     */
    public function except(array $keys): array
    {
        return array_diff_key($this->data, array_flip($keys));
    }

    /**
     * Retorna todos os dados enviados no método HTTP atual (GET ou POST).
     *
     * @return array
     */
    public static function getMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Retorna os arquivos enviados na requisição.
     *
     * @return array
     */
    public function files(): array
    {
        return $this->files ?? [];
    }
    /**
     * Retorna os arquivos enviados na requisição.
     *
     * @return array
     */
    public function file($key): array
    {
        return $this->files[$key] ?? [];
    }
    /**
     * Verifica se um arquivo foi enviado na requisição.
     *
     * @param string $key
     * @return bool
     */
    public function hasFile(string $key): bool
    {
        return isset($this->files[$key]);
    }
}
