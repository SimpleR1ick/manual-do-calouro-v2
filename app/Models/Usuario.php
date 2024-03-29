<?php

namespace App\Models;

use App\Utils\Database;

class Usuario {

    /**
     * ID do usuário
     * @var int
     */
    private $id_usuario;

    /**
     * Nome do usuário
     * @var string
     */
    private $nom_usuario;

    /**
     * Email do usuário
     * @var string
     */
    private $email;

    /**
     * Senha do usuário
     * @var string
     */
    private $senha;

    /**
     * Código da foto do usuário
     * @var string
     */
    private $img_perfil;

    /**
     * Data de criação do usuário
     * @var string
     */
    private $add_data;

    /**
     * Nivel de acesso do usuário
     * @var int
     */
    private $fk_acesso_id_acesso = 2;

    /**
     * Identificador de status do usuário
     * @var string
     */
    private $fk_nivel_id_nivel = 1;
    
    /**
     * Método responsável por cadastrar a instância atual no banco de dados
     * @return boolean
     */
    public function insertUser(): bool {
        // ATRIBUI AO OBJETO A HORA ATUAL
        $this->setAdd_data();

        // INSERE A INSTÂNCIA NO BANCO
        $this->setId_usuario((new Database('usuario'))->insert([
            'nom_usuario'         => $this->nom_usuario,
            'email'               => $this->email,
            'senha'               => $this->senha,
            'add_data'            => $this->add_data,
            'img_perfil'          => $this->img_perfil,
            'fk_nivel_id_nivel'   => $this->fk_nivel_id_nivel,
            'fk_acesso_id_acesso' => $this->fk_acesso_id_acesso
        ]));
        // SUCESSO
        return true;
    }

    /**
     * Método responsável por cadastrar a instancia existente no banco de dados 
     * @param \App\Utils\Database $conn
     * 
     * @return bool
     */
    public function insertUserTransaction(Database $conn): bool {
        $conn->setTable('usuario');

        // ATRIBUI AO OBJETO A HORA ATUAL
        $this->setAdd_data();

        // INSERE A INSTÂNCIA NO BANCO
        $this->setId_usuario(($conn)->insert([
            'nom_usuario'         => $this->nom_usuario,
            'email'               => $this->email,
            'senha'               => $this->senha,
            'add_data'            => $this->add_data,
            'img_perfil'          => $this->img_perfil,
            'fk_nivel_id_nivel'   => $this->fk_nivel_id_nivel,
            'fk_acesso_id_acesso' => $this->fk_acesso_id_acesso
        ]));
        // SUCESSO
        return true;
    }

    /**
     * Método responsável por atualizar os dados no banco
     * 
     * @return boolean
     */
    public function updateUser(): bool {
        return (new Database('usuario'))->update("id_usuario = {$this->id_usuario}", [
            'nom_usuario'         => $this->nom_usuario,
            'email'               => $this->email,
            'senha'               => $this->senha,
            'add_data'            => $this->add_data,
            'img_perfil'          => $this->img_perfil,
            'fk_nivel_id_nivel'   => $this->fk_nivel_id_nivel,
            'fk_acesso_id_acesso' => $this->fk_acesso_id_acesso
        ]);
    }

    /**
     * Método responsável por atualizar os dados no banco em uma instancia existente
     * @param \App\Utils\Database $conn
     * 
     * @return boolean
     */
    public function updateUserTransaction(Database $conn): bool {
        return ($conn)->update("id_usuario = {$this->id_usuario}", [
            'nom_usuario'         => $this->nom_usuario,
            'email'               => $this->email,
            'senha'               => $this->senha,
            'add_data'            => $this->add_data,
            'img_perfil'          => $this->img_perfil,
            'fk_nivel_id_nivel'   => $this->fk_nivel_id_nivel,
            'fk_acesso_id_acesso' => $this->fk_acesso_id_acesso
        ]);
    }

    /**
     * Método responsável por excluir um usuário do banco
     * @return boolean
     */
    public function deleteUser(): bool {
        return (new Database('usuario'))->delete("id_usuario = {$this->id_usuario}");
    }

    /**
     * Método responsável por excluir um usuário do banco
     * @param \App\Utils\Database $conn
     * 
     * @return boolean
     */
    public function deleteUserTransaction(Database $conn): bool {
        $conn->setTable('usuario');

        return ($conn)->delete("id_usuario = {$this->id_usuario}");
    }

    /**
     * Método responsável por retornar a turma pelo id do usuário
     * @return array|bool
     */
    public static function getUserClass(int $id): mixed {
        $table  = "turma t JOIN grupo g ON (t.id_turma = g.fk_turma_id_turma) JOIN grupo_aluno ga ON (g.id_grupo = fk_grupo_id_grupo) JOIN aluno a ON (ga.fk_aluno_fk_usuario_id_usuario = a.fk_usuario_id_usuario)";
        $where  = "a.fk_usuario_id_usuario = $id";
        $fields = "t.fk_curso_id_curso AS curso, t.num_modulo AS modulo";

        // RETORNA UM ARRAY ASSOCIATIVO
        return (new Database($table))->select($where, null, null, $fields)->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Método responsável por retornar o grupo pelo id do usuário
     * @param int $id
     * 
     * @return mixed
     */
    public static function getUserGroup(int $id): mixed {
        $table  = "grupo g JOIN turma t ON (g.fk_turma_id_turma = t.id_turma) JOIN grupo_aluno ga ON (g.id_grupo = fk_grupo_id_grupo)";
        $where  = "ga.fk_aluno_fk_usuario_id_usuario = $id";
        $fields = "id_grupo, dsc_grupo";

        // RETORNA UM ARRAY ASSOCIATIVO
        return (new Database($table))->select($where, null, null, $fields)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Método responsável por retornar o id do grupo pelo id
     * @param int $id
     * 
     * @return mixed
     */
    public static function getUserGroupId(int $id): mixed {
        $table  = "turma t JOIN grupo g ON (t.id_turma = g.fk_turma_id_turma) JOIN grupo_aluno ga ON (g.id_grupo = fk_grupo_id_grupo) JOIN aluno a ON (ga.fk_aluno_fk_usuario_id_usuario = a.fk_usuario_id_usuario)";
        $where  = "a.fk_usuario_id_usuario = $id";

        // RETORNA UM ARRAY ASSOCIATIVO
        return (new Database($table))->select($where, null, null, 'id_grupo')->fetch(\PDO::FETCH_ASSOC);
    }

    public static function getUserSector(int $id): mixed {
        $where = "fk_servidor_fk_usuario_id_usuario = $id";

        // RETORNA UM ARRAY ASSOCIATIVO
        return (new Database('administrativo'))->select($where, null, null, 'fk_setor_id_setor AS setor')->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Método responsável por retornar um contato de um usuário pelo id
     * @return array|bool
     */
    public static function getUserContact(int $id): mixed {
        $table = "usuario u JOIN servidor s ON (u.id_usuario = s.fk_usuario_id_usuario) JOIN contato c ON (s.fk_usuario_id_usuario = c.fk_servidor_fk_usuario_id_usuario) JOIN tipo_contato tc ON (c.fk_tipo_contato_id_tipo = tc.id_tipo)";
        $where = "id_usuario = $id";
        $fields = "nom_usuario, dsc_contato, dsc_tipo";

        // RETORNA UM ARRAY ASSOCIATIVO
        return (new Database($table))->select($where, null, null, $fields)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Método responsável por retornar usuário
     * @param  string $where
     * @param  string $order
     * @param  string $limit
     * @param  string $fields
     * 
     * @return mixed
     */
    public static function getUsers($where = null, $order = null, $limit = null, $fields = '*'): mixed {
        return (new Database('usuario'))->select($where, $order, $limit, $fields);
    }

    /**
     * Método responsável por retornar uma istância com base no ID
     * @param  int $id
     * 
     * @return self|bool
     */
    public static function getUserById($id): mixed {
        return self::getUsers("id_usuario = $id")->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar um usuário com base em seu email
     * @param  string $email
     * 
     * @return self|bool
     */
    public static function getUserByEmail(string $email): mixed {
        return self::getUsers("email = '$email'")->fetchObject(self::class);
    }

    /*
     * Métodos GETTERS E SETTERS
     */
    
     /**
      * Get id_usuario
      * @return int
      */
    public function getId_usuario(): int {
        return $this->id_usuario;
    }

    /**
     * Set id_usuario
     * @param int $id
     */
    private function setId_usuario(int $id): void {
        $this->id_usuario = $id;
    }
    
    /**
     * Get nom_usuario
     * @return string
     */
    public function getNom_usuario(): string { 
        return $this->nom_usuario;
    }

    /**
     * Set nom_usuario
     * @param string $name
     */
    public function setNom_usuario(string $name): void { 
        $this->nom_usuario = $name;
    }   

    /**
     * Get email
     * @return string
     */
    public function getEmail(): string { 
        return $this->email;
    }

    /**
     * Set email
     * @param string $email
     */
    public function setEmail(string $email): void { 
        $this->email = $email;
    }

    /**
     * Get senha
     * @return string
     */
    public function getSenha(): string { 
        return $this->senha;
    }

    /**
     * Set senha
     * @param string $senha
     */
    public function setSenha(string $senha): void { 
        !empty($senha) ? $this->senha = password_hash($senha, PASSWORD_DEFAULT) : $this->senha; 
    }

    /**
     * get img_perfil
     * @return string
     */
    public function getImg_perfil(): string {
        return !empty($this->img_perfil) ? $this->img_perfil : 'user.png';
    }

    /**
     * Set img_perfil
     * @param string $img
     */
    public function setImg_perfil(string $img): void { 
        $this->img_perfil = $img;
    }

    /**
     * Get ativo
     * @return int
     */
    public function getNivel(): int { 
        return $this->fk_nivel_id_nivel;
    }

    /**
     * Set ativo
     * @param int $ativo
     */
    public function setNivel(int $nivel): void {
        $this->fk_nivel_id_nivel = $nivel;
    }

    /**
     * Get add_data
     * @return string
     */
    public function getAdd_data(): string { 
        return date('d/m/Y H:i:s', strtotime($this->add_data));
    }

    /**
     * Set add_data
     * @return void
     */
    private function setAdd_data(): void { 
        //$this->add_data = $data;
        $this->add_data = date('Y-m-d H:i:s');
    }
    
    /**
     * Get fk_acesso_id_acesso
     * @return int
     */
    public function getFk_acesso(): int { 
        return $this->fk_acesso_id_acesso;
    }

    /**
     * Set fk_acesso_id_acesso
     * @param int $acesso
     */
    public function setFk_acesso(int $acesso = 2): void { 
        $this->fk_acesso_id_acesso = $acesso;
    }  
    
    /**
     * Get fk_nivel_id_nivel
     * @return int
     */
    public function getFk_nivel(): int {
        return $this->fk_nivel_id_nivel;
    }
    
    /**
     * Set fk_nivel_id_nivel
     * @param int $nivel
     */
    public function setFk_nivel(int $nivel): void {
        $this->fk_nivel_id_nivel = $nivel;
    }
}