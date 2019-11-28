<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Ramsey\Uuid\Uuid;

class Audit extends UuidModel
{

    public $companies = null;
    public $jobs = null;
    public $openJobs = null;

    public function getProgress()
    {

        if($this->startDate > Carbon::now()) {
            return 0;
        }

        return Utils::percentage(
            Utils::timeBetweenDatesMillis($this->startDate, $this->endDate),
            Utils::timeBetweenDatesMillis($this->startDate, Carbon::now())
        );
    }

    public function companies()
    {
        if($this->companies == null) {
            $this->companies = $this->hasMany('App\Company')->get();
        }
        return $this->companies;
    }

    public function jobs()
    {
        if($this->jobs == null) {
            $this->jobs = $this->hasMany('App\Job')->orderBy('created_at', 'desc')->get();
        }
        return $this->jobs;
    }

    public function openJobs()
    {
        if($this->openJobs == null) {
            $this->openJobs = Job::where('audit_id', $this->id)
                ->where('status', 0)
                ->get();
        }
        return $this->openJobs;
    }

    public function domains()
    {
        $domains = array();
        foreach ($this->companies() as $company) {
            foreach ($company->domains() as $domain) {
                array_push($domains, $domain);
            }
        }
        return $domains;
    }

    public function services()
    {
        $services = array();
        foreach ($this->companies() as $company) {
            foreach ($company->domains() as $domain) {
                foreach ($domain->services() as $service) {
                    array_push($services, $service);
                }
            }
        }
        return $services;
    }

    public function getOwner() {
        return User::where('rid', $this->owner)-first();
    }
}
