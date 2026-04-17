# Changelog

Todas as mudanças relevantes deste projeto estão documentadas aqui.  
Formato baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/).

---

## [1.2.0] — 2026-04-17

### Adicionado
- Gestão de usuários com CRUD completo e três perfis: admin, gerente e operador
- Controle de acesso por perfil no sidebar e nas rotas — acesso direto via URL também é bloqueado
- Cobertura de testes ampliada: perfis de acesso (`role`, `isAdmin`, `can`), schema de clientes e verificação de caixa aberto

### Alterado
- README atualizado com arquitetura, funcionalidades e instruções de execução via Docker

---

## [1.1.0] — 2026-04-17

### Adicionado
- Relatório de estoque com situação por produto: OK, crítico e zerado, com destaque visual
- Relatório de mais vendidos com ranking por quantidade no período
- Resumo por forma de pagamento com percentual de receita por categoria
- Aba "Geral" com dashboard de métricas: faturamento, ticket médio, cancelamentos, breakdown por pagamento, situação de estoque e top 5 produtos
- Carregamento automático dos relatórios ao trocar de aba

---

## [1.0.0] — 2026-04-16

### Adicionado
- Integrações externas via camada de service: ViaCEP (endereço por CEP), ReceitaWS (dados por CNPJ) e BrasilAPI (feriados nacionais)
- Renomeação da marca para **ChefePDV** com favicon SVG personalizado
- Tela de login com logo SVG e imagem de fundo

### Corrigido
- Validação do valor recebido na finalização de venda em dinheiro
- Cache-busting automático nos scripts via `filemtime`
- Ícones do sidebar substituídos por alternativas válidas do Font Awesome 6
- Encoding `utf8mb4` no banco de dados e ajuste de porta no Docker Compose
- Centralização do layout e hierarquia visual da tela de login

---

## [0.4.0] — 2026-04-03

### Adicionado
- Dashboard com resumo de vendas do dia, ticket médio, total de itens e faturamento
- Relatórios de vendas e estoque com filtro por período e forma de pagamento
- Testes unitários com PHPUnit para Auth, controllers e models usando SQLite em memória

---

## [0.3.0] — 2026-04-03

### Adicionado
- Módulo de vendas com histórico filtrável e cancelamento com estorno atômico de estoque
- CRUD de clientes com validação de e-mail, CPF/CNPJ, endereço completo e telefone
- CRUD de produtos com estoque mínimo, categoria, código de barras e preço
- CRUD de categorias com vínculo aos produtos

---

## [0.2.0] — 2026-03-29

### Adicionado
- Frente de caixa com busca por nome e código de barras, carrinho e finalização com dinheiro, crédito, débito e PIX
- Decremento atômico de estoque com lock pessimista (`SELECT ... FOR UPDATE`) em transação
- Desconto por venda com cálculo automático de troco
- Controle de abertura e fechamento de caixa com comparativo entre saldo esperado e real

---

## [0.1.0] — 2026-03-25

### Adicionado
- Estrutura base do projeto: front controller, autoload por diretório, configuração via `.env` e Docker com PHP + MySQL + Apache
- Autenticação com login/logout, CSRF token, rate limiting por IP (5 tentativas / 15 min) e sessão segura com `httponly` e `samesite: Lax`
- Senhas com `password_hash(PASSWORD_BCRYPT)` e verificação via `password_verify`
- Documentação prévia ao código: levantamento de requisitos (RF/RNF), arquitetura em camadas, modelagem do banco e fluxogramas do sistema
