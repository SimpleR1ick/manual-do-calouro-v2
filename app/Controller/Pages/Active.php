<?php

namespace App\Controller\Pages;

use App\Http\Request;
use App\Utils\View;

class Active extends Page {

    /**
     * Método responsável por retornar o contéudo (view) da página sobre
     * @return  
     */
    public static function getActive(Request $request): string {
        print_r($request->getQueryParams());

        exit;

        $request->getRouter()->redirect('/');
    }
}