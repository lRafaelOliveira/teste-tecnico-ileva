<?php

use core\Route;
use src\Controllers\AuthController;
use src\Controllers\ContatoController;
use src\Controllers\UsuarioController;
use src\Middlewares\AuthMiddleware;

Route::get('/', [AuthController::class, 'index']);
Route::post('/login', [AuthController::class, 'login']);

// Rotas Protegidas por Autenticação
Route::middleware(AuthMiddleware::class)->group(function () {
    Route::get('/usuarios', [UsuarioController::class, 'index']);
    Route::get('/usuarios/{id}', [UsuarioController::class, 'show']);
    Route::post('/usuarios', [UsuarioController::class, 'store']);
    Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);

    // ✅ CONTATOS
    Route::post('/contatos', [ContatoController::class, 'store'])->name('contatos.store');
    Route::get('/contatos', [ContatoController::class, 'index'])->name('contatos.index');
    Route::get('/contatos/find/{id}', [ContatoController::class, 'show'])->name('contatos.show');
    Route::put('/contatos/{id}', [ContatoController::class, 'update'])->name('contatos.update');
    Route::delete('/contatos/{id}', [ContatoController::class, 'delete'])->name('contatos.delete');

    Route::post('/contatos/{id}/telefones', [ContatoController::class, 'adicionarTelefone'])->name('contatos.telefone.store');
    Route::post('/contatos/{id}/emails',    [ContatoController::class, 'adicionarEmail'])->name('contatos.email.store');
    Route::post('/contatos/{id}/enderecos', [ContatoController::class, 'adicionarEndereco'])->name('contatos.endereco.store');

    Route::get('/contatos/email', [ContatoController::class, 'buscarPorEmail'])->name('contatos.buscar.email');
    Route::get('/contatos/telefone', [ContatoController::class, 'buscarPorTelefone'])->name('contatos.buscar.telefone');

    Route::put('/contatos/telefones/{telefone_id}', [ContatoController::class, 'atualizarTelefone'])->name('telefones.update');
    Route::put('/contatos/emails/{email_id}',       [ContatoController::class, 'atualizarEmail'])->name('emails.update');
    Route::put('/contatos/enderecos/{endereco_id}', [ContatoController::class, 'atualizarEndereco'])->name('enderecos.update');

    Route::delete('contatos/telefones/{telefone_id}', [ContatoController::class, 'deletarTelefone'])->name('telefones.destroy');
    Route::delete('contatos/emails/{email_id}',       [ContatoController::class, 'deletarEmail'])->name('emails.destroy');
    Route::delete('contatos/enderecos/{endereco_id}', [ContatoController::class, 'deletarEndereco'])->name('enderecos.destroy');

});
