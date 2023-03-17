<?php

namespace App\Controller\Pages;

use App\Http\Request;
use App\Models\Chave as EntityHash;
use App\Models\Usuario as EntityUser;
use App\Utils\Sanitize;
use App\Utils\View;

class Active extends Page {

    /**
     * Método responsável por retornar o contéudo (view) da página sobre
     * @return void 
     */
    public static function getActive(Request $request): void {
        // QUERY PARAMS
        $queryParams = $request->getQueryParams();

        $chave = $queryParams['chave'];

        $obHash = EntityHash::findHash(null, $chave);

        // VALIDA A INSTÂNCIA, VERIFICANDO SE HOUVE RESULTADO 
        if (!$obHash instanceof EntityHash) {
            // REDIRECIONA PARA HOME
            $request->getRouter()->redirect('/?status=invalid_hash');
        }
        // BUSCA O USUARIO PELO O ID CADASTRADO NA CHAVE
        $obUser = EntityUser::getUserById($obHash->getFkId());

        $obUser->setFk_nivel(1);

        // VERIFICA SE O ESTADO DO USUARIO FOI ATUALIZADO E DELETA A CHAVE
        if ($obUser->updateUser()) {
            $obHash->deleteHash();
        }
        // REDIRECIONA O USUARIO PARA HOME
        $request->getRouter()->redirect('/profile?status=user_activeted');
    }
}