# Chameleon Fake API Server
Welcome to the Chameleon Fake API Server.

Inspired by the project "mock-server.com",  this server will receive a request, and will shape its response acordingly to any desired response, in a very simple way.

![Screenshot](https://github.com/rodcinto/chameleon/blob/master/screenshot.png)

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
To compile the assets, specify one between dev and production:
``` yarn encore [dev/production]```
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
