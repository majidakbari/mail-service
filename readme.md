# Talents
This project includes four docker containers based on `php-apache`, `MySQL`, `Mailtrap` and `PHPMyAdmin` images.

It is under development, So the source code is mounted from the host to the container. On the production environment you should remove these volumes.

## Installation guide
Follow these steps to simply run the project.

### Clone the project
Clone this repository to your local machine using the following command
```bash
git clone git@bitbucket.org:neocard/talent-project.git
```

### Environment variables
Setting up the container (OS) level environment variables like $USER id `WWW_DATA_USER_ID`. So every single file which is created or modified by the container users will be owned by $USER because of user id mapping between the host and the containers.
```bash
cd /path-to-project
cp .env.example .env
vim .env
```

### Running the containers
Open the `Terminal` and type the following command:
```bash
docker-compose up -d 
```

### Bootup the application

Only the first time that you want to run the application, you need to execute the following command.
It will install the dependencies, creates .env laravel file, generates the application key and changes required directory permissions.

```bash
docker-compose exec --user www-data backend bootup
```

### Creating Admin User
Use the following command to create an Admin user
```bash
docker-compose exec backend make:admin
```
You will be prompted for entering admin data

## API Documentation
In the root of the project there is a postman collection which contains the API doc.
Also you can find the API documentation on the following address.

[API Documentation]()

## Tests
To run tests, in the terminal type the following command:
```bash
docker-compose exec backend vendor/bin/phpunit
```

## Images/Containers

`backend`
php:7.3.5-apache

`db`
MySQL 5.7.27

`mailtrap`
eaudeweb/mailtrap

`phpmyadmin`
phpmyadmin/phpmyadmin


## Licence

Copyright © 2019, Devolon
https://devolon.fi/ 
All rights reserved.

