<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('img/icon.png') }}" />  
        <title>ADMINISTRATOR PROKES</title>
        <link href="{{ asset('asset_admin/css/styles.css') }}" rel="stylesheet" />
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-light bg-light border-bottom">
            <a class="navbar-brand" href="{{ route('dashboard') }}">PROTOKOL KESEHATAN</a><button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fa fa-bars"></i></button
            ><!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0">
                <div class="input-group">
                    <!-- <input class="form-control" type="text" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2" /> -->
                    <!-- <div class="input-group-append">
                        <button class="btn btn-primary" type="button"><i class="fas fa-search"></i></button>
                    </div> -->
                </div>
            </form>
            <!-- Navbar-->
            <ul class="navbar-nav ml-auto ml-md-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-user" aria-hidden="true"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                        <!-- <a class="dropdown-item" href="#">Settings</a><a class="dropdown-item" href="#">Activity Log</a> -->
                        <!-- <div class="dropdown-divider"></div> -->
                        <a class="nav-link text-dark font-italic" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fa fa-sign-out"></i> {{ __('Logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-light border-right" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">ANALYTICS</div>
                            <a href="{{ route('dashboard') }}" class="nav-link text-dark font-italic">
                                <i class="fa fa-tachometer" aria-hidden="true"></i>&nbsp;
                                Dashboard
                            </a>
                            <div class="sb-sidenav-menu-heading">DATA</div>
                            <!-- <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts"
                                ><div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                                Kelola Data
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div
                            ></a> -->
                            <!-- <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav"> -->
                                    @if(Auth::user()->role == 'super admin' || Auth::user()->role == 'Admin')
                                    <a href="{{ route('user.index') }}" class="nav-link {{ request()->route()->getName() == 'user.index' ? ' active' : '' }} 'text-dark font-italic">
                                            <i class="fa fa-user" aria-hidden="true"></i>&nbsp; User Management
                                    </a>
                                    @endif
                                    <a href="{{ route('prokes.index') }}" class="nav-link {{ request()->route()->getName() == 'prokes.index' ? ' active' : '' }} 'text-dark font-italic">
                                        <i class="fa fa-table" aria-hidden="true"></i>&nbsp; Prokes Individu
                                    </a>
                                    <a href="{{ route('institusi.index') }}" class="nav-link {{ request()->route()->getName() == 'institusi.index' ? ' active' : '' }} 'text-dark font-italic">
                                        <i class="fa fa-table" aria-hidden="true"></i>&nbsp; Prokes Institusi
                                    </a>
                                    @if(Auth::user()->role == 'super admin')
                                    <a href="{{ route('report.index') }}" class="nav-link {{ request()->route()->getName() == 'report.index' ? ' active' : '' }} 'text-dark font-italic">
                                        <i class="fa fa-table" aria-hidden="true"></i>&nbsp; Laporan
                                    </a>
                                    @endif
                                <!-- </nav>
                            </div> -->
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        {{ Auth::user()->name }}
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid mt-4">
                        @yield('content')
                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Diskominfo Ciamis 2021</div>
                            <!-- <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div> -->
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="{{ asset('asset_admin/js/sweetalert2.js') }}"></script>
        <script src="{{ asset('asset_admin/js/jquery-3.4.1.min.js') }}" crossorigin="anonymous"></script>
        <script src="{{ asset('asset_admin/js/bootstrap.bundle.min.js') }}" crossorigin="anonymous"></script>
        <script src="{{ asset('asset_admin/js/scripts.js') }}"></script>
        <!-- <script src="{{ asset('asset_admin/js/Chart.min.js') }}" crossorigin="anonymous"></script> -->
        <script src="{{ asset('asset_admin/assets/demo/chart-area-demo.js') }}"></script>
        <script src="{{ asset('asset_admin/assets/demo/chart-bar-demo.js') }}"></script>
        <script src="{{ asset('asset_admin/js/jquery.dataTables.min.js') }}" crossorigin="anonymous"></script>
        <script src="{{ asset('asset_admin/js/dataTables.bootstrap4.min.js') }}" crossorigin="anonymous"></script>
        <script src="{{ asset('asset_admin/js/bootstrap.min.js') }}" crossorigin="anonymous"></script> -->
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <!-- <script src="{{ asset('admin/assets/demo/datatables-demo.js') }}"></script> -->
        @stack('scripts')
    </body>
</html>
