/**
 * Função para rendenizar os dados do modal contato
 * @param {int} id 
 */
async function editContact(id) {
    // REQUISIÇÃO A ROTA DE CONSULTA
    const data = await getData('/api/v1/contact/data/'+id);

    // ADICIONANDO VALORES AO MODAL DE EDIÇÃO
    document.getElementById('edit-id-contato').value = data.id_contato;
    document.getElementById('edit-fk-usuario').value = data.fk_servidor_fk_usuario_id_usuario;
    document.getElementById('edit-tipo-contato').value  = data.fk_tipo_contato_id_tipo;
    document.getElementById('edit-input-contato').value = data.dsc_contato;
}

/**
 * Função para alterar o valor do input no modal de exclusão
 * @param {int} id 
 */
async function delContact(id) {
    document.getElementById('id_delete').value = await id;
}
