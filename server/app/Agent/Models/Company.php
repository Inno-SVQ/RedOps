<?php


namespace App\Agent\Models;


class Company
{

    public $id;
    public $name;
    public $main_domain;
    public $type = '__company__';


    public function __construct($id, $name, Domain $domain)
    {
        $this->id = $id;
        $this->name = $name;
        $this->main_domain = $domain;
    }

    public static function  fromEloquent(\App\Company $company) {
        $mainDomain = \App\Domain::where([['company_id', $company->id], ['is_main', true]])->first();
        return new Company($company->id, $company->name, Domain::fromEloquent($mainDomain));
    }

}