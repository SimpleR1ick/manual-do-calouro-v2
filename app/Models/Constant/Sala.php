<?php

namespace App\Models\Constant;
use App\Utils\Database;

class Sala {

    /**
     * ID da sala
     * @var int
     */
    private $id_sala;

    /**
     * Descrição da sala
     * @var string
     */
    private $num_sala;

    /**
     * Método responsavel por consultar as salas 
     * @return array
     */
    public static function getRooms(): array {
        return ((new Database))->find('sala');
    }
}