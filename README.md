# 📊 Log Processor - Laravel + Docker

Este projeto Laravel processa arquivos de log (`logs.txt`) contendo registros de requisições de um API Gateway. Ele salva os dados em banco de dados e gera relatórios em formato CSV.

## 🚀 Tecnologias Utilizadas

-   PHP 8.2 / Laravel 12.0
-   PostgreSQL
-   Docker e Docker Compose

## 🐳 Como Rodar o Projeto

> Certifique-se de ter o **Docker** e o **Docker Compose** instalados em sua máquina.

1. Clone o repositório:

    ```bash
    git clone https://github.com/devMaikel/logs-processor.git
    cd logs-processor
    ```

2. Suba os containers:

    ```bash
    docker-compose up -d --build
    ```

3. Adicione o arquivo `logs.txt` na **raiz do projeto** (mesmo nível do `artisan`).

---

## ⚙️ Comandos Disponíveis

### 1. 📥 Processar logs (forma **performática** com `QueryBuilder` e batch de 1000 registros)

```bash
php artisan process:logs
```

-   ⚡️ ~10x mais rápido do que usar Eloquent.
-   Ideal para grandes volumes de dados.

### 2. 🐢 Processar logs (forma tradicional usando `Eloquent`)

```bash
php artisan process:logsEloquent
```

-   Insere cada registro individualmente.
-   Mais simples, porém mais lento.

### 3. 📊 Gerar relatórios (CSV)

```bash
php artisan generate:reports
```

-   Gera 3 arquivos no diretório `storage/app/reports/{timestamp}/`:
    -   `requests_by_consumer.csv`: total de requisições por consumidor.
    -   `requests_by_service.csv`: total de requisições por serviço.
    -   `average_latencies_by_service.csv`: média das latências `request`, `proxy` e `gateway` por serviço.

---

## 🧪 Testes

Execute os testes com:

```bash
php artisan test
```

Cobertura para:

-   Processamento dos logs.
-   Geração dos relatórios.
-   Comandos artisan.

---

## 📌 Observações

-   O diretório `logs.txt` deve estar na raiz do projeto.
-   O ambiente de execução e banco de dados é todo gerenciado via Docker.

---

## 📬 Considerações Finais

Este projeto foi desenvolvido com foco em:

-   Eficiência no processamento de grandes volumes de dados.
-   Geração de relatórios úteis para análise de desempenho.
-   Boas práticas de organização e modularização em Laravel.
-   Testes automatizados para garantir confiabilidade.
