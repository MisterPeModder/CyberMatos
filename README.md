# CyberMatos

CyberMatos is a toy e-commerce site built with Symfony and deployed with Ansible.

## Deployment

- Ansible
- Debian 11  
`ansible-galaxy collection install ansible.posix`
`ansible-galaxy collection install community.general`

## Entity Relationship Diagrams
# Setup

1. Import the cybermatos.sql file into a mariaDB database.
2. Create a file `.env.local` with environment variable : `DATABASE_URL="mysql://user:password@127.0.0.1:3307/cybermatos?serverVersion=10.6.5-MariaDB"`
3. Install dependencies with `composer install`
4. Start the server with `symfony server:start`
5. Go to homepage `https://localhost:+port` with the link specified in the logs.

# Api routes

List of API routes with JSON content type

Retrieve a product or the list of products

✔️ **/api/products**  
✔️ **/api/products/{productId}**

```
{
" id ": 1 ,
" name ": " Item 3000" ,
" description ": " Best item in the shop !" ,
" photo ": " https :// path / to / image . png " ,
" price ": 13.37
}
```

### Entity Relationship Diagrams

```mermaid
erDiagram
    USER ||--|{ ORDER : OneToMany
    USER {
      id int
      first_name varchar
      last_name varchar
      email varchar
      password varchar
      created_at timestamp
    }
    ORDER }|--|{ PRODUCT : ManyToMany
    ORDER {
      id int
      created_at timestamp
    }
    PRODUCT {
      id int
      name varchar
      description varchar
      image varchar
      created_at timestamp
    }
```
