<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
ini_set('display_errors', 0);

date_default_timezone_set('America/Araguaina');


// Tempo limite da sessão (6 horas)
$timeout = 60 * 60 * 6;

ini_set('session.gc_maxlifetime', $timeout);
ini_set('session.cookie_lifetime', $timeout);
session_start();

// Autoload do Composer
require __DIR__ . '/vendor/autoload.php';

use core\RouterBase;

// Importa todas as rotas automaticamente
foreach (glob(__DIR__ . '/src/Routes/*.php') as $routeFile) {
    require $routeFile;
}

// Inicializa o roteador base
$router = new RouterBase();
$router->run();
