<?php

namespace src\Middlewares;

use Throwable;

class ExceptionMiddleware
{
    public function handle(Throwable $exception)
    {
        // Exibe a página de erro diretamente
        $this->renderError($exception);
        exit;
    }
    /**
     * Exibe a mensagem de erro na tela.
     */
    private function renderError(Throwable $exception,)
    {
        $errorCode = $exception->getCode() ?? 500;
        $errorMessage = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $trace = $exception->getTraceAsString();
        $trace = str_replace('#', '<br>#', $trace);

        header('Content-Type: application/json');
        http_response_code($errorCode); // Define o código de status HTTP
        echo json_encode([
            'error' => [
                'code' => $errorCode,
                'message' => $errorMessage,
                'file' => $file,
                'line' => $line,
                'trace' => $trace
            ]
        ]);
        exit; // Finaliza a execução após a resposta
    }
}
