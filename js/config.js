const HTML = searchElement('html', 'query');
const project_dir = `${location.protocol}//${location.host}/boasnovas_papelaria/`;
// ALTERAR DE ACORDO COM O DIRETÓRIO ONDE O PROJETO ESTÁ

function lightTheme() {
    ChromaticManager.apply('light', 'bn_orange');
}

function darkTheme() {
    ChromaticManager.apply('dark', 'bn_blue');
}

async function search(searching) {
    return await fetch(project_dir + 'php/search.php?search=' + encodeURIComponent(searching))
    .then(resp => resp.text());
}

// urgentemente implementar as funções de interação com php

class getStr {
    constructor(object) {
        this.assign(object);
    }

    assign(object = {}) {
        Object.entries(object).forEach(double => {
            this.#values[double[0]] = `${double[1]}`;
        });
    }

    #values = {};

    toString() {
        return Object.entries(this.#values).map(double => `${encodeURIComponent(double[0])}=${encodeURIComponent(double[1])}`).join('&');
    }
}

async function perfil(id) {
    let get = new getStr();
    if (typeof id !== 'undefined') get.assign({id: id});

    return await fetch(`${project_dir}php/perfil.php?${get}`)
        .then(resp => resp.json());
}

async function login(email, senha) {
    let post = new FormData();
    if (typeof email !== 'undefined') post.append('email', email);
    if (typeof senha !== 'undefined') post.append('senha', senha);

    return await fetch(`${project_dir}php/login.php`, {
        method: 'POST',
        body: post
    }).then(resp => resp.json());
}

async function register(email, senha, nome) {
    let post = new FormData();
    if (typeof email !== 'undefined') post.append('email', email);
    if (typeof senha !== 'undefined') post.append('senha', senha);
    if (typeof nome !== 'undefined') post.append('nome', nome);

    return await fetch(`${project_dir}php/register.php`, {
        method: 'POST',
        body: post
    }).then(resp => resp.json());
}

async function cart(id, action, params = {}) {
    let get = new getStr();
    if (typeof id !== 'undefined') get.assign({id: id});
    if (typeof action !== 'undefined') get.assign({action: action});
    if (typeof params !== 'undefined') get.assign(params);

    return await fetch(`${project_dir}php/cart.php?${get}`)
        .then(resp => resp.json());
}

async function crud_produtos(action, params = {}) {
    let post = new FormData();
    if (typeof action !== 'undefined') post.append('action', action);
    if (typeof params !== 'undefined') {
        Object.entries(params).forEach(([key, value]) => {
            post.append(key, value);
        });
    }

    return await fetch(`${project_dir}php/crud_produtos.php`, {
        method: 'POST',
        body: post
    }).then(resp => resp.json());
}

async function crud_promocoes(action, params = {}) {
    let post = new FormData();
    if (typeof action !== 'undefined') post.append('action', action);
    if (typeof params !== 'undefined') {
        Object.entries(params).forEach(([key, value]) => {
            post.append(key, value);
        });
    }

    return await fetch(`${project_dir}php/crud_promocoes.php`, {
        method: 'POST',
        body: post
    }).then(resp => resp.json());
}

async function crud_usuarios(action, params = {}) {
    let post = new FormData();
    if (typeof action !== 'undefined') post.append('action', action);
    if (typeof params !== 'undefined') {
        Object.entries(params).forEach(([key, value]) => {
            post.append(key, value);
        });
    }

    return await fetch(`${project_dir}php/crud_usuarios.php`, {
        method: 'POST',
        body: post
    }).then(resp => resp.json());
}

async function logout(redirect = true) {
    let resp = await fetch(`${project_dir}php/logout.php`).then(resp => resp.json());
    if (redirect) window.location.href = project_dir;
    return resp;
}