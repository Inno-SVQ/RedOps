<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Domain extends UuidModel
{

    public function parentCompany()
    {
        return Company::where('id', $this->company_id)->firstOrFail();
    }

    public function services() {
        return Service::where('host_id', $this->id)->get();
    }

}
