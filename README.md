CyberMatos
[![codecov](https://codecov.io/gh/MisterPeModder/CyberMatos/branch/master/graph/badge.svg?token=N34LB696AS)](https://codecov.io/gh/MisterPeModder/CyberMatos)
===

CyberMatos is a toy e-commerce site built with Symfony and deployed with Ansible.

## Installation

### Locally

1. Import the cybermatos.sql file into a mariaDB database.
2. Create a file `.env.local` with environment variable : `DATABASE_URL="mysql://user:password@127.0.0.1:3307/cybermatos?serverVersion=10.6.5-MariaDB"`
3. Install dependencies with `composer install`
4. Start the server: `cd app`, `symfony server:start`
5. Go to homepage `https://localhost:+port` with the link specified in the logs.

### Remote Using Ansible

#### Target Machine Requirements

- Debian 11
- SSH access with sudo privileges (the user must **_not_** be root)

#### Installer Machine Requirements

- Ansible ([installation](https://docs.ansible.com/ansible/latest/installation_guide/intro_installation.html))
- The following Ansible third-party collections:
  - `ansible.posix`
  - `community.general`
  - `community.mysql`
  - Install them with the following command:

```sh
ansible-galaxy collection install ansible.posix community.general community.mysql
```

#### Steps

1. Copy the `app/.env` file to `app/.env.local` and fill the variables with your own values.
2. Copy the `ansible/group_vars/all.example.yml` file to `ansible/group_vars/all.yml` and fill the variables with your own values.
   - **optional**: You can encrypt this file using the command `ansible-vault encrypt ansible/group_vars/all.yml`
3. When using Vault, place your password in a file named `.passwd` in the `ansible` directory.
4. Create an inventory file specifying the target machine(s) IP address(es) and the SSH user(s).

```ini
# file: hosts
[all]
example-host ansible_host=0.0.0.0 ansible_port=22 ansible_user=user
```

5. **From the ansible directory**, launch the playbook with the following command:

```sh
ansible-playbook -i hosts playbook.yml
```

(where `hosts` is the inventory file path) 6. Take a coffee break ☕, this may take a while. 7. Enjoy!

## Running Tests

```sh
cd app
symfony console doctrine:database:drop --force --env=test
symfony console doctrine:database:create --env=test
symfony console doctrine:migrations:migrate -n --env=test
./bin/phpunit
```

## Documentation

-> Postman collection : [e-commerce.postman](/docs/e-commerce.postman_collection.json)  
-> Documentation API : [documentation](/docs/Documentation.md)
