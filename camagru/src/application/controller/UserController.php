<?php

require_once 'base/BaseController.php';
require_once 'base/Route.php';
require_once '/var/www/camagru/templates/views/View.php';
require_once '/var/www/camagru/src/infrastructure/repository/UserRepository.php';
require_once '/var/www/camagru/src/domain/auth/UserManager.php';

class UserController extends BaseController
{
    private $userRepository;

    private $userManager;

    function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->userManager = new UserManager();

        parent::__construct([
            new Route('get', '/profile',
                function ($request) {
                    try {
                        $view = new View('/var/www/camagru/templates/views/profile/Profile.php');
                        $args = $this->userRepository->getProfileInformation($request->getSession()['id']);
                        header('HTTP/1.1 200 OK');
                        echo $view->render($args);
                    } catch (Exception $e) {
                        header('HTTP/1.1 500 Internal Server Error');
                        return json_encode($e->getMessage());
                    }
                }, true, false
            ),
            new Route('post', '/profile',
                function ($request) {
                    try {
                        $response = [];
                        if ($this->userManager->update($request->getBody())) {
                            header('HTTP/1.1 200 OK');
                            $response['message'] = 'success';
                        } else {
                            header('HTTP/1.1 400 Bad Request');
                            $response['errors'] = $this->userManager->getErrors();
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
