# Camagru

School 42 project. Camagru is an Instagram like web app.

- Backend in vanilla PHP
- Custom built router, request object, controllers and ORM entities to mimic the behaviour of frameworks like Laravel or Symfony.
- Frontend in HTML, CSS + Tailwindcss, vanilla Javascript
- Mobile friendly
- Runs on nginx server
- Scripts executed by php-fpm
- Data stored using MySQL
- Phpmyadmin enabled
- Deployed using Docker

![alt text](https://i.imgur.com/9dkaEGO.png)

When a user registers, he / she needs to verify the account via an email that was sent.
Furthermore, it is possible to reset a password.

![alt text](https://i.imgur.com/Yno0qmp.png)

Logged in user can take a picture via camera or upload it.

![alt text](https://i.imgur.com/E8ig6Vl.png)

If user desires, frames can be added to the image.
After description is added, new post can be submitted.
Also, previous posts are available here and can be deleted too.

![alt text](https://i.imgur.com/sBcBuR4.png)

In the catalog all submitted posts can be viewed, liked, commented and shared.

![alt text](https://i.imgur.com/z2kTG6h.png)

Only logged in users can like and comment.

![alt text](https://i.imgur.com/IaZq8oY.png)

The user can also edit profile settings. Each time user's post is commented a notification via email is sent.

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

Add API keys in

```
camagru/config/apis.php
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
