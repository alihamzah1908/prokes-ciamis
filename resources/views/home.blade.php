<html>
    <head>
        <title>Peta Prokes Covid 19</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/jquery.dataTables.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/mask.jpg') }}" />  
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> 
        <link rel="stylesheet" href="{{ asset('assets/css/leaflet.css') }}" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.css"/>
        <style>
            .datepicker td, .datepicker th {
                width: 2.5rem;
                height: 2.5rem;
                font-size: 0.85rem;
            }

            .datepicker {
                margin-bottom: 3rem;
            }

            /*
            *
            * ==========================================
            * FOR DEMO PURPOSES
            * ==========================================
            *
            */
            body {
                min-height: 100vh;
                background-color: #fafafa;
            }

            .input-group {
                border-radius: 30rem;
            }

            input.form-control {
                border-radius: 30rem 0 0 30rem;
                border: none;
            }

            input.form-control:focus {
                box-shadow: none;
            }

            input.form-control::placeholder {
                font-style: italic;
            }

            .input-group-text {
                border-radius: 0 30rem 30rem 0;
                border: none;
            }

            .datepicker-dropdown {
                box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            }
        </style>
        @stack('stylesheets')
    </head>
<body>
    @if(Auth::user())
    <div class="container mt-2">
        <div class="row">
            <div class="border ml-3 title-sebaran-peta-risiko">
                <a class="nav-link text-dark font-italic" href="{{ route('logout.dashboard') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-forms').submit();">
                    <i class="fa fa-sign-out" aria-hidden="true"></i> {{ __('Logout') }}
                </a>
                <form id="logout-forms" action="{{ route('logout.dashboard') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </div> 
    @endif  
    <div class="card-body">
        @yield('content')
    </div>
    <script src="{{ asset('assets/js/jquery.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/leaflet.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script>
    $(function () {
        // INITIALIZE DATEPICKER PLUGIN
        $('.datepicker').datepicker({
            clearBtn: true,
            format: "dd/mm/yyyy"
        });


        // FOR DEMO PURPOSE
        $('#reservationDate').on('change', function () {
            var pickedDate = $('input').val();
            $('#pickedDate').html(pickedDate);
        });
    });
    </script>
    @stack('scripts')
</body>
