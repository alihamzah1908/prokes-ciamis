<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/docs/4.0/assets/img/favicons/favicon.ico">

    <title>PROTOKOL KESEHATAN</title>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/mask.jpg') }}" />  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> 
    <link rel="stylesheet" href="{{ asset('assets/css/leaflet.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.css"/>
    <link href="https://getbootstrap.com/docs/4.0/examples/navbar-fixed/navbar-top-fixed.css" rel="stylesheet">
    
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
                background-color: #ffffff;
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
    <nav class="navbar navbar-expand-lg navbar-light fixed-top bg-light mb-4" style='border-bottom: 3px solid #b300b3;'>
      <!-- <div class="row col-lg-12"> -->
          <div class="col-md-6 d-flex justify-content-start">
              <a class="navbar-brand" href="#">KEPATUHAN PROTOKOL KESEHATAN</a>
          </div>
          <div class="col-md-7 d-flex justify-content-end">
              <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"></span>
              </button>
              <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav mr-auto">
                    @if(Auth::user()->kode_kecamatan == '')
                    <li class="border-right nav-item {{ request()->route()->getName() == 'prokes.individu' ? ' active' : '' }}">
                        <a class="nav-link" href="{{ route('prokes.individu') }}"><strong>PROKES INDIVIDU</strong></a>
                    </li>
                    <li class="border-right nav-item {{ request()->route()->getName() == 'prokes.institusi' ? ' active' : '' }}">
                        <a class="nav-link" href="{{ route('prokes.institusi') }}"><strong>PROKES INSTITUSI</strong> <span class="sr-only"></span></a>
                    </li>
                    <li class="nav-item border-right {{ request()->route()->getName() == 'dokumentasi' ? ' active' : '' }}">
                        <a class="nav-link" href="{{ route('dokumentasi') }}"><strong>DOKUMENTASI PEMANTAUAN </strong><span class="sr-only"></span></a>
                    </li>
                    @else 
                    <li class="border-right nav-item {{ request()->route()->getName() == 'prokes.individu' ? ' active' : '' }}">
                        <a class="nav-link" href="{{ route('individu.desa') }}?kecamatan={{request()->kecamatan}}&periode_kasus={{request()->periode_kasus}}&latitude={{request()->latitude}}&longitude={{request()->longitude}}"><strong>PROKES INDIVIDU</strong></a>
                    </li>
                    <li class="border-right nav-item {{ request()->route()->getName() == 'prokes.institusi' ? ' active' : '' }}">
                        <a class="nav-link" href="{{ route('institusi.desa') }}?kecamatan={{request()->kecamatan}}&periode_kasus={{request()->periode_kasus}}&latitude={{request()->latitude}}&longitude={{request()->longitude}}"><strong>PROKES INSTITUSI </strong><span class="sr-only"></span></a>
                    </li>
                    <li class="nav-item border-right {{ request()->route()->getName() == 'dokumentasi' ? ' active' : '' }}">
                        <a class="nav-link" href="{{ route('dokumentasi') }}?kecamatan={{request()->kecamatan}}&periode_kasus={{request()->periode_kasus}}&latitude={{request()->latitude}}&longitude={{request()->longitude}}"><strong>DOKUMENTASI PEMANTAUAN </strong><span class="sr-only"></span></a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a href="{{ route('logout.dashboard') }}" class="nav-link"
                            onclick="event.preventDefault(); document.getElementById('logout-forms').submit();">
                             <strong>{{ __('KELUAR') }}</strong>
                        </a>
                        <form id="logout-forms" action="{{ route('logout.dashboard') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
              </div>
          </div>
      <!-- </div> -->
    </nav>
    <main role="main ml-3" style="margin-left: 30px; margin-right: 30px;">
        @yield('content')
    </main>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
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
            format: 'yyyy-mm-dd'
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
</html>
