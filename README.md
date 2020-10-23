# API de teste 123Milhas

Api de teste para a empresa 123Milhas construída com framework Laravel/Lumen.

## Requisitos
1. PHP >= 7.x

## Instruções de uso

1. Clonar projeto:
> git clone https://github.com/ronierisonsena/123Milhas.git

2. Dentro da pasta do projeto rode o comando abaixo para instalar as dependências:
> composer install

3. Servir a aplicação na porta desejada. Assumindo que o projeto esta sendo rodando na porta 3000, navegue ate a pasta do projeto e rode o comando:
> php -S localhost:3000 -t public

4. Para os testes através do PHPUnit, rodar o comando abaixo de dentro da pasta do projeto
> ./vendor/bin/phpunit

## Urls da API

Retorna a lista de todos os vôos. Parametro opcional: /outbound ou /inbound (vôos ida/volta respectivamente)
> /api/v1/flights

Retorna a lista de vôos agrupados e ordenados pelo menos preço
> /api/v1/flights/groups

Retorna todos os dados: Vôos, grupos, total de grupos, total de vôos, preço do grupo mais barato, id do grupo mais barato
> /api/v1/flights/all
