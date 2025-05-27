# Projeto de Simulação de Ofertas de Crédito

Este projeto é uma API para simulação de ofertas de crédito. Utiliza Docker para facilitar o ambiente de desenvolvimento, incluindo execução de migrations, testes e simulações via endpoint.
Para o cálculo foi utilizado o método de amortização baseado na Tabela Price.

## 🚀 Começando

Siga os passos abaixo para configurar o ambiente e iniciar o projeto.

### 1. Configuração do Ambiente

Copie o arquivo `.env.example` e renomeie para `.env`:

```bash
cp .env.example .env
```

Em seguida, edite o arquivo `.env` e configure os seguintes parâmetros:

```env
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=creditoffer_db
DB_SCHEMA=public
DB_USERNAME=creditoffer
DB_PASSWORD='main@7777Aabbccdd'

GOSAT_API_BASE_URL=https://dev.gosat.org
```

Certifique-se de que essas configurações estejam corretas antes de subir os containers.

### 2. Subindo os Containers com Docker

Execute o comando abaixo para construir e iniciar os serviços:

```bash
docker compose up --build
```

### 3. Executando as Migrations

Após os containers estarem ativos, rode o comando dentro do container `api-service` para aplicar as migrations:

```bash
docker compose exec api-service php artisan migrate
```

### 4. Executando os Testes

Você pode rodar os testes da aplicação com o comando:

```bash
docker compose exec api-service php artisan test
```

### 5. Simulando Ofertas de Crédito

A API disponibiliza um endpoint para simular ofertas de crédito:

**Endpoint:**

```
POST /api/simulate-credit
```

**Parâmetros esperados (JSON):**

```json
{
  "cpf": "11111111111",
  "simulateValue": 7000
}
```

Utilize um dos CPFs listados previamente no sistema para simulações válidas.

---

## 🛠 Tecnologias Utilizadas

- PHP / Laravel
- Docker / Docker Compose
- PostgreSQL
- PHPUnit (para testes)
