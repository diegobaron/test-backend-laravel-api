## Sobre a Api
Api criada com framework laravel para cadastro de escolas e cursos e cotação de valores dos cursos

## Autenticação
Jwt
Dados para autenticação:
email: root@ally.com
password: 123456

## Rotas gerais da api
    POST: /auth/login
    
    GET: /courses
    GET: /course/show/{id}
    POST: /course/create
    PUT: /course/update/{id}
    DELETE: /course/delete/{id}

    GET: /schools
    GET: /school/show/{id}
    POST: /school/create
    PUT: /school/update/{id}
    DELETE: /school/delete/{id}
    
    GET: /quotation
