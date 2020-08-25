# Camagru

School 42 project.

- Backend in vanilla PHP
- Custom built router and controllers
- Frontend in HTML, CSS and vanilla Javascript
- Runs on nginx server
- Scripts executed by php-fpm
- Data stored using MySQL
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

Add API key for Imgur image uploading

```
camagru/src/domain/app/ImageManager.php $imgurClientId
```

Add API key for SendGrid email

```
camagru/src/domain/email/EmailManager.php $user and $pass
```


Run the app using docker

```
docker-compose up
```

Open the app

```
http://localhost:8098/catalog
```

## Logging into phpmyadmin

Log into phpmyadmin http://localhost:8088 using:

```
username: root
password: rootroot
```

## Database setup

Sample user with sample posts are created in config file

```
camagru/config/database.php
```

I would have used data dump in Docker setup, but school's
subject required having database setup in that file.
