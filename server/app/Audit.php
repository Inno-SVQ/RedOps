<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Ramsey\Uuid\Uuid;
use DB;

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

    public function differentTechnologies() {
        $webtechonolgies = array();
        foreach ($this->services() as $service) {
            $technologies = $service->technologies();
            foreach($technologies as $technology) {
                if(array_key_exists($technology->name, $webtechonolgies)) {
                    $webtechonolgies[$technology->name] = $webtechonolgies[$technology->name] + 1;
                } else {
                    $webtechonolgies[$technology->name] = 1;
                }
            }
        }
        asort($webtechonolgies);
        $webtechonolgies = array_reverse($webtechonolgies);
        return $webtechonolgies;
    }

    public function getCredentials() {
        return Credential::where('audit_id', $this->id)->get();
    }

    public function getJobsByHours() {

        $result = [];
        $items = DB::table('jobs')
            ->where('audit_id', $this->id)
            ->select(DB::raw('count(*) as count, HOUR(created_at) as hour'))
            ->whereDate('created_at', '=', Carbon::now()->toDateString())
            ->groupBy('hour')
            ->get();

        for ($x = 0; $x < count($items); $x++) {
            array_push($result, $items[$x]->count);
        }

        dd($items);

        return json_encode($result);
    }

    public function getDomainsAddedByHour() {
        $result = [];
        $items = DB::table('domains')
            ->where('audit_id', $this->id)
            ->union('')
            ->select(DB::raw('count(*) as count, HOUR(created_at) as hour'))
            ->whereDate('created_at', '=', Carbon::now()->toDateString())
            ->groupBy('hour')
            ->get();

        for ($x = 0; $x < count($items); $x++) {
            array_push($result, $items[$x]->count);
        }

        return json_encode($result);
    }

    public function getLeakedCredentialsAddedByHour() {
        return DB::table('credentials')
            ->select(DB::raw('count(*) as count, HOUR(created_at) as hour'))
            ->whereDate('created_at', '=', Carbon::now()->toDateString())
            ->groupBy('hour')
            ->get();
    }

    public function getServicesAddedByHour() {
        return DB::table('services')
            ->select(DB::raw('count(*) as count, HOUR(created_at) as hour'))
            ->whereDate('created_at', '=', Carbon::now()->toDateString())
            ->groupBy('hour')
            ->get();
    }

    public function getDomainInsertEvents() {
        $result = array();

        for ($x = 0; $x < 24; $x++) {
            $date_start = new \DateTime();
            $date_end = new \DateTime();
            $date_start->modify('-'.$x.' hours');
            $date_end->modify('-'.($x + 1).' hours');
            $formatted_date_start = $date_start->format('Y-m-d H:i:s');
            $formatted_date_end = $date_end->format('Y-m-d H:i:s');
            $items = DB::table('insert_events')
                ->where('audit_id', $this->id)
                ->where('type', '__domain__')
                ->whereDate('created_at', '>', $formatted_date_end)
                ->whereDate('created_at', '<', $formatted_date_start)
                ->count();
            array_push($result, $items);
        }

        return $result;
    }

    public function getServiceInsertEvents() {
        $result = array();

        for ($x = 0; $x < 24; $x++) {
            $date_start = new \DateTime();
            $date_end = new \DateTime();
            $date_start->modify('-'.$x.' hours');
            $date_end->modify('-'.($x +1).' hours');
            $formatted_date_start = $date_start->format('Y-m-d H:i:s');
            $formatted_date_end = $date_end->format('Y-m-d H:i:s');
            $items = DB::table('insert_events')
                ->where('audit_id', $this->id)
                ->where('type', '__service__')
                ->whereDate('created_at', '>', $formatted_date_end)
                ->whereDate('created_at', '<', $formatted_date_start)
                ->count();
            array_push($result, $items);
        }

        return $result;
    }

    public function getCredntialsInsertEvents() {
        $result = array();

        for ($x = 0; $x < 24; $x++) {
            $date_start = new \DateTime();
            $date_end = new \DateTime();
            $date_start->modify('-'.$x.' hours');
            $date_end->modify('-'.($x +1).' hours');
            $formatted_date_start = $date_start->format('Y-m-d H:i:s');
            $formatted_date_end = $date_end->format('Y-m-d H:i:s');
            $items = DB::table('insert_events')
                ->where('audit_id', $this->id)
                ->where('type', '__credential__')
                ->whereDate('created_at', '>', $formatted_date_end)
                ->whereDate('created_at', '<', $formatted_date_start)
                ->count();
            array_push($result, $items);
        }

        return $result;
    }

    public function getOwner() {
        return User::where('id', $this->owner)->first();
    }
}
