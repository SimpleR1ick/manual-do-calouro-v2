<?php

namespace App\Controller\Admin;

use App\Http\Request;
use App\Utils\Database;
use App\Utils\View;
use App\Utils\Pagination;
use App\Models\Admin as EntityAdmin;
use App\Models\Usuario as EntityUser;
use App\Models\Servidor as EntityServer;
use App\Models\Professor as EntityTeacher;
use App\Utils\Tools\Alert;
use Exception;

class User extends Page {

    /**
     * Método responsável por obter a renderização dos items de usuários para página
     * @param \App\Http\Request $request
     * @param \App\Utils\Pagination $obPagination
     * 
     * @return string
     */
    private static function getUsersItems(Request $request, &$obPagination): string {
        // USUARIOS
        $itens = '';

        // QUANTIDADE TOTAL DE REGISTROS
        $quantidadeTotal = EntityUser::getUsers(null, null, null, 'COUNT(*) AS qtd')->fetchObject()->qtd;

        // PAGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        // INSTANCIA DE PAGINAÇÃO
        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 10);

        // RESULTADOS DA PAGINA
        $results = EntityUser::getUsers(null, 'id_usuario DESC', $obPagination->getLimit());

        // RENDERIZA O ITEM
        while ($obUser = $results->fetchObject(EntityUser::class)) {
            // VIEW De DEPOIMENTOSS
            $itens .= View::render('admin/modules/users/item',[
                'click' => "onclick=deleteItem({$obUser->getId_usuario()})",
                'id'    => $obUser->getId_usuario(),
                'nome'  => $obUser->getNom_usuario(),
                'email' => $obUser->getEmail(),
            ]);
        }
        // RETORNA OS DEPOIMENTOS
        return $itens;
    }

    /**
     * Método responsável por renderizar a view de listagem de usuários
     * @param \App\Http\Request $request
     * 
     * @return string
     */
    public static function getUsers(Request $request): string {
        // CONTEUDO DA HOME
        $content = View::render('admin/modules/users/index', [
            'itens'      => self::getUsersItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status'     => Alert::getStatus($request)
        ]);

        // RETORNA A PAGINA COMPLETA
        return parent::getPanel('Usuários > MDC', $content, 'users');
    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo usuário
     * @param \App\Http\Request $request
     * 
     * @return string
     */
    public static function getNewUser(Request $request): string {
        // CONTEUDO DO FORMULARIO
        $content = View::render('admin/modules/users/form', [
            'tittle'   => 'Cadastrar Usuário',
            'status'   => Alert::getStatus($request),
            'nome'     => '',
            'email'    => '',
            'ativo'    => 'checked',
            'inativo'  => '',
            'acesso'   => '2',
            'botao'    => 'Cadastrar'
        ]);

        // RETORNA A PAGINA COMPLETA
        return parent::getPanel('Cadastrar Usuário > MDC', $content, 'users');
    }

    /**
     * Método responsável por cadastrar um usuário no banco
     * @param \App\Http\Request
     * 
     * @return void
     */
    public static function setNewUser(Request $request): void {
        // POST VARS
        $postVars = $request->getPostVars();

        $nome   = $postVars['nome'] ?? '';
        $email  = $postVars['email'] ?? '';
        $senha  = $postVars['senha'] ?? '';
        $status = $postVars['status'] ?? '';
        $ativo  = $postVars['active'] ?? '';

        // VALIDA O EMAIL DO USUÁRIO
        $obUser = EntityUser::getUserByEmail($email);

        // VALIDA A INSTANCIA DA CLASSE
        if ($obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/admin/users/new?status=duplicated_email');
        }
        // NOVA INSTANCICA DE CONEXÃO
        $connection = new Database;

        // INICIANDO TRANSAÇÃO
        $connection->beginTransaction();

        try {
            // NOVA INSTANCIA DE USUÁRIO
            $obUser = new EntityUser;

            $obUser->setNom_usuario($nome);
            $obUser->setEmail($email);
            $obUser->setSenha($senha);
            $obUser->setFk_acesso($status);
            $obUser->setNivel($ativo);

            // INSERE O USUARIO COM CONEXÃO EXISTENTE
            $obUser->insertUserTransaction($connection);

            self::registerByUserType($obUser, $connection);

            // SALVA AS ALTERAÇÕES NO BD
            $connection->commit();

        } catch(Exception $e) {
            // REVER ALTERAÇÕES NO BD
            $connection->rollBack();

            // REDIRECIONA COM MENSAGEM DE ERRO
            $request->getRouter()->redirect('/admin/users/new?status=register_error');
        }
        // REDIRECIONA O PARA EDIÇÃO COM MENSAGEM DE SUCESSO
        $request->getRouter()->redirect('/admin/users/edit/'.$obUser->getId_usuario().'?status=user_registered');
    }

    /**
     * Método responsável por retornar o formulário de edição de um usuário
     * @param \App\Http\Request
     * @param int $id
     * 
     * @return string
     */
    public static function getEditUser(Request $request, int $id): string {
        // DECLARAÇÃO DE VARIÁVEIS
        $status = [
            'ativo'   => '',
            'inativo' => ''
        ];
        
        // OBTENDO O USUÁRIO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id);

        // VALIDA A INSTANCIA
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/admin/users');
        }
        $obUser->getNivel() == 1 ? $status['ativo'] = 'checked' : $status['inativo'] = 'checked';

        // CONTEUDO DO FORMULARIO
        $content = View::render('admin/modules/users/form', [
            'status'  => Alert::getStatus($request),
            'tittle'  => 'Editar Usuário',
            'botao'   => 'Atualizar',
            'nome'    => $obUser->getNom_usuario(),
            'email'   => $obUser->getEmail(),
            'acesso'  => $obUser->getFk_acesso(),
            'ativo'   => $status['ativo'],
            'inativo' => $status['inativo'],
        ]);

        // RETORNA A PAGINA COMPLETA
        return parent::getPanel('Editar Usuário  > WDEV', $content, 'users');
    }

    /**
     * Método responsável por gravar a atualização de um usuário
     * @param \App\Http\Request
     * @param int $id
     * 
     * @return void
     */
    public static function setEditUser(Request $request, int $id): void {
        // OBTENDO O USUÁRIO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id);

        // VALIDA A INSTANCIA
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/admin/users');
        }
        // POST VARS
        $postVars = $request->getPostVars();

        $nome   = $postVars['nome'] ?? '';
        $email  = $postVars['email'] ?? '';
        $senha  = $postVars['senha'] ?? '';
        $active = $postVars['active'] ?? '';
        $status = $postVars['status'] ?? '';

        // VALIDA O EMAIL DO USUÁRIO
        $obUserEmail = EntityUser::getUserByEmail($email);

        // VERIFICA SE A INSTANCIA E VALIDA E SE O ID E DIFERENTE DO ATUAL
        if ($obUserEmail instanceof EntityUser && $obUserEmail->getId_usuario() != $id) {
            $request->getRouter()->redirect('/admin/users/edit/'.$id.'?status=duplicated_email');
        }
        
        // ATUALIZA A INSTANCIA
        $obUser->setNom_usuario($nome);
        $obUser->setEmail($email);
        $obUser->setSenha($senha);
        $obUser->setNivel($active);
        $obUser->setFk_acesso($status);

        $obUser->updateUser();

        // REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/users/edit/'.$id.'?status=user_updated');
    }

    /**
     * Método responsável por excluir um usuário
     * @param \App\Http\Request $request
     * 
     * @return void
     */
    public static function setDeleteUser(Request $request): void {
        // POST VARS
        $postVars = $request->getPostVars();
       
        // OBTENDO O USUÁRIO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($postVars['id']);

        // VALIDA A INSTANCIA
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/admin/users');
        }
        // EXCLUIR DEPOIMENTO
        $obUser->deleteUser();

        // REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/users?status=user_deleted');
    }

    /**
     * Método responsável por inserir um usuário professor e administrador nas tabelas de herança
     * @param \App\Models\Usuario $obUser
     * @param \App\Utils\Database $conn
     * 
     * @return void
     */
    private static function registerByUserType(EntityUser $obUser, Database $conn): void {
        // OBTEM O ID DO USUÁRIO
        $id = $obUser->getId_usuario();
        $lv = $obUser->getFk_acesso();

        // NOVA INSTANCIA DE SERVIDOR
        $obServer = new EntityServer;

        $obServer->setFk_id_usuario($id);
        $obServer->setFk_id_sala(1);

        // VERIFICA SE A INSERÇÃO OCORREU COM SUCESSO
        if (!$obServer->insertServerTransaction($conn)) {
            throw new Exception("Erro ao inserir servidor!", 0);
        }
        // VERIFICA O ACESSO DO USUARIO
        switch ($lv) {
            case 4: 
                // ADMINISTRATIVO
                $obAdmin = new EntityAdmin;
                $obAdmin->setFk_id_usuario($id);
                $obAdmin->setFk_id_setor(1);

                // VERIFICA SE HOUVE ERRO AO INSERIR ADMIN
                if (!$obAdmin->insertAdminTransaction($conn)) {
                    throw new Exception("Erro ao inserir administrativo!", 0);
                }
                break;

            case 5: 
                // PROFESSOR
                $obTeacher = new EntityTeacher;
                $obTeacher->setFk_id_usuario($id);

                // VERIFICA SE HOUVE ERRO AO INSERIR PROFESSOR
                if (!$obTeacher->insertTeacherTransaction($conn)) {
                    throw new Exception("Erro ao inserir professor!", 0);
                }
                break;
        }
    }
}