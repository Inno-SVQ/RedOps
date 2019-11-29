<?php


namespace App\Agent\Models;

use Storage;

class WebScreenshot
{

    public $service_id = null;
    public $picture = null;
    public $type = '__webscreenshot__';

    public function __construct($service_id, $picture)
    {
        $this->service_id = $service_id;
        $this->picture = $picture;
    }

    public static function  fromEloquent(\App\WebScreenshot $webScreenshot) {
        $file = Storage::disk('local')->get($webScreenshot->image_name);
        return new WebScreenshot($webScreenshot->image_name, $file);
    }

    public function toEloquent() {
        $new = new \App\WebScreenshot();
        $new->service_id = $this->service_id;
        $new->image_name = $this->service_id . '.jpeg';
        return $new;
    }

}