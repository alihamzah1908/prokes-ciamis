<!DOCTYPE html>
<html lang="en">

<head>
    <title>Aplikasi Prokes (Protokol Kesehatan)</title>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> 
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/mask.jpg') }}" />   
    <link rel="stylesheet" href="{{ asset('assets/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
      ul.breadcrumb {
        padding: 10px 16px;
        list-style: none;
        background-color: #eee;
      }
      ul.breadcrumb li {
        display: inline;
        font-size: 18px;
      }
      ul.breadcrumb li+li:before {
        padding: 8px;
        color: black;
        content: "/\00a0";
      }
      ul.breadcrumb li a {
        color: #0275d8;
        text-decoration: none;
      }
      ul.breadcrumb li a:hover {
        color: #01447e;
        text-decoration: underline;
      }
    </style>
    @stack('stylesheets')
</head>

<body>
    <!-- Vertical navbar -->
<div class="vertical-nav bg-white" id="sidebar">
  <div class="py-4 px-3 mb-4 bg-light">
    <div class="media d-flex align-items-center">
      <div class="media-body">
        <h4 class="m-0">{{ Auth::user()->name }}</h4>
        <p class="font-weight-light text-muted mb-0">{{ Auth::user()->role }}</p>
      </div>
    </div>
  </div>

  <p class="text-gray font-weight-bold text-uppercase px-3 small pb-4 mb-0">Menu</p>

  <ul class="nav flex-column bg-white mb-0">
    <li class="nav-item">
        <a href="{{ URL::to('/admin') }}" class="nav-link text-dark font-italic">
            <i class="fa fa-tachometer" aria-hidden="true"></i>
            Home
        </a>
    </li>
    <li class="nav-item">
      @if(Auth::user()->role == 'super admin' || Auth::user()->role == 'Admin')
      <a href="{{ route('user.index') }}" class="nav-link text-dark font-italic">
            <i class="fa fa-user" aria-hidden="true"></i>
            User Management
      </a>
      @endif
    </li>
    <li class="nav-item">
      <a href="{{ route('prokes.index') }}" class="nav-link text-dark font-italic">
          <i class="fa fa-table" aria-hidden="true"></i>
            Prokes Individu
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('institusi.index') }}" class="nav-link text-dark font-italic">
        <i class="fa fa-table" aria-hidden="true"></i>
            Prokes Institusi
      </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-dark font-italic" href="#"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa fa-sign-out" aria-hidden="true"></i> {{ __('Logout') }}
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </li>
  </ul>
</div>

<div class="page-content p-3" id="content">
<!-- <button id="sidebarCollapse" type="button" class="btn btn-light bg-white rounded-pill shadow-sm px-4 mb-4"><i class="fa fa-bars mr-2"></i><small class="text-uppercase font-weight-bold">Toggle</small></button> -->
<!-- End vertical navbar -->
    @yield('content')
</div>

<script src="{{ asset('assets/js/jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/propper.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
@stack('scripts')
</body>

</html>
