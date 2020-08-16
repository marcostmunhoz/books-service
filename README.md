# Books Service
## Projeto desenvolvido durante o curso básico de microservices.

### Setup

- Duplicar o arquivo .env.example para .env, e alterar as seguintes variáveis de acordo:
    - ACCEPTED_SECRETS: Uma lista de chaves secretas aceitas pelo serviço, separadas por vírgula.
- `composer install`, para instalar as dependências do projeto.
- `php artisan jwt:secret`, para gerar uma nova chave secreta JWT.
- `touch database/database.sqlite`, para criar o banco de dados de SQLite, caso queira usar esse tipo de banco.
- `php artisan migrate --seed`, para criar a tabela de usuários, criando também o usuário de testes.


