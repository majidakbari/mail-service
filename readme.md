# Email Service
## Summary
This application is just a standalone micro service which provides REST HTTP endpoints for sending emails.

## Features Overview
* Fully isolated and dockerized application
* Fail over plan for sending emails
* Managing queues using `Horizon` and `supervisor`
* Infrastructure level logs(Web server and Supervisor logs)
* Application level logs (For every single Email)
* Increased code coverage by writing different unit and feature tests
* capable of sending `Markdown`, `HTML` and `Text` emails with files attached to them.
* Powerful error handling
* Descriptive API documentation powered by Swagger
* Command line interface for sending emails

## Installation guide
Follow these steps to simply run the project.

### Clone the project
Clone this repository to your local machine using the following command:
```bash
git clone git@github.com:majidakbari/mail-service.git
```

### Environment variables
There is a `.env.example` file in the root of the project which contains OS level environment variables that are used for deploying the whole project and also for running the application.
Every single variable inside of this file, has a default value, so you do not need to change them; But you can also override your own variables. First copy the example file to the `.env` file:
```bash
cd /path-to-project
cp .env.example .env
```
Then open your favorite text editor like `vim` or `nano` and change the variables. All of the variables have comments which describe them.

For example `BACKEND_ADDRESS` environment variable shows that the project will run on the following port. You can change them to your desired values. You can find different mail providers here, please fill your credentials for each service you want to use.

* Notice: In this application laravel `.env` file is totally removed and `OS` level environment variables are used instead. So if you want to change any variable "after you ran the containers", do not forget to restart the application so that your changes will affect. For restarting the containers use the following command:
```bash
docker-compose up -d --force-recreate
```

### Running the containers
Open the `Terminal` and type the following command:
```bash
docker-compose up -d 
```

### Bootup the application

Only the first time that you want to run the application, you need to execute the following command.
It will install the dependencies, migrates the database and changes required directory permissions.

```bash
docker-compose exec --user www-data app bootup
```
## Features descriptions 

### Redundancy in sending emails
Under the `config/mail.php` directory of the application, under `providers` key, you can add as many Email(SMTP Relay) providers as you want. They are used for sending emails. The first email provider is the default provider, if it wasn't able to send the mail, the second provider would take care of that email and so on. If none of the providers could send the email then just by adding a log record which indicates the situation, the email will be cleared from the queue. 

### Logs
In this application there are two levels of logs, you can figure out more in the following sections:

#### Infrastructure level logs
In the root of the project, there is `.data` directory which is used to store logs (and also used for some other purposes). You can use your preferred logging tool like `ELK` and etc to manage them.
Under the `.data/app/log` directory there are two different directories. The first one is `supervisor` which shows supervisor and horizon logs and the second one is `webserver` which holds apache server `access` and `error` logs.

#### Application level logs
In the database which will be automatically created, there is a table which is named `logs`. It shows all `successful` and `failed` emails And also you can find out the failure reason. 

### Horizon and managing queues
This application uses laravel built in feature for queueing emails. `Redis` in-memory database is used as queue driver. `Supervisor` process manager and `Horizon` are responsible for consuming these queue records. You can simply monitor your queues on the following address. 
`localhost:{{backend_address}}/horizon` (default equals to http://localhost:9090/horizon)
### API Documentation
Models and endpoints are fully specified using swagger openApi.
Simply navigate to `localhost:{{SWAGGER_ADDRESS}}` (default equals to http://localhost:9093) on your host to see API documentation.

### Command line interface
You can simply send emails through the command line, just type the command below into the Terminal
```bash
docker-compose exec app php artisan email:send
```
You will be prompted for entering the inputs.

### Database management
If you want to browse the database manually, you can use `phpmyadmin` container which is installed on this project, because it is common and easy to use.
Navigate to the following address to see DB structure.
`localhost:{{PMA_ADDRESS}}` (default equals to http://localhost:9092)

### Tests
There are different types of testing methods which you can find under `app/tests` directory. Tests are divided to the following groups:
* FeatureApplicationLevelTests
* FeatureIndexLogsTests
* FeatureSendMultipleEmailsTests
* FeatureSendSingleEmailTests
* FeatureConsoleCommandTests
* MarkdownToHtmlServiceUnitTest

To run tests, in the terminal type the following command:
```bash
docker-compose exec app vendor/bin/phpunit
```
* Notice: There are some tests that rely on database. It is possible to use `in-memory database` to make sure that the database is empty and clean but it costs a lot. So please make sure that the database (`logs` table) is empty before you run tests.

## Technical discussions (Images/Containers)
This project includes five docker containers based on `php-apache`, `MySQL`, `Redis`, `PHPMyAdmin` and `Swagger` images.
It is under development, So the source code is mounted from the host to the containers. On the production environment you should remove these volumes.

`app`
php:7.3.11-apache

`db`
MySQL 5.7.27

`redis`
redis:alpine

`phpmyadmin`
phpmyadmin/phpmyadmin

`swagger`
swaggerapi/swagger-ui

## Author
Majid Akbari [Linkedin](https://linkedin.com/in/majid-akbari)

## Licence
[MIT](https://choosealicense.com/licenses/mit/)
