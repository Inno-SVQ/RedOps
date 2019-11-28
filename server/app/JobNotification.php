<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobNotification extends UuidModel
{

    public function getHumanTime() {
        return \Carbon\Carbon::createFromTimeStamp(strtotime($this->created_at))->diffForHumans();
    }

}
