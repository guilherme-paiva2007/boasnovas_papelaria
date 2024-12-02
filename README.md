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

MANUAL - FUNCIONALIDADES PHP - JSON

--cart.php--
- REQUISIÇÃO GET
Fornecer 'id' ou estar com sessão com 'id' iniciada.
'id' refere ao id do usuário.
Verifica autorização do nível ou relação com id de usuário.

Procura por um carrinho com o id do usuário, se não encontrar, cria um.

Inicializa um itemsList e espera por ações.
Se nenhuma 'action' é fornecida, 'read' é assumido como valor.

read:
    retorna cart_json com informações dos produtos no carrinho.

add:
    espera parametros 'produto' para id do produto e 'qnt' para quantidade.
    adiciona o item na tabela, utilizando seu id e quantidade, incluindo o id do carrinho e ligação como 'carrinho'.
    retorna crud_json de create.

remove:
    espera parametro 'item' para id do item na lista.
    remove o item na tabela.
    retorna crud_json de delete.

clear:
    remove todos os itens relacionados ao id do carrinho de ligação 'carrinho'.
    retorna crud_json de delete.

edit:
    espera pelos parametros 'item' para id do item e 'qnt' para nova quantidade.
    altera a quantidade do item.
    retorna crud_json de update.

Pode retornar error_json por variados motivos.


--connection.php--
Incluir para inicializar conexão com banco de dados.
Necessita do arquivo local connection_config.php no mesmo diretório que retorna valor das configurações necessárias para incializar mysqli.


--crud_produtos.php--
- REQUISIÇÃO POST
Recebe 'action' para ação a ser tomada.
Verifica autorização do nível.

create:
    espera obrigatoriamente 'nome', 'preco' e 'estoque'. 'descricao' como opcional.
    insere um novo elemento na tabela 'produtos' com base nos valores fornecidos.
    retorn crud_json de create.

delete:
    espera 'id' para id do produto.
    apaga produto por seu id na tabela produtos.
    retorn crud_json de delete.

update:
    espera obrigatoriamente 'id' para id do produto.
    espera opcionalmente (pelo menos um) 'nome', 'preco', 'descricao' e 'estoque'.
    altera os parametros fornecidos na tabela produtos através do 'id'.
    retorna crud_json de update.

Pode retornar error_json por variados motivos.


--crud_promocoes.php--
- REQUISIÇÃO POST
Recebe 'action' para ação a ser tomada.
Verifica autorização do nível.

create:
    espera obrigatoriamente 'nome', 'status' e 'descricao'.
    insere um novo elemento na tabela 'promocoes' com base nos valores fornecidos.
    retorn crud_json de create.

delete:
    espera 'id' para id da promoção.
    apaga promoção por seu id na tabela 'promocoes'.
    retorn crud_json de delete.

update:
    espera obrigatoriamente 'id' para id da promoção.
    espera opcionalmente (pelo menos um) 'nome', 'descricao' e 'status'.
    altera os parametros fornecidos na tabela produtos através do 'id'.
    retorna crud_json de update.

Pode retornar error_json por variados motivos.


--crud_usuario.php--
- REQUISIÇÃO POST
Recebe 'action' para ação a ser tomada.
Verifica autorização do nível.

create:
    espera obrigatoriamente 'nome', 'status' e 'descricao'.
    insere um novo elemento na tabela 'promocoes' com base nos valores fornecidos.
    retorn crud_json de create.

delete:
    espera 'id' para id da promoção.
    apaga promoção por seu id na tabela 'promocoes'.
    retorn crud_json de delete.

update:
    espera obrigatoriamente 'id' para id da promoção.
    espera opcionalmente (pelo menos um) 'nome', 'descricao' e 'status'.
    altera os parametros fornecidos na tabela produtos através do 'id'.
    retorna crud_json de update.

Pode retornar error_json por variados motivos.


login.php
--logout.php--
Finaliza e remove definições da sessão.


--perfil.php--
- REQUSIÇÃO GET
Espera 'id' ou procura por 'id' na sessão.
Verifica autorização de nível.

Procura por usuário na tabela 'usuarios'.
Retorna profile_json.

Pode retornar error_json por variados motivos.


--promotion.php--
- REQUISIÇÃO GET
Espera 'id' para id da promoção.
Busca por promoção na tabela 'promocoes'.
Busca por itens relacionados ao id da promoção com ligação 'promocao' na tabela 'itens'.

Retorna promotion_json.

Pode retornar error_json por variados motivos.


--promotions.php--
- REQUISIÇÃO GET
Recebe opcionalmente 'search'.
Busca por promoções, em geral ou com base em 'search'.
Retorna promotions_json.

Pode retornar error_json por variados motivos.


register.php
--script.php--
Importar quando necessário o uso de funções programadas.


--search.php--
- REQUISIÇÃO GET
Recebe opcionalmente 'search'.
Busca por produtos, em geral ou com base em 'search'.
Retorna search_json.

Pode retornar error_json por variados motivos.


--setup.php--
Incluir no início de páginas para importar funcionalidades.


wish_list.php
nao implementado ainda