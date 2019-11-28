<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends UuidModel
{

    public $host = null;

    public function getDomain() {
        return Domain::where('id', $this->host_id)->first();
    }

}
