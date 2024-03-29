<?php

namespace App\Http;

class Response {

    /**
     * Código status HTTP
     * @var int
     */
    private $httpCode = 200;

    /**
     * Cabeçalho do Response
     * @var array
     */
    private $headers = [];

    /**
     * Tipo de conteúdo que está sendo retornado
     * @var 
     */
    private $contentType = 'text/html';

    /**
     * Conteúdo do Response
     * @var mixed
     */
    private $content;

    /**
     * Método construtor da classe
     * @param int $httpCode
     * @param mixed   $content
     * @param string  $contentType 
     */
    public function __construct(int $httpCode, mixed $content, string $contentType = 'text/html') {
        $this->httpCode = $httpCode;
        $this->content  = $content;
        $this->setContentType($contentType);
    }

    /**
     * Método responsável por alterar o content type do Response
     * @return void
     */
    private function setContentType(string $contentType): void {
        $this->contentType = $contentType;
        $this->addHeader('Content-Type', $contentType);
    }

    /**
     * Método responsável por adicionar um registro no cabeçalho do Response
     * @return void
     */
    private function addHeader(string $key, mixed $value): void {
        $this->headers[$key] = $value;
    }

    /**
     * Método responsável por enviar os headers para o navegador
     * @return void
     */
    private function sendHeaders(): void {
        // STATUS 
        http_response_code($this->httpCode);

        // ENVIAR HEADERS
        foreach($this->headers as $key=>$value) {
            header($key.': '.$value);
        }
    }

    /**
     * Método responsável por enviar a resposta ao usuário
     * @return void
     */
    public function sendResponse(): void {
        // ENVIANDO OS HEADERS  
        $this->sendHeaders();

        // IMPRIME O CONTEUDO
        switch ($this->contentType) {
            case 'text/html':
                echo $this->content;
                exit;
            case 'application/json':
                echo json_encode($this->content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
        }   
    }
}