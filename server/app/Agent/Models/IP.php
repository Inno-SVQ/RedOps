<?php


namespace App\Agent\Models;


class IP
{
    public $id;
    public $value;
    public $isIPv4;

    public function __construct($id, $value, $isIPv4)
    {
        $this->id = $id;
        $this->value = $value;
        $this->isIPv4 = $isIPv4;
    }
    
}