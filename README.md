# CyberMatos

CyberMatos is a toy e-commerce site built with Symfony and deployed with Ansible.

# Setup

1. Import the cybermatos.sql file into a mySQL database.
2. Check that the DATABASE_URL environment variable in the .env is: "mysql://user:mdp@127.0.0.1:3306/cybermatos?serverVersion=8.0.2"
3. Install dependencies with `composer install`
4. Start the server with `symfony server:start`
5. Go to homepage -> https://localhost:34795

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
