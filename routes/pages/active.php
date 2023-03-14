<?php

use App\Http\Response;
use App\Controller\Pages;

// ROTA SOBRE
$obRouter->get('/active', [
    function($request) {
        return new Response(200, Pages\Active::getActive($request));
    }
]);
