function avvisaCliente(clienteId) {
    const formData = new FormData();
    formData.append('cliente_id', clienteId);

    fetch('../php/avvisa_cliente.php', {
        method: 'POST', body: formData
    })

    .then(response => response.text())
    .then(data => {
        alert(data);
    })

    .catch(error => {
        console.error('Errore', error)
    });
}