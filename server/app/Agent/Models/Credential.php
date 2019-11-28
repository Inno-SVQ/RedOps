<?php


namespace App\Agent\Models;


class Credential
{

    public $username;
    public $password;
    public $domain;
    public $source;
    public $type = '__credential__';


    public function __construct($username, $password, $domain, $source)
    {
        $this->username = $username;
        $this->password = $password;
        $this->domain = $domain;
        $this->source = $source;
    }

    public static function fromEloquent(\App\Credential $credential) {
        return new Credential($credential->username, $credential->password, $credential->domain, $credential->source);
    }

    public function toEloquent() {
        $new = new \App\Credential();
        $new->username = $this->username;
        $new->password = $this->password;
        $new->domain = $this->domain;
        $new->source = $this->source;
        return $new;
    }

}