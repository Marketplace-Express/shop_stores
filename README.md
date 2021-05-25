Shop: Stores Service
--
### Introduction:
Welcome to development team of Marketplace project, This project involves the following technologies:
1. PHP 7.3 - using symfony framework
2. MySQL 8
3. Redis
4. MongoDB
5. RabbitMQ
6. Docker

---

### Description:
This service handles stores functionality, including CRUDs, processing and handling all logic related to stores.

---

### Installation:

1. Clone the repository:
```shell script
git clone git@github.com:Marketplace-Express/shop_stores.git
```

2- Copy ".env.example" file to “.env” under project root directory, then change the parameters to match your preferences, example:
```
###> doctrine/doctrine-bundle ###
DATABASE_URL=mysql://shop_stores:secret@172.18.0.1:3306/shop_stores?serverVersion=8.0
###< doctrine/doctrine-bundle ###

###> jurry/amqp-symfony-bundle ###
JURRY_RABBIT_MQ_DSN=tcp://guest:guest@172.18.0.1:5672
###< jurry/amqp-symfony-bundle ###
```
And so on for Redis and RabbitMQ ...
>Note: You can use network (marketplace-network) gateway ip instead of providing each container ip

3- Run `docker-compose build` to build the docker image for this repository.
       
4- Run `docker-compose up -d`, This command will create new containers:

1. shop_stores_stores-sync_1:
- This will declare a new queue “stores_sync” in RabbitMQ queues list
2. shop_stores_stores-async_1:
- This will declare a new queue “stores_async” in RabbitMQ queues list
3. shop_stores_stores-api_1:
- This will start a new application server listening on a specific port specified in `docker-compose.yml` file, you can access it by going to this URL: [http://localhost:port](http://localhost:1000)
- As a default, the port value is 1000.
- You can use Postman with the collections provided to test micro service APIs.
4. shop_stores_stores-unit-test_1:
- This will run the unit test for this micro-service

If you want to scale up the workers (sync / async), you can simply run this command:
```bash
docker-compose up --scale stores-{sync/async}=num -d
```

Where “num” is the number of processes to run, {sync/async} is the service which you want to scale up, example:
```bash
docker-compose up --scale stores-async=3 -d
```

---
### Unit test
To run the unit test, just run this command:
```bash
docker-compose up stores-unit-test
```
