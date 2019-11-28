<?php


namespace App\Agent\Models;


class Service
{

    public $host = null;
    public $port = null;
    public $protocol = null;
    public $version = null;
    public $product = null;
    public $application_protocol = null;
    public $type = '__service__';

    public function __construct($host, $port, $protocol, $version, $product, $application_protocol)
    {
        $this->host = $host;
        $this->port = $port;
        $this->protocol = $protocol;
        $this->version = $version;
        $this->product = $product;
        $this->application_protocol = $application_protocol;
    }

    public static function  fromEloquent(\App\Service $service) {
        return new Service($service->host_id, $service->port, $service->protocol, $service->version, $service->product, $service->application_protocol);
    }

    public function toEloquent() {
        $new = new \App\Service();
        $new->host_id = $this->host;
        $new->port = $this->port;
        $new->protocol = $this->protocol;
        $new->product = $this->product;
        $new->version = $this->version;
        $new->application_protocol = $this->application_protocol;
        return $new;
    }

}