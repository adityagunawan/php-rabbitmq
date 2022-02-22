## RabbitMQ With PHP
update composer with command :
```
composer update
```
create image from Dockerfile :
```
docker build -t rabbitmq-custom .
```
run image whith docker compose :
```
docker-compose up
```
run publisher or consumer :
```
php publisher.php
```
check queue in rabbitmq management from web browser :

> http://localhost:15672/
> guest:guest
