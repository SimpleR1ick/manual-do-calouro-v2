// URL do projeto
const url = 'http://localhost/manual-do-calouro';

/**
 * Função para realizar request a uma rota e obter os dados
 * @param {string} route 
 * 
 * @returns 
 */
async function getData(route) {
    const response = await fetch(url+route);

    const result = await response.json();

    return await result['dados'];
}