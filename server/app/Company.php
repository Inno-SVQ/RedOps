<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends UuidModel
{

    public $domains = null;

    public function parent()
    {
        return $this->belongsTo(Company::class, 'parent');
    }

    public function audit()
    {
        return Audit::where('id', $this->audit_id)->firstOrFail();
    }

    public function domains()
    {
        if($this->domains == null) {
            $this->domains = $this->hasMany('App\Domain', 'company_id', 'id')->get();
        }
        return $this->domains;
    }
}
