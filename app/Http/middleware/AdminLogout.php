<?php

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;
use App\Utils\Session;
use Closure;

class AdminLogout {

    /**
     * Método responsavel por executar o middleware
     * @param \App\Http\Request
     * @param \Closure
     * 
     * @return \App\Http\Response
     */
    public function handle(Request $request, Closure $next): Response { 
        // VERIFICA SE O USUARIO ESTA LOGADO
        if (Session::isLogged()) {
            $request->getRouter()->redirect('/admin');
        }
        // CONTINUA A EXECUÇÃO
        return $next($request);
    }
}