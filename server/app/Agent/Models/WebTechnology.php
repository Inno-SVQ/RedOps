<?php


namespace App\Agent\Models;


class WebTechnology
{

    public $name = null;
    public $icon = null;
    public $service_id = null;
    public $type = '__technology__';

    public function __construct($name, $icon, $service_id)
    {
        $this->name = $name;
        $this->icon = $icon;
        $this->service_id = $service_id;
    }

    public static function fromEloquent(\App\WebTechnology $webTechnology) {
        return new WebTechnology($webTechnology->name, $webTechnology->icon, $webTechnology->service_id);
    }

    public function toEloquent() {
        $new = new \App\WebTechnology();
        $new->id = $this->id;
        $new->name = $this->name;
        $new->icon = $this->icon;
        $new->service_id = $this->service_id;
        return $new;
    }

}