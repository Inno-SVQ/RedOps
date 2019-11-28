<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends UuidModel
{

    public $domainsAdded = null;
    public $servicesAdded = null;

    public function domainsAdded() {
        if($this->domainsAdded == null) {
            $this->domainsAdded = Domain::where('from_job_id', $this->id)->get();
        }
        return $this->domainsAdded;
    }

    public function servicesAdded() {
        if($this->servicesAdded == null) {
            $this->servicesAdded = Service::where('from_job_id', $this->id)->get();
        }
        return $this->servicesAdded;
    }

    public function getAudit() {
        return Audit::where('id', $this->audit_id)->first();
    }

    public function getOwner() {
        return $this->getAudit()->getOwner();
    }

}
