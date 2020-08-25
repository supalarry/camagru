<?php

require_once 'base/BaseController.php';
require_once 'base/Route.php';
require_once '/var/www/camagru/src/domain/app/ImageManager.php';
require_once '/var/www/camagru/src/domain/app/PostManager.php';
require_once '/var/www/camagru/templates/views/View.php';

class AppController extends BaseController
{
    private $imageManager;
    private $postManager;

    function __construct()
    {
        $this->imageManager = new ImageManager();
        $this->postManager = new PostManager();

        parent::__construct([
            new Route('get', '/editor',
                function ($request) {
                    try {
                        $view = new View('/var/www/camagru/templates/views/app/Editor.php');
                        $args = [];
                        if (isset($request->getSession()['id'])) {
                            $args['id'] = $request->getSession()['id'];
                        } else {
                            $args['id'] = 'undefined';
                        }
                        header('HTTP/1.1 200 OK');
                        echo $view->render($args);
                    } catch (Exception $e) {
                        header('HTTP/1.1 500 Internal Server Error');
                        return json_encode($e->getMessage());
                    }
                }, true, false
            ),
            new Route('post', '/post',
                function ($request) {
                    try {
                        $response = [];
                        $image = $this->postManager->post($request->getSession()['id'], $request->getBody());
                        if ($image) {
                            $response['message'] = 'success';
                            $response['image'] = $image;
                            header('HTTP/1.1 201 Created');
                        } else {
                            $response['error'] = $this->postManager->getErrors();
                            header('HTTP/1.1 400 Bad Request');
                        }
                        return json_encode($response);
                    } catch (Exception $e) {
                        header('HTTP/1.1 500 Internal Server Error');
                        return json_encode($e->getMessage());
                    }
                }, true, false
            ),
            new Route('post', '/generateImage',
                function ($request) {
                    try {
                        $response = [];
                        $imageUrl = $this->imageManager->generate($request->getBody());
                        if ($imageUrl) {
                            $response['message'] = 'success';
                            $response['imageUrl'] = $imageUrl;
                            header('HTTP/1.1 201 Created');
                        } else {
                            $response['message'] = 'error';
                            header('HTTP/1.1 400 Bad Request');
                        }
                        return json_encode($response);
                    } catch (Exception $e) {
                        header('HTTP/1.1 500 Internal Server Error');
                        return json_encode($e->getMessage());
                    }
                }, true, false
            ),
            new Route('get', '/editorUploads',
                function ($request) {
                    try {
                        $response = $this->postManager->getUploadedByUserId($request->getSession()['id']);
                        header('HTTP/1.1 200 OK');
                        return json_encode($response);
                    } catch (Exception $e) {
                        header('HTTP/1.1 500 Internal Server Error');
                        return json_encode($e->getMessage());
                    }
                }, true, false
            ),
            new Route('post', '/deletePost',
                function ($request) {
                    try {
                        $response = [];
                        $deleted = $this->postManager->deletePostByUserIdPostId($request->getSession()['id'], $request->getBody()['postId']);
                        if ($deleted) {
                            $response['message'] = 'success';
                            header('HTTP/1.1 200 OK');
                        } else {
                            $response['error'] = 'error while deleting image';
                            header('HTTP/1.1 400 Bad Request');
                        }
                        return json_encode($response);
                    } catch (Exception $e) {
                        header('HTTP/1.1 500 Internal Server Error');
                        return json_encode($e->getMessage());
                    }
                }, true, false
            ),
        ]);
    }
}
