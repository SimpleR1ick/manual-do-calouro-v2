<?php

namespace App\Utils;

class Pagination {

	/**
	 * Número máximo de registros por página
	 * @var int
	 */
	private $limit;

	/**
	 * Quantidade total de resultados do banco
	 * @var int
	 */
	private $results;

	/**
	 * Quantidade de páginas
	 * @var int
	 */
	private $pages;

	/**
	 * Página atual
	 * @var int
	 */
	private $currentPage;

	/**
	 * Construtor da classe
	 * @param int  $results
	 * @param int  $currentPage
	 * @param int  $limit
	 */
	public function __construct(int $results, int $currentPage = 1, int $limit = 10) {
		$this->results     = $results;
		$this->limit       = $limit;
		$this->currentPage = (is_numeric($currentPage) and $currentPage > 0) ? $currentPage : 1;
		$this->calculate();
	}

	/**
	 * Método responsável por calcular a páginação
	 * @return void
	 */
	private function calculate(): void {
		//CALCULA O TOTAL DE PÁGINAS
		$this->pages = $this->results > 0 ? ceil($this->results / $this->limit) : 1;

		//VERIFICA SE A PÁGINA ATUAL NÃO EXCEDE O NÚMERO DE PÁGINAS
		$this->currentPage = $this->currentPage <= $this->pages ? $this->currentPage : $this->pages;
	}

	/**
	 * Método responsável por retornar a cláusula limit da SQL
	 * @return string
	 */
	public function getLimit(): string {
		$offset = ($this->limit * ($this->currentPage - 1));

		return $this->limit.' OFFSET '.$offset;
	}

	/**
	 * Método responsável por retornar as opções de páginas disponíveis
	 * @return array
	 */
	public function getPages(): array {
		// VERIFICA SE EXISTE SÓ 1 PAGINA
		if ($this->pages == 1) return [];

		// PÁGINAS
		$pages = [];
		for ($i = 1; $i <= $this->pages; $i++) {
			$pages[] = [
				'page'    => $i,
				'current' => $i == $this->currentPage
			];
		}
		// RETORNA AS PAGINAS
		return $pages;
	}
}
