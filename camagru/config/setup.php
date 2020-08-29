<?php

require_once '/var/www/camagru/src/infrastructure/MysqlConnection.php';

$connection = MysqlConnection::connect();

/* Database settings */

try {
    $query = "ALTER DATABASE camagru CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
    $connection->exec($query);
} catch (PDOException $e) {
    echo "Error while setting up database: " . $e->getMessage();
    die;
}

/* Users table */

try {
    $query = "CREATE TABLE IF NOT EXISTS `users` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `username` varchar(50) NOT NULL UNIQUE,
          `email` varchar(50) NOT NULL UNIQUE,
          `password` varchar(200) NOT NULL,
          `vkey` varchar(50) NOT NULL,
          `verified` tinyint(1) NOT NULL DEFAULT '0',
          `notifyAboutComments` tinyint(1) NOT NULL DEFAULT '1',
          `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`)
      )";
    $connection->exec($query);
} catch (PDOException $e) {
    echo "Error while creating users table: " . $e->getMessage();
    die;
}

/* Insert a user */
require_once '/var/www/camagru/src/infrastructure/entity/User.php';
require_once '/var/www/camagru/src/infrastructure/repository/UserRepository.php';

try {
    $username = 'larry';
    $email = 'lauris.skraucis@gmail.com';
    $password = '$2y$10$zPG6PrrI5YhGQxC93XlZwO5BYSL5iDvlHAS/aw2rl8zyODrFOqhEy';
    $vkey = 'd8ddaa1afc3c044d589d2f182dc1aebe';

    $userRepository = new UserRepository();
    if (!$userRepository->userCount()) {
        $user = new User($username, $email, $password, $vkey);
        $user->save();
        $userRepository->verify($vkey);
    }
} catch (PDOException $e) {
    echo "Error while creating users table: " . $e->getMessage();
    die;
}

/* Reset password table */

try {
    $query = "CREATE TABLE IF NOT EXISTS `passwordReset` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `email` varchar(50) NOT NULL,
          `selector` varchar(200) NOT NULL,
          `token` varchar(200) NOT NULL,
          `expires` varchar(200) NOT NULL,
          PRIMARY KEY (`id`)
      )";
    $connection->exec($query);
} catch (PDOException $e) {
    echo "Error while creating reset password table: " . $e->getMessage();
    die;
}

/* Uploaded posts table */

try {
    $query = "CREATE TABLE IF NOT EXISTS `posts` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `userId` int(11) NOT NULL,
          `imageUrl` varchar(200) NOT NULL,
          `description` varchar(200) NOT NULL,
          `likes` int(11) DEFAULT 0,
          `comments` int(11) DEFAULT 0,
          `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE
      )";
    $connection->exec($query);
} catch (PDOException $e) {
    echo "Error while creating uploaded posts table: " . $e->getMessage();
    die;
}

/* Insert sample posts */

require_once '/var/www/camagru/src/infrastructure/entity/Post.php';
require_once '/var/www/camagru/src/infrastructure/repository/PostRepository.php';

try {
    $userId = 1;
    $postRepository = new PostRepository();
    if (!count($postRepository->getUploadedByUserId($userId))) {
        $data = [
            ['https://i.imgur.com/j3nRW09.jpeg', "Amsterdam is the best! Highly recommend you to get some nice apple pie at Winkel 43 :))"],
            ['https://i.imgur.com/mTaK276.jpeg', "Today I was participating at a hackathon! Thanks garage48 for this awesome experience."],
            ['https://i.imgur.com/EyYp6h3.jpeg', "Made this drawing for a friend as a gift! Drawing is awesome haha!"],
            ['https://i.imgur.com/SJLpAta.jpeg', "mmm what should I do today? leave a comment and I will do it."],
            ['https://i.imgur.com/6da8SBb.jpeg', "just saw a dragon fly by :D"],
            ['https://i.imgur.com/KSgaptk.jpeg', "i cant see anything help"],
            ['https://i.imgur.com/dZwzV2S.jpeg', "Always amazes me of how smart people were hundreds of years ago :o"],
            ['https://i.imgur.com/cVezLjw.jpeg', "selling gloves for 40 euros. brand new. DM for more details."],
            ['https://i.imgur.com/8ThOGPL.jpeg', "Hack In The Box is an amazing cybersecurity conference. Highly recommend it to you :)"],
            ['https://i.imgur.com/1qOEPsZ.jpeg', "london was really fancy! the people were chill and tea tasty!"],
            ['https://i.imgur.com/foSBQOB.jpeg', "Painted this one today. What ya think?"],
            ['https://i.imgur.com/PjNV18Q.jpeg', "yes philosophy is very thought provoking. thanks marcus aurelius really made me think about life."],
            ['https://i.imgur.com/IJKiIZY.jpeg', "Serbia was really fun. Thanks Sasha for the experience and hosting! Looking forward visiting again))"],
            ['https://i.imgur.com/0aMpuy7.jpeg', "This is Latvian winter 2016 before global warming :(("],
            ['https://i.imgur.com/UuWdKnB.jpeg', "Got new headphones today. sony mx 1000 m3 are good headphones but bluetooth doesn't work well if you use them on multiple devices. hope they fix this on next release :)) but they are niiice"],
        ];
        foreach ($data as $row) {
            $post = new Post($userId, $row[0], $row[1]);
            $post->save();
        }
    }
} catch (PDOException $e) {
    echo "Error while creating posts entry: " . $e->getMessage();
    die;
}

/* Liked posts table */

try {
    $query = "CREATE TABLE IF NOT EXISTS `postsLiked` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `postId` int(11) NOT NULL,
          `userId` int(11) NOT NULL,
          PRIMARY KEY (`id`),
          FOREIGN KEY (`postId`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
          FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE
      )";
    $connection->exec($query);
} catch (PDOException $e) {
    echo "Error while creating liked posts table: " . $e->getMessage();
    die;
}

/* Catalog posts' comments table */

try {
    $query = "CREATE TABLE IF NOT EXISTS `comments` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `postId` int(11) NOT NULL,
          `commentatorId` int(11) NOT NULL,
          `content` varchar(200) NOT NULL,
          `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          FOREIGN KEY (`postId`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
          FOREIGN KEY (`commentatorId`) REFERENCES `users` (`id`) ON DELETE CASCADE
      )";
    $connection->exec($query);
} catch (PDOException $e) {
    echo "Error while creating comments table: " . $e->getMessage();
    die;
}
