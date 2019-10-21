# Cidadao De Olho

Essa aplicação foi criada no intuito de disponibilizar uma ferramenta para os cidadãos de Minas Gerais se informarem 
sobre os gastos com verbas indenizatórias dos deputados do seu estado. Além disso, a aplicação disponibiliza um estudo
sobre qual a rede social de maior impacto para divulgação dos dados.

## Organização de Código e Objetivo

O código foi feito utilizando o modelo MVC e a organizações de pastas do laravel. Nesse sentido, as views desenvolvidas podem ser encontradas em resources/js/components, os Models em app e os controllers em app/Http/Controllers.

## Requisitos
- PHP >= 7.2
- Composer 
- Node
- Servidor MySql com os seguintes dados 
```
IP: 127.0.0.1
Porta:  3306
Usuario: root
Senha: 
DBName: cidadaoDeOlho
```

Obs.: Caso deseje alterar os dados do banco a configuração deve ser modificada no arquivo .env na raiz do diretorio

## Instalação

- git clone https://github.com/pepedoni/CidadaoDeOlho.git
- cd CidadaoDeOlho
- composer install
- npm install
- php artisan migrate

## Rodar Aplicação
- php artisan serve

( Por padrão a aplicação é executada no endereço http://127.0.0.1:8000 )

## Rotas da api

- GET /preencherDados            => Busca os dados dos deputados e de verbas indenizatorias e preenche as tabelas do sistema
- GET /deputados_por_verba/{mes} => Retorna a lista de deputados ordenados pelo reembolso de verbas indenizatorias de maneira decrescente
- GET /redes_sociais_mais_utilizadas => Retorna a lista de redes sociais ordenadas das mais utilizadas para as menos utilziadas

## Ferramentas Utilizadas

- [Laravel](http://laravel.com) 
- [Vue](https://vuejs.org/)
- [Vuetify](https://vuetifyjs.com/pt-BR/)
- [MySql](https://www.mysql.com/)

## Imagens
![](https://user-images.githubusercontent.com/9373165/67168946-c64ee980-f37e-11e9-87db-340c8157cf1b.jpeg)

![](https://user-images.githubusercontent.com/9373165/67168976-e9799900-f37e-11e9-9b76-f61dc176b976.jpeg)

![](https://user-images.githubusercontent.com/9373165/67168999-f6968800-f37e-11e9-9c0d-aa533695a66a.jpeg)


