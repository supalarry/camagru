# Camagru

School 42 project.

- Backend in vanilla PHP
- Custom built router
- Frontend in vanilla Javascript
- Runs on nginx server
- Scripts executed by php-fpm
- Data stored using mysql
- Phpmyadmin enabled
- Deployed using Docker

## Installation

First, your machine must have Docker installed.

Clone this repository

```
git clone git@github.com:lauris-printify/camagru.git
```

Switch to project folder

```
cd camagru
```

Run the app using docker

```
docker-compose up
```

## Logging into phpmyadmin

Log into phpmyadmin http://localhost:8088 using:

```
username: root
password: rootroot
```

## Database setup

This project uses mysql together with doctrine within the symfony framework.

After executing `docker-compose up` mysql is set up from `docker/backup/mysql/api.sql` file.

All work occurs in `printify_api` database.

Four tables are created:

- user : stores created users

- product : stores products submitted by the user

- order : stores orders submitted by the user

- relation : links an order with it's products

Connection to mysql docker container is defined in `api/env` file's DATABASE_URL field.


## Testing

Tests are written with phpunit and are located in `api/tests`.

> Testing is done within memory to reduce test time.

> `api/config/services_test.yml` : declares which repositories are used for testing. Production repositories are being simulated by test repositories stored in api/src/Repository/Test.

> `api/config/services.yml` : declares which repositories are used for production. Production repositories are stored in api/src/Repository/Prod.


With docker running, open a new terminal in the root folder and switch to api folder

```
cd api
```

Create an alias in your shell for more convenient use

```
alias phpunit='vendor/bin/phpunit'
```

Run tests

```
phpunit
```

## Usage

- Endpoints can be accessed via port 8098 : http://localhost:8098/{endpoint}

- All JSON keys can be a combination of upper and lowercase letters. If required key is "name", then API accepts "Name", "nAmE" etc.

```
{
	"name" : "John",
	"surname" : "Doe"
}
```
```
{
	"nAmE" : "John",
	"surname" : "Doe"
}
```

Both examples are valid.

- API ignores redundant keys. If name and surname keys are required, but user also adds birthdate key, the API only grabs keys it needs.

- If invalid endpoint or non existing user, product or order are requested, API returns an empty body with 404 status code.

 - Accessing `'/'` displays HTML welcome page.

## Endpoints

### 1.1 Create a new user
- endpoint : /users
- method : POST
- url : http://localhost:8098/users
- request body :

```
{
	"name" : "John",
	"surname" : "Doe"
}
```

> "name" : accepts upper and lowercase letters, spaces, dot (.) , comma (,) , apostrophe (') and dash (-).

> "surname" : accepts upper and lowercase letters, spaces, dot (.) , comma (,) , apostrophe (') and dash (-).

- response body :
```
{
    "id": 1,
    "name": "John",
    "surname": "Doe",
    "balance": 10000
}
```

Each user is assigned 100$ or 10000 cents as a starting balance to make orders later on.

### 1.2 View a user
- endpoint : /users/{id}
- method : GET
- url : http://localhost:8098/users/{id}
- response body :

```
{
    "id": 1,
    "name": "John",
    "surname": "Doe",
    "balance": 10000
}
```

### 1.3 View all user
- endpoint : /users
- method : GET
- url : http://localhost:8098/users
- response body :

```
[
    {
        "id": 1,
        "name": "John",
        "surname": "Doe",
        "balance": 10000
    },
    {
        "id": 2,
        "name": "Alice",
        "surname": "Doe",
        "balance": 10000
    }
]
```

### 2.1 Create a new product for user
- endpoint : /users/{id}/products
- method : POST
- url : http://localhost:8098/users/{id}/products
- request body :

```
{
	"type" : "t-shirt",
	"title" : "just do it",
	"sku" : "100-abc-999",
	"cost" : 1000
}
```

> "type" : valid product types are "t-shirt" and "mug". They can be written in upper or lowercase e.g. "T-Shirt" is valid.

> "title" : can consist of upper and lowercase letters, digits, dash (-) and space ( ). It is stored as a string later on.

> "sku" : (stock keeping unit) must be unique among products user has submitted. It is stored as a string later on.

> "cost" : must be an integer representing cents

- response body :

```
{
    "id": 1,
    "ownerId": 1,
    "type": "t-shirt",
    "title": "just do it",
    "sku": "100-abc-999",
    "cost": 1000
}
```

### 2.2 View user's product
- endpoint : /users/{id}/products/{id}
- method : GET
- url : http://localhost:8098/users/{id}/products/{id}
- response body :

```
{
    "id": 1,
    "ownerId": 1,
    "type": "t-shirt",
    "title": "just do it",
    "sku": "100-abc-999",
    "cost": 1000
}
```

### 2.3 View user's all products
- endpoint : /users/{id}/products
- method : GET
- url : http://localhost:8098/users/{id}/products
- response body :

```
[
    {
        "id": 1,
        "ownerId": 1,
        "type": "t-shirt",
        "title": "just do it",
        "sku": "100-abc-999",
        "cost": 1000
    },
    {
        "id": 2,
        "ownerId": 1,
        "type": "t-shirt",
        "title": "lui v",
        "sku": "100-abc-100",
        "cost": 1000
    }
]
```

### 3.1 Create a new order for user
- endpoint : /users/{id}/orders
- method : POST
- url : http://localhost:8098/users/{id}/orders
- request body :

```
{
	"shipToAddress": {
		"name" : "John",
		"surname" : "Doe",
		"street" : "Palm street 255",
		"state" : "NY",
		"zip" : "12315",
		"country" : "US",
		"phone" : "917-568-2970"
	},
	"lineItems": [
		{"id": 1, "quantity": 1},
		{"id": 2, "quantity": 1}
	],
	"info":
	{
		"expressShipping": true
	}
}
```

`shipToAddress`

Orders can be either international or domestic (US).

Domestic order's "shipToAddress" must include all keys as in the example above, but for international orders "state" and "zip" keys are optional.

> "name" : accepts upper and lowercase letters, spaces, dot (.) , comma (,) , apastrophe (') and dash (-).

> "surname" : accepts upper and lowercase letters, spaces, dot (.) , comma (,) , apastrophe (') and dash (-).

> "street" : accepts upper and lowercase letters, spaces, dash (-) and digits.

> "state" : accepts all US states as shortform "NY" or long form "New York". Both can be written as a combination of upper and lowercase letters. "ny" and "new york" are both valid.

> "zip" : accepts the five digit and nine digit formats e.g. 12345 or 12345-6789 works, but not 1234 or 12345-67899.

> "country" : accepts two letter code "LV", three letter code "LVA" and name "Latvia". Made up countries are not accepted.

> "phone" : accepts a string or digits only. It is stored as a string later on.

`lineItems`

Each object within lineItems array represents a product within the order.

> "id" : id of the product user has created.

> "quantity" : how many units of the product are requested.

`info`

This part is only applicable to US orders and is optional. If `expressShipping` is set to `true`, then express shipping is enabled for the order. In that case, shipping costs for each product is 10$ (1000 cents).

- If the user has sufficient funds, order is created. Once it is created, funds from user are deducted.

- Response body includes additional data about each ordered item and about order and it's costs :

```
{
    "shipToAddress": {
        "name": "John",
        "surname": "Doe",
        "street": "Palm Street 255",
	"state" : "NY",
	"zip" : "12315",
        "country": "US",
        "phone": "917-568-2970"
    },
    "lineItems": [
        {
            "id": 1,
            "quantity": 1,
            "ownerId": 1,
            "type": "t-shirt",
            "title": "just do it",
            "sku": "100-abc-999",
            "cost": 1000,
            "totalCost": 1000
        },
        {
            "id": 2,
            "quantity": 1,
            "ownerId": 1,
            "type": "t-shirt",
            "title": "mo mamba",
            "sku": "100-abc-100",
            "cost": 1000,
            "totalCost": 1000
        }
    ],
    "info": {
        "id": 3,
        "ownerId": 1,
        "productionCost": 2000,
        "shippingCost": 2000,
        "expressShipping": true,
        "totalCost": 4000
    }
}
```

### 3.2 View user's order
- endpoint : /users/{id}/orders/{id}
- method : GET
- url : http://localhost:8098/users/{id}/orders/{id}
- response body :

```
{
    "shipToAddress": {
        "name": "John",
        "surname": "Doe",
        "street": "Palm Street 255",
        "country": "US",
        "phone": "917-568-2970"
    },
    "lineItems": [
        {
            "id": 1,
            "quantity": 1,
            "ownerId": 1,
            "type": "t-shirt",
            "title": "just do it",
            "sku": "100-abc-999",
            "cost": 1000,
            "totalCost": 1000
        },
        {
            "id": 2,
            "quantity": 1,
            "ownerId": 1,
            "type": "t-shirt",
            "title": "mo mamba",
            "sku": "100-abc-100",
            "cost": 1000,
            "totalCost": 1000
        }
    ],
    "info": {
        "id": 3,
        "ownerId": 1,
        "productionCost": 2000,
        "shippingCost": 2000,
        "expressShipping": true,
        "totalCost": 4000
    }
}
```


### 3.3 View user's all orders
- endpoint : /users/{id}/orders
- method : GET
- url : http://localhost:8098/users/{id}/orders
- response body :

```
[
    {
        "shipToAddress": {
            "name": "John",
            "surname": "Doe",
            "street": "Palm Street 255",
            "country": "US",
            "phone": "917-568-2970"
        },
        "lineItems": [
            {
                "id": 1,
                "quantity": 1,
                "ownerId": 1,
                "type": "t-shirt",
                "title": "just do it",
                "sku": "100-abc-999",
                "cost": 1000,
                "totalCost": 1000
            },
            {
                "id": 2,
                "quantity": 1,
                "ownerId": 1,
                "type": "t-shirt",
                "title": "mo mamba",
                "sku": "100-abc-100",
                "cost": 1000,
                "totalCost": 1000
            }
        ],
        "info": {
            "id": 1,
            "ownerId": 1,
            "productionCost": 2000,
            "shippingCost": 200,
            "totalCost": 2200
        }
    },
    {
        "shipToAddress": {
            "name": "John",
            "surname": "Doe",
            "street": "Palm Street 255",
            "country": "US",
            "phone": "917-568-2970"
        },
        "lineItems": [
            {
                "id": 1,
                "quantity": 1,
                "ownerId": 1,
                "type": "t-shirt",
                "title": "just do it",
                "sku": "100-abc-999",
                "cost": 1000,
                "totalCost": 1000
            },
            {
                "id": 2,
                "quantity": 1,
                "ownerId": 1,
                "type": "t-shirt",
                "title": "mo mamba",
                "sku": "100-abc-100",
                "cost": 1000,
                "totalCost": 1000
            }
        ],
        "info": {
            "id": 2,
            "ownerId": 1,
            "productionCost": 2000,
            "shippingCost": 200,
            "totalCost": 2200
        }
    }
]
```
