<?php

namespace core;

class Controller
{
    protected Request $request;

    public function __construct()
    {
        // Inicializa a propriedade $request
        $this->request = new Request();
    }
    /**
     * Acessa a classe Request.
     *
     * @return Request
     */
    protected function request(): Request
    {
        // Garante que $request está inicializada
        if (!isset($this->request)) {
            $this->request = new Request();
        }
        return $this->request;
    }


    protected function jsonResponse(array $data, int $status = 200)
    {
        // Retorna a resposta como JSON
        header('Content-Type: application/json');
        http_response_code($status); // Define o código de status HTTP
        echo json_encode($data); // Codifica os dados em JSON e os exibe
        exit; // Finaliza a execução após a resposta
    }
}
