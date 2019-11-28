<?php


namespace App\Events;


use App\Models\User;

class CurrentJobsUpdate
{

    public $currentJobs;

    public function __construct($currentJobs) {
        $this->$currentJobs = $currentJobs;
    }

}