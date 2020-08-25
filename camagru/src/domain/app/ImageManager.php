<?php

class ImageManager
{
    private $imgurClientId;

    private $backgroundImagePath;

    private $outputImagePath;

    private $errors;

    function __construct()
    {
        $this->imgurClientId = '';
        $this->backgroundImagePath = $_SERVER['DOCUMENT_ROOT'] . '/background.jpeg';
        $this->outputImagePath = $_SERVER['DOCUMENT_ROOT'] . '/out.jpeg';
        $this->errors = [];
    }

    function generate($images)
    {
        $backgroundPath = $this->saveBackground($images['background']);
        $mergedPath = $this->merge($backgroundPath, $images['frames']);
        $uploadedImageUrl = $this->uploadToStorage($mergedPath);
        $this->deleteTempImages();
        return $uploadedImageUrl;
    }

    function saveBackground($background): string
    {
        $background = str_replace('data:image/jpeg;base64,', '', $background);
        $background = str_replace(' ', '+', $background);
        $backgroundData = base64_decode($background);
        $backgroundPath = $this->backgroundImagePath;
        file_put_contents($backgroundPath, $backgroundData);
        return $backgroundPath;
    }

    function merge($backgroundPath, $frames)
    {
        $jpeg = imagecreatefromjpeg($backgroundPath);
        $frames = array_reverse(array_values($frames));

        if (!count($frames)) {
            copy($this->backgroundImagePath, $this->outputImagePath);
        } else {
            foreach ($frames as $frame) {
                $png = imagecreatefrompng($_SERVER['DOCUMENT_ROOT'] . '/' . $frame);
                list($width, $height) = getimagesize($frame);
                list($newwidth, $newheight) = getimagesize($backgroundPath);
                $out = imagecreatetruecolor($newwidth, $newheight);
                imagecopyresampled($out, $jpeg, 0, 0, 0, 0, $newwidth, $newheight, $newwidth, $newheight);
                imagecopyresampled($out, $png, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                imagejpeg($out, $this->outputImagePath, 100);
                $jpeg = imagecreatefromjpeg($this->outputImagePath);
            }
        }

        return $this->outputImagePath;
    }

    function uploadToStorage($imagePath)
    {
        $ch = curl_init();
        $curl_file_upload = new CURLFile($imagePath, "application/octet-stream", "out.jpg");
        $data = array(
            "image" => $curl_file_upload
        );
        curl_setopt($ch, CURLOPT_URL, 'https://api.imgur.com/3/image');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'authorization: Client-ID ' . $this->imgurClientId
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);

        if ($response && isset($response['success']) && $response['success']) {
            return 'https://i.imgur.com/' . $response['data']['id'] . '.jpeg';
        }
        return '';
    }

    function deleteTempImages()
    {
        if (is_file($this->backgroundImagePath))
        {
            unlink($this->backgroundImagePath);
        }
        if (is_file($this->outputImagePath))
        {
            unlink($this->outputImagePath);
        }
    }

    function getErrors(): array
    {
        return $this->errors;
    }
}
