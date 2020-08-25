<?php

require_once 'base/BaseController.php';
require_once 'base/Route.php';
require_once '/var/www/camagru/templates/views/View.php';
require_once '/var/www/camagru/src/infrastructure/entity/User.php';
require_once '/var/www/camagru/src/domain/auth/UserManager.php';
require_once '/var/www/camagru/src/domain/auth/ResetPasswordManager.php';

class AuthController extends BaseController
{
    private $userManager;

    private $resetPasswordManager;

    function __construct()
    {
        $this->userManager = new UserManager();
        $this->resetPasswordManager = new ResetPasswordManager();

        parent::__construct([
            new Route('get', '/login',
                function ($request) {
                    try {
                        $view = new View('/var/www/camagru/templates/views/auth/Login.php');
                        header('HTTP/1.1 200 OK');
                        echo $view->render();
                    } catch (Exception $e) {
                        header('HTTP/1.1 500 Internal Server Error');
                        return json_encode($e->getMessage());
                    }
                }, false, true
            ),
            new Route('post', '/login',
                function ($request) {
                    try {
                        $response = [];
                        if ($this->userManager->login($request->getBody())) {
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
                }, false, true
            ),
            new Route('post', '/logout',
                function ($request) {
                    try {
                        $this->userManager->logout($request);
                        header('HTTP/1.1 200 OK');
                    } catch (Exception $e) {
                        header('HTTP/1.1 500 Internal Server Error');
                        return json_encode($e->getMessage());
                    }
                }, true, false
            ),
            new Route('get', '/register',
                function ($request) {
                    try {
                        $view = new View('/var/www/camagru/templates/views/auth/Register.php');
                        header('HTTP/1.1 200 OK');
                        echo $view->render();
                    } catch (Exception $e) {
                        header('HTTP/1.1 500 Internal Server Error');
                        return json_encode($e->getMessage());
                    }
                }, false, true
            ),
            new Route('post', '/register',
                function ($request) {
                    try {
                        $response = [];
                        if ($this->userManager->register($request->getBody())) {
                            header('HTTP/1.1 201 Created');
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
                }, false, true
            ),
            new Route('get', '/verify',
                function ($request) {
                    try {
                        $view = new View('/var/www/camagru/templates/views/auth/Verify.php');
                        header('HTTP/1.1 200 OK');
                        echo $view->render();
                    } catch (Exception $e) {
                        header('HTTP/1.1 500 Internal Server Error');
                        return json_encode($e->getMessage());
                    }
                }, false, true
            ),
            new Route('get', '/verified',
                function ($request) {
                    try {
                        if (isset($request->getVariables()['vkey'])) {
                            $view = null;
                            if ($this->userManager->verify($request->getVariables()['vkey'])) {
                                header('HTTP/1.1 200 OK');
                                $view = new View('/var/www/camagru/templates/views/auth/Verified.php');
                            } else {
                                header('HTTP/1.1 400 Bad Request');
                                $view = new View('/var/www/camagru/templates/views/auth/VerifiedProblem.php');
                            }
                        } else {
                            header('HTTP/1.1 400 Bad Request');
                            $view = new View('/var/www/camagru/templates/views/auth/SomethingWentWrong.php');
                        }
                        echo $view->render();
                    } catch (Exception $e) {
                        header('HTTP/1.1 500 Internal Server Error');
                        return json_encode($e->getMessage());
                    }
                }, false, true
            ),
            new Route('get', '/resetPasswordRequest',
                function ($request) {
                    try {
                        $view = new View('/var/www/camagru/templates/views/auth/ResetPasswordRequest.php');
                        header('HTTP/1.1 200 OK');
                        echo $view->render();
                    } catch (Exception $e) {
                        header('HTTP/1.1 500 Internal Server Error');
                        return json_encode($e->getMessage());
                    }
                }, false, true
            ),
            new Route('post', '/resetPasswordRequest',
                function ($request) {
                    try {
                        $response = [];
                        if ($this->resetPasswordManager->addResetPasswordRequest($request->getBody())) {
                            header('HTTP/1.1 200 OK');
                            $response['message'] = 'success';
                        } else {
                            header('HTTP/1.1 400 Bad Request');
                            $response['errors'] = $this->resetPasswordManager->getErrors();
                        }
                        return json_encode($response);
                    } catch (Exception $e) {
                        header('HTTP/1.1 500 Internal Server Error');
                        return json_encode($e->getMessage());
                    }
                }, false, true
            ),
            new Route('get', '/resetPassword',
                function ($request) {
                    try {
                        if (isset($request->getVariables()['selector']) && isset($request->getVariables()['token'])) {
                            $view = null;
                            if ($this->resetPasswordManager->verify($request->getVariables()['selector'], $request->getVariables()['token'])) {
                                $view = new View('/var/www/camagru/templates/views/auth/ResetPassword.php');
                                header('HTTP/1.1 200 OK');
                            } else {
                                header('HTTP/1.1 400 Bad Request');
                                $view = new View('/var/www/camagru/templates/views/auth/ResetPasswordRequestExpired.php');
                            }
                        } else {
                            header('HTTP/1.1 400 Bad Request');
                            $view = new View('/var/www/camagru/templates/views/auth/SomethingWentWrong.php');
                        }
                        echo $view->render();
                    } catch (Exception $e) {
                        header('HTTP/1.1 500 Internal Server Error');
                        return json_encode($e->getMessage());
                    }
                }, false, true
            ),
            new Route('post', '/resetPassword',
                function ($request) {
                    try {
                        $response = [];
                        if ($this->resetPasswordManager->resetPassword($request->getBody())) {
                            header('HTTP/1.1 200 OK');
                            $response['message'] = 'success';
                        } else {
                            header('HTTP/1.1 400 Bad Request');
                            $response['errors'] = $this->resetPasswordManager->getErrors();
                        }
                        return json_encode($response);
                    } catch (Exception $e) {
                        header('HTTP/1.1 500 Internal Server Error');
                        return json_encode($e->getMessage());
                    }
                }, false, true
            ),
        ]);
    }
}
