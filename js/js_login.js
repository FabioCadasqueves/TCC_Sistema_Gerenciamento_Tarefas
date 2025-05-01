function mostrarFormulario(tipo) {
    document.getElementById('selecao-perfil').classList.add('d-none');
    document.getElementById('subtitulo-login').classList.add('d-none');
    document.getElementById('titulo-login').innerText = 'Login';
    document.getElementById('titulo-login').classList.remove('d-none');

    if (tipo === 'admin') {
        document.getElementById('form-admin').classList.remove('d-none');
    } else {
        document.getElementById('form-colaborador').classList.remove('d-none');
    }
}

function mostrarCadastroAdmin() {
    document.getElementById('form-admin').classList.add('d-none');
    document.getElementById('form-cadastro-admin').classList.remove('d-none');
    //document.getElementById('titulo-login').classList.add('d-none'); // esconde "Entrar"
    document.getElementById('titulo-login').innerText = 'Cadastro';
}

function voltarParaLoginAdmin() {
    document.getElementById('form-cadastro-admin').classList.add('d-none');
    document.getElementById('form-admin').classList.remove('d-none');
    document.getElementById('titulo-login').classList.remove('d-none'); // mostra "Entrar"
}

function voltar() {
    document.getElementById('titulo-login').innerText = 'Entrar';
    document.getElementById('titulo-login').classList.remove('d-none');
    document.getElementById('subtitulo-login').classList.remove('d-none');
    document.getElementById('selecao-perfil').classList.remove('d-none');
    document.getElementById('form-admin').classList.add('d-none');
    document.getElementById('form-colaborador').classList.add('d-none');
    document.getElementById('form-cadastro-admin').classList.add('d-none');
}
