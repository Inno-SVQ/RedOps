<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Host extends UuidModel
{

    public $domains = null;

    public function domains()
    {
        if($this->domains == null) {
            $this->domains = $this->hasMany('App\Domain')->get();
        }
        return $this->domains;
    }

}
