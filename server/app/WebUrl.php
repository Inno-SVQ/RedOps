<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebUrl extends UuidModel
{

    public function getService() {
        return Service::where('id', $this->service_id)->first();
    }

}
