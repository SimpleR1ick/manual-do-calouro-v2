<?php

namespace App\Controller\Pages;

use App\Http\Request;
use App\Models\Mail\Email;
use App\Models\Usuario as EntityUser;
use App\Models\Chave as EntityHash;
use App\Utils\Tools\Alert;
use App\Utils\Database;
use App\Utils\Sanitize;
use App\Utils\View;
use Exception;

class SignUp extends Page {

    /**
     * Método responsável por retornar o contéudo (view) da página de cadastro
     * @param \App\Http\Request $request
     * 
     * @return string
     */
    public static function getSignUp($request): string {
        // RENDERIZA O CONTEUDO DA PAGINA DE CADASTRO
        $content = View::render('pages/signup', [
            'status' => Alert::getStatus($request)
        ]);
        // RETORNA A VIEW DA PAGINA
        return parent::getPage('Cadastro', $content);
    }

    /**
     * Método responsável por processar o formulário de cadastro
     * @param \App\Http\Request $request
     * 
     * @return void
     */
    public static function setSignUp(Request $request): void {
        // POST VARS
        $postVars = $request->getPostVars();

        // VERIFICA HTML INJECT
        if (Sanitize::validateForm($postVars)) {
            $request->getRouter()->redirect('/signup?status=invalid_chars');
        }
        // SANITIZA O ARRAY
        $postVars = Sanitize::sanitizeForm($postVars);

        // ATRIBUINDO AS VARIAVEIS
        $nome     = $postVars['nome'] ?? '';
        $email    = $postVars['email'] ?? '';
        $password = $postVars['senha'] ?? '';
        $confirm  = $postVars['senhaConfirma'] ?? '';

        // VALIDA O NOME
        if (Sanitize::validateName($nome)) {
            $request->getRouter()->redirect('/signup?status=invalid_name');
        }
        // VALIDA O EMAIL
        if (Sanitize::validateEmail($email)) {
            $request->getRouter()->redirect('/signup?status=invalid_email');
        }
        // VALIDA A SENHA
        if (Sanitize::validatePassword($password, $confirm)) {
            $request->getRouter()->redirect('/signup?status=invalid_pass');
        }
        // CONSULTA O USUARIO UTILIZANDO O EMAIL
        $obUser = EntityUser::getUserByEmail($email);

        // VALIDA A INSTANCIA, VERIFICANDO SE O EMAIL JÁ ESTA SENDO UTILIZADO
        if ($obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/signup?status=duplicated_email');
        }
        // NOVA INSTÂNCIA DE USUARIO
        $obUser = new EntityUser;

        // ATRIBUTOS DO OBJETO
        $obUser->setNom_usuario($nome);
        $obUser->setEmail($email);
        $obUser->setSenha($password);
        $obUser->setNivel(2);

        // CRIANDO UMA CONEXÃO COM BANCO DE DADOS
        $connection = (new Database);

        try {
            // INICIANDO PROCESSO DE TRANSAÇÃO
            $connection->beginTransaction();

            // VERIFICA SE O USUARIO FOI INSERIDO COM SUCESSO
            if (!$obUser->insertUserTransaction($connection)) {
                throw new Exception('Erro ao cadastrar o usuario', 0);
            }
            // INSTANCIA DA CHAVE DE CONFIRMAÇÃO
            $obHash = new EntityHash;

            $obHash->setFkId($obUser->getId_usuario());
            $obHash->setHash();

            // VERIFICA SE A CHAVE FOI CADASTRADA NA TABELA
            if (!$obHash->insertHashTransaction($connection)) {
                throw new Exception('Erro ao cadastrar a hash', 0);
            }
            // INSTANCIA DO EMAIL + CRIAÇÃO DO LINK DE ATIVAÇÃO
            $obEmail = new Email;

            $link = "<a href=".getenv('URL')."/active?chave={$obHash->getHash()}>Clique aqui</a>";

            $subject = 'Confirmar conta';
            $message = 'Clique no link para ativar sua conta'. $link;

            // VERIFICA SE O EMAIL DE CONFIRMAÇÃO FOI ENVIADO
            if (!$obEmail->sendEmail($obUser->getEmail(), $subject, $message)) {
                throw new Exception('Erro ao enviar o e-mail', 0);
            }
            // SALVA AS ALTERAÇÕES NO BANCO DE DADOS
            $connection->commit();
        }
        catch(Exception $e) {
            // REVERTE TODAS AS OPERAÇÕES
            $connection->rollback();

            $request->getRouter()->redirect('/signup?status=email_erro');
        }
        // REDIRECIONA PARA PAGINA DE LOGIN
        $request->getRouter()->redirect('/signin?status=user_registered');
    }
}