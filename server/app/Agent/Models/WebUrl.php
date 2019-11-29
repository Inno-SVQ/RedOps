<?php


namespace App\Agent\Models;


class WebUrl
{

    public $serviceId = null;
    public $host = null;
    public $port = null;
    public $path = null;
    public $fileType = null;
    public $wordLength = null;
    public $charLength = null;
    public $statusCode = null;
    public $type = '__weburl__';

    public function __construct($serviceId, $host, $port, $path, $fileType, $wordLength, $charLength, $statusCode)
    {
        $this->serviceId = $serviceId;
        $this->host = $host;
        $this->port = $port;
        $this->path = $path;
        $this->fileType = $fileType;
        $this->wordLength = $wordLength;
        $this->charLength = $charLength;
        $this->statusCode = $statusCode;
    }

    public static function  fromEloquent(\App\WebUrl $webUrl) {
        return new WebUrl($webUrl->service_id, $webUrl->getService()->getDomain()->domain, $webUrl->getService()->port, $webUrl->path, $webUrl->file_type, $webUrl->word_length, $webUrl->char_length, $webUrl->status_code);
    }

    public function toEloquent() {
        $new = new \App\WebUrl();
        $new->service_id = $this->serviceId;
        $new->path = $this->path;
        $new->file_type = $this->fileType;
        $new->word_length = $this->wordLength;
        $new->char_length = $this->charLength;
        $new->status_code = $this->statusCode;
        return $new;
    }

}