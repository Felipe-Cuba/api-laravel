# 🚀 Laravel API - Guia de Inicialização

Este projeto é uma API construída com Laravel. Aqui irei descrever o passo a passo para rodar a aplicação localmente e conectar ao banco de dados da sua preferência.

## Documentação

Para acessar a documentação da API, acesse a página: [Documentação API Laravel](https://documenter.getpostman.com/view/20400369/2sB2cPkRCV).

---

## Requisitos

Antes de tudo, garanta que você tem essas ferramentas instaladas:

-   [PHP 8.1+](https://www.php.net/)
-   [Composer](https://getcomposer.org/)
-   [Laravel CLI](https://laravel.com/docs/10.x/installation)
-   Banco de Dados:
    -   SQLite (nativo)
    -   MySQL / MariaDB
    -   PostgreSQL
    -   SQL Server (opcional, mas funciona)
-   Extensões PHP obrigatórias (verifique no seu `php.ini`):

```ini
extension=curl
extension=fileinfo
extension=mbstring
extension=openssl
extension=pdo_mysql ; Para MySQL / MariaDB
;extension=pdo_pgsql ; Descomente se for usar PostgreSQL
;extension=pgsql      ; Descomente se for usar PostgreSQL
;extension=sqlsrv     ; Descomente se for usar PostgreSQL e requer instalação adicional se for usar SQL Server
```

-   Apenas lembrando que para descomentar as extensões, basta remover o `;` antes do nome da extensão.

Para encontrar o arquivo `php.ini` no seu sistema, basta rodar o código abaixo:

```php
<?php

phpinfo();

?>
```

## Clonando o repositório e instalando as dependências

Para clonar o repositório, basta rodar o comando abaixo:

```bash
git clone https://github.com/Felipe-Cuba/api-laravel.git
```

Após clonar o repositório, entre no diretório do projeto e rode o comando abaixo:

```bash
composer install
```

## Configurando o banco de dados

Para configurar o banco de dados, basta rodar a sequência de comandos abaixo:

-   Primeiro, crie o arquivo `.env` na raiz do projeto:

    -   Para Linux / MacOS:

        ```bash
        cp .env.example .env
        ```

    -   Para Windows (cmd):

        ```bash
        copy .env.example .env
        ```

    -   Para Windows (PowerShell):
        ```bash
        Copy-Item .env.example .env
        ```

Agora, abra o arquivo `.env` e altere as configurações conforme necessário:

> Para utilizar o SQLite:

```dotenv
DB_CONNECTION=sqlite
DB_DATABASE=${CAMINHO_ABSOLUTO}/database/database.sqlite
```

> Para utilizar o MySQL / MariaDB:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

> Para utilizar o PostgreSQL:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

> Para utilizar o SQL Server:

```dotenv
DB_CONNECTION=sqlsrv
DB_HOST=127.0.0.1
DB_PORT=1433
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

> Lembre-se de alterar o `CAMINHO_ABSOLUTO` para o caminho absoluto do seu projeto. (exemplos: `/home/felipe/api-laravel` ou `C:/Users/felipe/Documents/GitHub/api-laravel`)

> Lembre-se também de que caso seu banco de dados possua uma senha, você deverá adicionar essa informação no arquivo `.env`.

## Iniciando o servidor

Finalmente, para iniciar o servidor e testar se tudo está funcionando, basta rodar os comandos abaixo:

-   Gerar os arquivos de autoload:

    ```bash
    composer dump-autoload
    ```

-   Rodar as migrações:

    ```bash
    php artisan migrate
    ```

-   Iniciar o servidor:

    ```bash
    php artisan serve
    ```

Pronto, desta forma você já pode acessar a API em `http://localhost:8000/api`. E testar as rotas manualmente.

## Criando um novo módulo

Para criar um novo módulo, basta rodar o comando abaixo:

```bash
php artisan module:make <nome_do_modulo>
```

E então, criar os arquivos (Controllers, Models, Routes, etc.) conforme necessário.

Aqui está um passo a passo para criar um novo módulo: [Creating a Module (nwidart)](https://nwidart.com/laravel-modules/v6/basic-usage/creating-a-module)

Aqui está uma lista de comandos úteis durante o processo de criação de um novo módulo: [Artisan Commands (nwidart)](https://nwidart.com/laravel-modules/v6/advanced-tools/artisan-commands)

## Rodando os testes unitários

Caso você queira rodar os testes unitários, será necessário mais algumas configurações:

-   Primeiro no arquivo `phpunit.xml`, altere algumas linhas:

### Localização dos arquivos de teste

```xml
<testsuites>
    <testsuite name="Unit">
        <directory suffix="Test.php">Modules\Product\tests\Unit</directory>
    </testsuite>
</testsuites>
```

> O Módulo padrão nesse projeto é `Product`.

> Para cada módulo, você deverá adicionar uma linha com o nome do módulo.

-   Exemplo com outros módulos:

```xml
<testsuites>
    <testsuite name="Unit">
        <directory suffix="Test.php">Modules\Product\tests\Unit</directory>
        <directory suffix="Test.php">Modules\Category\tests\Unit</directory>
        <directory suffix="Test.php">Modules\User\tests\Unit</directory>
    </testsuite>
</testsuites>
```

### Banco de dados de teste

-   Para SQLite:

```xml
        <env name="DB_CONNECTION" value="mysql" />
        <!-- <env name="DB_HOST" value="127.0.0.1" />
        <env name="DB_PORT" value="3306" /> -->
        <env name="DB_DATABASE" value=":memory:" />
        <!-- <env name="DB_USERNAME" value="root" />
        <env name="DB_PASSWORD" value="" /> -->
```

-   Para MySQL / MariaDB:

```xml
        <env name="DB_CONNECTION" value="mysql" />
        <env name="DB_HOST" value="127.0.0.1" />
        <env name="DB_PORT" value="3306" />
        <env name="DB_DATABASE" value="api-laravel-test" />
        <env name="DB_USERNAME" value="root" />
        <env name="DB_PASSWORD" value="" />
```

-   Para PostgreSQL:

```xml
        <env name="DB_CONNECTION" value="pgsql" />
        <env name="DB_HOST" value="127.0.0.1" />
        <env name="DB_PORT" value="5432" />
        <env name="DB_DATABASE" value="api-laravel-test" />
        <env name="DB_USERNAME" value="root" />
        <env name="DB_PASSWORD" value="" />
```

-   Para SQL Server:

```xml
        <env name="DB_CONNECTION" value="sqlsrv" />
        <env name="DB_HOST" value="127.0.0.1" />
        <env name="DB_PORT" value="1433" />
        <env name="DB_DATABASE" value="api-laravel-test" />
        <env name="DB_USERNAME" value="root" />
        <env name="DB_PASSWORD" value="" />
```

-   Depois, rode os comandos abaixo:

    ```bash
    php artisan test
    ```
