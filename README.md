-- Instalação --

Ao importar o projeto, certifique-se de criar os arquivos localconfig.json e connectionconfig.json no diretório php/
Sendo as informações necessárias:

localconfig.json
{
    "dirbase": string (sub-diretório onde o projeto está localizado, vazio se não existe)
}

connectionconfig.json
{
    "hostname": string,
    "username": string,
    "password": string,
    "database": string
}

Além disso, necessário que o banco de dados esteja acessível e com sua estrutura completa.