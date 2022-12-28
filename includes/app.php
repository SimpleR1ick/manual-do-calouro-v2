<?php
// LOAD COMPOSER
require __DIR__.'/../vendor/autoload.php';

use App\Http\Middleware\Queue as MiddlewareQueue;
use App\Utils\View;
use App\Utils\Database;
use App\Utils\Environment;

// CARREGA VARIAVEIS DE AMBIENTE
Environment::load(__DIR__.'/../');

// DADOS DE CONEXÃO COM O BANCO
Database::config(
    getenv('DB_HOST'),
    getenv('DB_NAME'),
    getenv('DB_USER'),
    getenv('DB_PASS'),
    getenv('DB_PORT')
);

// DEFINE A CONSTANTE DE URL DO PROJETO
define('URL', getenv('URL'));

// DEFINE O MAPEAMENTO DE MIDDLEWARES DISPONIVEIS
MiddlewareQueue::setMap([
    'admin-logout' => \App\Http\Middleware\AdminLogout::class,
    'admin-login'  => \App\Http\Middleware\AdminLogin::class,
    'api'          => \App\Http\Middleware\Api::class,
    'basic-auth'   => \App\Http\Middleware\BasicAuth::class,
    'cache'        => \App\Http\Middleware\Cache::class,
    'jwt-auth'     => \App\Http\Middleware\JWTAuth::class,
    'maintenence'  => \App\Http\Middleware\Maintenence::class,
    'user-login'   => \App\Http\Middleware\UserLogin::class,
    'user-logout'  => \App\Http\Middleware\UserLogout::class,
]);

// DEFINE O MAPEAMENTO DE MIDDLEWARES EM TODAS AS ROTAS
MiddlewareQueue::setDefault([
    'maintenence'
]);

// DEFINE O VALOR PADRÃO DAS VARIAVEIS
View::init([
    'URL' => URL
]);