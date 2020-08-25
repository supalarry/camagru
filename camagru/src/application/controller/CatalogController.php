<?php

require_once 'base/BaseController.php';
require_once 'base/Route.php';
require_once '/var/www/camagru/src/domain/app/PostManager.php';
require_once '/var/www/camagru/src/domain/app/CommentManager.php';
require_once '/var/www/camagru/templates/views/View.php';

class CatalogController extends BaseController
{
    private $postManager;

    private $commentManager;

    function __construct()
    {
        $this->postManager = new PostManager();
        $this->commentManager = new CommentManager();

        parent::__construct([
            new Route('get', '/catalog',
                function ($request) {
                    try {
                        $view = new View('/var/www/camagru/templates/views/app/Catalog.php');
                        $args = [];
                        if (isset($request->getSession()['id'])) {
                            $args['id'] = $request->getSession()['id'];
                            $args['username'] = $request->getSession()['username'];
                        } else {
                            $args['id'] = 'undefined';
                            $args['username'] = 'undefined';
                        }
                        header('HTTP/1.1 200 OK');
                        echo $view->render($args);
                    } catch (Exception $e) {
                        header('HTTP/1.1 500 Internal Server Error');
                        return json_encode($e->getMessage());
                    }
                }, false, false
            ),
            new Route('get', '/posts',
                function ($request) {
                    try {
                        header('HTTP/1.1 200 OK');
                        return json_encode(
                            $this->postManager->getPosts(
                                $request->getVariables()['count'],
                                $request->getVariables()['offset']
                            )
                        );
                    } catch (Exception $e) {
                        header('HTTP/1.1 500 Internal Server Error');
                        return json_encode($e->getMessage());
                    }
                }, false, false
            ),
            new Route('post', '/likePost',
                function ($request) {
                    try {
                        header('HTTP/1.1 200 OK');
                        $this->postManager->likePost($request->getBody()['postId'], $request->getBody()['userId']);
                    } catch (Exception $e) {
                        header('HTTP/1.1 500 Internal Server Error');
                        return json_encode($e->getMessage());
                    }
                }, true, false
            ),
            new Route('post', '/dislikePost',
                function ($request) {
                    try {
                        header('HTTP/1.1 200 OK');
                        $this->postManager->dislikePost($request->getBody()['postId'], $request->getBody()['userId']);
                    } catch (Exception $e) {
                        header('HTTP/1.1 500 Internal Server Error');
                        return json_encode($e->getMessage());
                    }
                }, true, false
            ),
            new Route('get', '/userLikedPosts',
                function ($request) {
                    try {
                        header('HTTP/1.1 200 OK');
                        return json_encode($this->postManager->postsLikedByUser($request->getVariables()['userId']));
                    } catch (Exception $e) {
                        header('HTTP/1.1 500 Internal Server Error');
                        return json_encode($e->getMessage());
                    }
                }, true, false
            ),
            new Route('post', '/commentPost',
                function ($request) {
                    try {
                        header('HTTP/1.1 201 Created');
                        return json_encode($this->commentManager->commentPost($request->getBody()));
                    } catch (Exception $e) {
                        header('HTTP/1.1 500 Internal Server Error');
                        return json_encode($e->getMessage());
                    }
                }, true, false
            ),
            new Route('get', '/postComments',
                function ($request) {
                    try {
                        header('HTTP/1.1 200 OK');
                        return json_encode($this->commentManager->getByPostId($request->getVariables()['postId']));
                    } catch (Exception $e) {
                        header('HTTP/1.1 500 Internal Server Error');
                        return json_encode($e->getMessage());
                    }
                }, false, false
            ),
        ]);
    }
}
