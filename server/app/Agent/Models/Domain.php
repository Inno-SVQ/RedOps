<?php


namespace App\Agent\Models;


use function React\Promise\Stream\first;

class Domain
{

    public $id = null;
    public $name = null;
    public $parent = null;
    public $ip = null;
    public $type = '__domain__';

    public function __construct($id, $name, $parent, $ip)
    {
        $this->id = $id;
        $this->name = $name;
        $this->parent = $parent;
        $this->ip = $ip;
    }

    public static function fromEloquent(\App\Domain $domain) {
        return new Domain($domain->id, $domain->domain, $domain->domain_id, $domain->ip);
    }

    public function toEloquent() {
        $new = new \App\Domain();
        $new->domain = $this->name;
        $new->domain_id = $this->parent;
        $new->ip = $this->ip;
        $new->company_id = \App\Domain::where('id', $this->parent)->firstOrFail()->company_id;
        return $new;
    }

}