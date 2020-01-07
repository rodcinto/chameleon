# Chameleon Fake API Server
Welcome to the Chameleon Fake API Server.

This server will receive a request, and will shape its response acordingly to any desired response.

___
## Configuring the Database
In the .env file, there are two options for the DATABASE_URL:
1) For the provisioned docker container, in case you opted for MySQL database:
   ```DATABASE_URL=mysql://root:root@172.19.0.15:3306/chameleon```
2) For file system option. Make sure the folder has enough permissions.
    ```DATABASE_URL=sqlite:///%kernel.project_dir%/var/chameleon.db```

----
## Installing the Application
In the root folder, run the following commands:
```
composer install
bin/console doctrine:database:create
bin/console doctrine:schema:create
```
___
## Running the server
Via Docker stack or Symfony local server:
#### Symfony
At the root folder:
```
symfony server:start -d
```
#### Docker
At the root/dokcer folder:
```
docker-compose up -d
```
___
## Contribution
Extend. Modify. Contribute.
