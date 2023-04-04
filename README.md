# CyberMatos

CyberMatos is a toy e-commerce site built with Symfony and deployed with Ansible.

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
