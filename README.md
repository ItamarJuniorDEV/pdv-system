# ChefePDV

Sistema de ponto de venda desenvolvido em PHP puro, sem frameworks externos.

A ideia veio de um freela de manutenção em sistema legado: o cliente tinha um PDV em PHP puro com jQuery e AJAX, sem nenhum framework. Para trabalhar nele com segurança, precisei entender esse modelo de ponta a ponta — como a camada de dados se conecta ao controller sem ORM, como o AJAX se comunica sem biblioteca de roteamento, onde ficam as vulnerabilidades. Em vez de só ler o código alheio, recriei o sistema do zero com as mesmas restrições tecnológicas, aplicando as melhorias de segurança e arquitetura que o sistema original não tinha. O projeto cobre o fluxo completo de uma venda: abertura do caixa, lançamento dos itens, finalização com decremento atômico de estoque e cancelamento com estorno automático.

---

## Funcionalidades

- Frente de caixa com busca de produtos por nome ou código
- Finalização de venda com múltiplas formas de pagamento e desconto
- Cancelamento de venda com estorno de estoque em transação atômica
- Controle de abertura e fechamento de caixa com saldo esperado vs. real
- CRUD de produtos com controle de estoque mínimo e categoria
- CRUD de clientes com vinculação à venda
- CRUD de categorias
- Dashboard com resumo de vendas e produtos mais vendidos
- Relatórios de vendas e estoque com filtro por período
- Autenticação com CSRF, rate limiting (5 tentativas / 15 min por IP) e sessão segura

---

## Arquitetura

Estrutura em camadas sem framework: cada requisição de página passa pelo `index.php` e cada chamada AJAX vai para um endpoint em `api/`, que valida a sessão via `Guard` antes de chamar o controller.

```
pdv/
├── api/          Endpoints JSON (um arquivo por recurso)
├── config/       Database, Auth, Guard, Response, LoginRateLimiter
├── controller/   Validação de entrada e orquestração
├── dao/          Queries com PDO e prepared statements
├── model/        Entidades com fromArray()
├── view/         Templates PHP por módulo
└── assets/js/    Um arquivo JS por módulo (jQuery + AJAX)
```

### Fluxo de uma venda

```
Frente de caixa → api/pos.php → SaleController → SaleDAO::save()
                                                     ├── BEGIN TRANSACTION
                                                     ├── SELECT ... FOR UPDATE (estoque)
                                                     ├── INSERT INTO vendas
                                                     ├── INSERT INTO venda_itens
                                                     ├── UPDATE estoque
                                                     └── COMMIT / ROLLBACK
```

---

## Stack

- PHP 7.4+ com PDO (prepared statements reais, `EMULATE_PREPARES = false`)
- MySQL 8.0
- Bootstrap 5 + jQuery
- PHPUnit 9
- Docker + Apache
