<!DOCTYPE html>
<html lang="en">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>RedOps | InnoSVQ </title>

        <!-- Bootstrap -->
        <link href="{{ asset("css/bootstrap.min.css") }}" rel="stylesheet">
        <!-- Font Awesome -->
        <link href="{{ asset("css/font-awesome.min.css") }}" rel="stylesheet">
        <!-- Custom Theme Style -->
        <link href="{{ asset("css/gentelella.min.css") }}" rel="stylesheet">
        <!-- Nprogress -->
        <link href="{{ asset("css/nprogress.css") }}" rel="stylesheet">
        <!-- Datatables -->
        <link href="{{ asset("css/datatables/jquery.dataTables.min.css") }}" rel="stylesheet">

        <link href="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.buttons.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.nonblock.css" rel="stylesheet">

        @stack('stylesheets')

    </head>

    <body class="nav-md">
        <div class="container body">
            <div class="main_container">

                @include('includes/sidebar')

                @include('includes/topbar')

                @yield('main_container')

                @include('includes/footer')

            </div>
        </div>

        <!-- jQuery -->
        <script src="{{ asset("js/jquery.min.js") }}"></script>
        <!-- Bootstrap -->
        <script src="{{ asset("js/bootstrap.min.js") }}"></script>
        <!-- Bootstrap progressbar -->
        <script src="{{ asset("js/bootstrap-progressbar.min.js") }}"></script>
        <!-- Nprogress -->
        <script src="{{ asset("js/nprogress.js") }}"></script>
        <!-- Custom Theme Scripts -->
        <script src="{{ asset("js/gentelella.min.js") }}"></script>

        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.6.0/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.6.0/js/buttons.flash.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.6.0/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.6.0/js/buttons.print.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.buttons.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.nonblock.js"></script>
        <script src={{ asset("js/app.js") }}></script>

        <script>

            $( document ).ready(function() {
                @if(isset($selectedAudit))
                    jobUpdate({{count($selectedAudit->openJobs())}});
                @endif
            });

            function jobUpdate(openJobs) {
                if($('#badge-current-jobs').length) {
                    if (openJobs > 0) {
                        $('#badge-current-jobs').show()
                        $('#badge-current-jobs').text(openJobs)
                        $('#i-current-jobs').removeClass("fa-inbox")
                        $('#i-current-jobs').addClass("fa-refresh")
                        $('#i-current-jobs').addClass("fa-spin")
                    } else {
                        $('#badge-current-jobs').text(0)
                        $('#badge-current-jobs').hide()
                        $('#i-current-jobs').removeClass("fa-spin")
                        $('#i-current-jobs').removeClass("fa-refresh")
                        $('#i-current-jobs').addClass("fa-inbox")
                    }
                }
            }

            function deletedCompanies(rows) {
                $('#datatable-companies').DataTable({retrieve: true}).ajax.reload(null, false);
            }

            function addedCompany(company) {
                $('#datatable-companies').DataTable({retrieve: true}).ajax.reload(null, false);
            }

            function deletedDomains(domains) {
                $('#datatable-domains').DataTable({retrieve: true}).ajax.reload(null, false);
            }

            function addedDomain(company) {
                $('#datatable-domains').DataTable({retrieve: true}).ajax.reload(null, false);
            }

            function addedService(service) {
                $('#datatable-services').DataTable({retrieve: true}).ajax.reload(null, false);
            }

            function deletedServices(services) {
                $('#datatable-services').DataTable({retrieve: true}).ajax.reload(null, false);
            }

            function addedCredential(credential) {
                $('#datatable-credentials').DataTable({retrieve: true}).ajax.reload(null, false);
            }

            function notification(options) {
                new PNotify({
                    title: options.title,
                    text: options.text,
                    type: options.type,
                    styling: 'bootstrap3'
                });
            }

            Echo.private('user.{{\Illuminate\Support\Facades\Auth::user()->rid}}').listen('WsMessage', (e) => {
                console.log(e.eventType);
                content = JSON.parse(e.jsonContent);
                switch (e.eventType) {
                    case 'jobUpdate':
                        jobUpdate(content);
                        break;
                    case 'deletedCompanies':
                        deletedCompanies(content);
                        break;
                    case 'addedCompany':
                        addedCompany(content);
                        break;
                    case 'deletedDomains':
                        deletedDomains(content);
                        break;
                    case 'addedDomain':
                        addedDomain(content);
                        break;
                    case 'addedService':
                        addedService(content);
                        break;
                    case 'addedCredential':
                        addedCredential(content);
                        break;
                    case 'deletedServices':
                        deletedServices(content);
                        break;
                    case 'notification':
                        notification(content);
                        break;
                }
            });

        </script>

        @stack('scripts')

    </body>
</html>