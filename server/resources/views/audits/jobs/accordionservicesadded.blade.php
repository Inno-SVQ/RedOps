@if(count($job->servicesAdded()) > 0)
    <div class="accordion" id="accordion-{{$job->id}}" role="tablist"
         aria-multiselectable="true">
        <div class="panel">
            <a class="panel-heading" role="tab" id="heading-{{$job->id}}"
               data-toggle="collapse" data-parent="#accordion-{{$job->id}}"
               href="#collapse-{{$job->id}}" aria-expanded="true"
               aria-controls="collapse-{{$job->id}}">
                <h4 class="panel-title">{{count($job->servicesAdded())}} Services</h4>
            </a>
            <div id="collapse-{{$job->id}}" class="panel-collapse collapse"
                 role="tabpanel" aria-labelledby="heading-{{$job->id}}">
                <div class="panel-body">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th style="width: 80%">Service</th>
                            <th style="width: 20%">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($job->servicesAdded() as $service)
                            <tr>
                                <td>{{$service->application_protocol}}://{{$service->getDomain()->domain}}:{{$service->port}} ({{$service->protocol}})</td>
                                <td><a type="button" class="btn btn-danger btn-xs" data-toggle="modal"
                                       data-target=".bs-modal-{{$job->id}}"><i class="fa fa-remove"></i>
                                        Remove</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif