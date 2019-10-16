# Cidadao De Olho

Essa aplicação foi criada no intuito de disponibilizar uma ferramenta para os cidadãos de Minas Gerais se informarem 
sobre os gastos com verbas indenizatórias dos deputados do seu estado. Além disso, a aplicação disponibiliza um estudo
sobre qual a rede social de maior impacto para divulgação dos dados.

## Requisitos
PHP >= 7.2
Composer = 1.9.0
Servidor MySql com os seguintes dados 
    - IP: 127.0.0.1
    - Porta:  3306
    - Usuario: root
    - Senha: 
    - DBName: cidadaoDeOlho

Obs.: Caso deseje alterar os dados do banco a configuração deve ser modificada no arquivo .env na raiz do diretorio

## Instalação

git clone https://github.com/pepedoni/CidadaoDeOlho.git
cd CidadaoDeOlho
composer install
php artisan migrate
php artisan serve

## Ferramentas Utilizadas

    - [laravel.com]Laravel 
    - Vue
    - Vuetify
    - MySql