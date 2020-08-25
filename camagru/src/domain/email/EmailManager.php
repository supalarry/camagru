<?php

require_once '/var/www/camagru/config/apis.php';

class EmailManager
{
    private $mailApiUrl;

    private $user;

    private $pass;

    function __construct()
    {
        $this->mailApiUrl = 'https://api.sendgrid.com/v3/mail/send';
        $this->user = $GLOBALS['sendGridUser'];
        $this->pass = $GLOBALS['sendGridPass'];
    }

    public function sendVerificationEmail(string $to, string $vkey, string $username): bool
    {
        $data = [
            "personalizations" => [
                [
                    "to" => [
                        [
                            "email" => $to
                        ]
                    ]
                ]
            ],
            "from"=> [
                "email" => $this->user
            ],
            "subject" => 'Camagru account verification',
            "content" => [
                [
                    "type" => "text/html",
                    "value" => "<h2>Hey {$username} :)</h2>
      <p>Welcome to camagru. Before you can start making amazing pictures, please, confirm your profile by clicking 'Confirm'</p>
      <h2><a href='http://localhost:8098/verified?vkey={$vkey}'>Confirm</a></h2>
      <h2>Enjoy!</h2>"
                ]
            ]
        ];
        return $this->send($data);
    }

    public function sendResetPasswordEmail(string $to, string $selector, string $token): bool
    {
        $token = bin2hex($token);
        $data = [
            "personalizations" => [
                [
                    "to" => [
                        [
                            "email" => $to
                        ]
                    ]
                ]
            ],
            "from"=> [
                "email" => $this->user
            ],
            "subject" => 'Password reset',
            "content" => [
                [
                    "type" => "text/html",
                    "value" => "<h2>Hey there :)</h2>
      <p>We heard you want to reset your password. Press 'Reset' to do so.</p>
      <h2><a href='http://localhost:8098/resetPassword?selector={$selector}&token={$token}'>Reset</a></h2>
      <h2>Enjoy!</h2>"
                ]
            ]
        ];
        return $this->send($data);
    }

    public function sendNewCommentNotificationEmail(string $to, string $commentator, string $comment)
    {
        $data = [
            "personalizations" => [
                [
                    "to" => [
                        [
                            "email" => $to
                        ]
                    ]
                ]
            ],
            "from"=> [
                "email" => $this->user
            ],
            "subject" => "${commentator} commented your post",
            "content" => [
                [
                    "type" => "text/html",
                    "value" => "<h2>Hey there :)</h2>
      <p>${commentator} commented one of your posts: </p>
      <p>${comment}</p>"
                ]
            ]
        ];
        return $this->send($data);
    }

    public function send($data)
    {
        $headers = [
            'Authorization: Bearer ' . $this->pass,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.sendgrid.com/v3/mail/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        if (isset($response['errors'])) {
            return false;
        }
        return true;
    }
}
