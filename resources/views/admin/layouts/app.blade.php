<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Robust admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template.">
    <meta name="keywords" content="admin template, robust admin template, dashboard template, flat admin template, responsive admin template, web app, crypto dashboard, bitcoin dashboard">
    <meta name="author" content="PIXINVENT">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Depo Online</title>
    <link rel="apple-touch-icon" href="{{ url('app-assets') }}/images/ico/IDMS.png">
    <link rel="shortcut icon" type="image/x-icon" href="{{ url('app-assets') }}/images/ico/IDMS.png">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i%7CMuli:300,400,500,700" rel="stylesheet">
    <!-- BEGIN VENDOR CSS-->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <!-- (Opsional) tema bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">

    <link rel="stylesheet" type="text/css" href="{{ url('app-assets') }}/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="{{ url('app-assets') }}/vendors/css/charts/jquery-jvectormap-2.0.3.css">
    <link rel="stylesheet" type="text/css" href="{{ url('app-assets') }}/vendors/css/charts/morris.css">
    <link rel="stylesheet" type="text/css" href="{{ url('app-assets') }}/vendors/css/extensions/unslider.css">
    <link rel="stylesheet" type="text/css" href="{{ url('app-assets') }}/vendors/css/weather-icons/climacons.min.css">
    <link rel="stylesheet" type="text/css" href="{{ url('app-assets') }}/vendors/css/forms/selects/selectivity-full.min.css">
    <!-- END VENDOR CSS-->
    <!-- BEGIN ROBUST CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('app-assets') }}/css/app.css">
    <!-- END ROBUST CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('app-assets') }}/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="{{ url('app-assets') }}/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="{{ url('app-assets') }}/css/plugins/forms/selectivity/selectivity.css">
    <link rel="stylesheet" type="text/css" href="{{ url('app-assets') }}/css/plugins/calendars/clndr.css">
    <link rel="stylesheet" type="text/css" href="{{ url('app-assets') }}/css/core/colors/palette-climacon.css">
    <link rel="stylesheet" type="text/css" href="{{ url('app-assets') }}/css/pages/users.css">
    <link rel="stylesheet" type="text/css" href="{{ url('app-assets') }}/vendors/css/forms/selects/select2.min.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('app-assets') }}/css/style.css">
    <!-- END Custom CSS-->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.datatables.net/1.11.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <style>
        .pagination .page-link {
            border: 0px;
            background-color: #E4F7FF;
        }

        .red-label {
            color: red;
        }

        .btn-round {
            border-radius: 50%;
            width: 90px;
            height: 90px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
        }

        /* Styling untuk block-container dan block-grid */
        .block-container {
            margin-bottom: 30px;
            overflow-x: auto;
            /* Menambahkan overflow horizontal untuk slide */
            white-space: nowrap;
            /* Menghindari breaking line dalam grid */
        }

        .block-grid {
            display: flex;
            flex-direction: column;
        }

        .block-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .block-cell {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
            cursor: pointer;
            transition: background-color 0.3s ease;
            position: relative;
            z-index: 10;
        }

        .block-cell:hover {
            background-color: #e0e0e0;
        }

        .cell-content {
            display: flex;
            flex-direction: column;
            /* Mengatur teks menjadi kolom */
            text-align: center;
            /* Mengatur teks agar rata tengah */
        }

        .cell-info {
            font-size: 12px;
            color: #333;
        }

        /* Styling untuk modal */
        .modal-content {
            padding: 20px;
        }

        .modal-body {
            max-height: 400px;
            overflow-y: auto;
        }

        /* Responsiveness */
        @media (max-width: 768px) {
            .block-row {
                flex-wrap: nowrap;
                /* Menghindari wrapping */
            }

            .block-cell {
                width: 40px;
                height: 40px;
                padding: 8px;
            }

            .cell-info {
                font-size: 8px;
            }
        }

        @media (max-width: 576px) {
            .block-row {
                flex-wrap: nowrap;
                /* Menghindari wrapping */
            }

            .block-cell {
                width: 30px;
                height: 30px;
                padding: 6px;
            }

            .cell-info {
                font-size: 6px;
            }
        }
    </style>
    @yield('head')
</head>

<body class="vertical-layout vertical-menu 2-columns   menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-col="2-columns">
    @yield('modal')
    <!-- fixed-top-->
    <nav class="header-navbar navbar-expand-md navbar navbar-with-menu fixed-top navbar-dark bg-gradient-x-dark">
        <div class="navbar-wrapper">
            <div class="navbar-header">
                <ul class="nav navbar-nav flex-row">
                    <li class="nav-item mobile-menu d-md-none mr-auto"><a
                            class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i
                                class="ft-menu font-large-1"></i></a></li>
                    <li class="nav-item"><a class="navbar-brand"
                            href="{{ url('app-assets') }}/html/ltr/vertical-menu-template/index.html"><img
                                class="brand-logo" alt="robust admin logo"
                                src="{{ url('app-assets') }}/images/logo/IDMS.png">
                            <h3 class="brand-text">Depo Admin</h3>
                        </a></li>
                    <li class="nav-item d-md-none"><a class="nav-link open-navbar-container" data-toggle="collapse"
                            data-target="#navbar-mobile"><i class="fa fa-ellipsis-v"></i></a></li>
                </ul>
            </div>
            <div class="navbar-container content">
                <div class="collapse navbar-collapse" id="navbar-mobile">
                    <ul class="nav navbar-nav mr-auto float-left">
                        <li class="nav-item d-none d-md-block"><a class="nav-link nav-menu-main menu-toggle hidden-xs"
                                href="#"><i class="ft-menu"> </i></a></li>
                    </ul>
                    <ul class="nav navbar-nav float-right">
                        <li class="dropdown dropdown-user nav-item"><a
                                class="dropdown-toggle nav-link dropdown-user-link" href="#"
                                data-toggle="dropdown">
                                <span class="avatar avatar-online"><img src="{{ url('app-assets') }}/images/portrait/small/avatar-s-1.png" alt="avatar"><i></i></span>
                                @if(auth()->check())
                                <span class="user-name">{{ auth()->user()->name }}</span>
                                @else
                                <span class="user-name">Guest</span>
                                @endif
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="user-profile.html"><i class="ft-user"></i> Edit Profile</a>
                                    <a class="dropdown-item" href="email-application.html"><i class="ft-mail"></i> MyInbox</a>
                                    <a class="dropdown-item" href="user-cards.html"><i class="ft-check-square"></i> Task</a>
                                    <a class="dropdown-item" href="chat-application.html"><i class="ft-message-square"></i> Chats</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>

                                </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <!-- ////////////////////////////////////////////////////////////////////////////-->


    <div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="main-menu-content overflow-auto">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                <?php
                if (auth()->check()) {
                    $rows = \DB::select("SELECT * FROM ACCESS_ROLE WHERE ROLE_ID='" . auth()->user()->role_id . "'");
                    $column = array_column($rows, 'menu_id');
                } else {
                    $rows = [];
                    $column = [];
                }
                ?>
                <?php /* @if(auth()->check() && auth()->user()->role === 'administrator') */ ?>
                <?php if (array_search("1", $column) !== false) { ?>
                    <li class="active nav-item"><a href="{{ url('/') }}"><i class="icon-home"></i><span
                                class="menu-title" data-i18n="nav.changelog.main">Dashboard</span><span
                                class="badge badge badge-pill badge-danger float-right mr-2">2.1</span></a></li>
                <?php }
                if (array_search("2", $column) !== false || array_search("3", $column) !== false) { ?>
                    <li class=" navigation-header"><span data-i18n="nav.category.layouts">Master Data</span><i
                            class="ft-more-horizontal ft-minus" data-toggle="tooltip" data-placement="right"
                            data-original-title="Layouts"></i></li>
                <?php }
                if (array_search("2", $column) !== false) { ?>
                    <li class=" nav-item"><a href="index.html"><i class="fa fa-database"></i><span class="menu-title"
                                data-i18n="nav.dash.main">Master Data</span><span
                                class="badge badge badge-info badge-pill float-right mr-2">6</span></a>
                        <ul class="menu-content">
                            <li><a class="menu-item" href="{{ url('admin/datamaster/datakapal') }}"
                                    data-i18n="nav.menu_levels.second_level">Data Kapal</a></li>
                            <li><a class="menu-item" href="{{ url('admin/datamaster/datagudang') }}"
                                    data-i18n="nav.menu_levels.second_level_child.main">Data Gudang</a></li>
                            <li><a class="menu-item" href="{{ url('admin/datamaster/datacontainer') }}"
                                    data-i18n="nav.menu_levels.second_level_child.main">Data Container</a></li>
                            <li><a class="menu-item" href="{{ url('admin/datamaster/datacustomer') }}"
                                    data-i18n="nav.menu_levels.second_level_child.main">Data Customer</a></li>
                            <li><a class="menu-item" href="{{ url('admin/datamaster/datatarif') }}"
                                    data-i18n="nav.menu_levels.second_level_child.main">Data Tarif</a></li>
                            <li><a class="menu-item" href="{{ url('admin/datamaster/datacedex') }}"
                                    data-i18n="nav.menu_levels.second_level_child.main">Data Cedex</a></li>
                            <li><a class="menu-item" href="{{ url('admin/datamaster/datayard') }}"
                                    data-i18n="nav.menu_levels.second_level_child.main">Data Yard</a></li>
                        </ul>
                    </li>
                <?php }
                if (array_search("3", $column) !== false) { ?>
                    <li class=" nav-item"><a href="#"><i class="icon-user"></i><span class="menu-title"
                                data-i18n="nav.templates.main">User Setting</span></a>
                        <ul class="menu-content">
                            <li><a class="menu-item" href="{{ route('auth.userlist') }}"
                                    data-i18n="nav.menu_levels.second_level">User list</a></li>
                            <li><a class="menu-item" href="{{ route('auth.change')}}"
                                    data-i18n="nav.menu_levels.second_level">Change password</a></li>
                            <li><a class="menu-item" href="{{ route('register') }}"
                                    data-i18n="nav.menu_levels.second_level">Create New User</a></li>
                        </ul>
                    </li>
                <?php }
                if (array_search("4", $column) !== false || array_search("5", $column) !== false) { ?>
                    <!-- Menu OPERATION -->
                    <li class=" navigation-header"><span data-i18n="nav.category.general">ADM.OPERATION</span><i
                            class="ft-more-horizontal ft-minus" data-toggle="tooltip" data-placement="right"
                            data-original-title="General"></i>
                    </li>
                <?php }
                if (array_search("4", $column) !== false) { ?>
                    <li class="nav-item"><a href="{{ url('admin/annimport') }}"><i class="fa fa-bullhorn"></i><span
                                class="menu-title" data-i18n="nav.changelog.main">Ann Import</span></a>
                    </li>
                    <li class="nav-item"><a href="{{ url('admin/movementinout') }}"><i class="fa fa-random"></i><span
                                class="menu-title" data-i18n="nav.changelog.main">Movements In/Out</span></a>
                    </li>
                <?php }
                if (array_search("5", $column) !== false) { ?>
                    <li class="nav-item"><a href="{{ url('admin/workorder') }}"><i class="fa fa-desktop"></i><span
                                class="menu-title" data-i18n="nav.changelog.main">Work Order</span></a>
                    </li>
                    <li class="nav-item"><a href="{{ url('admin/monitoringyard') }}"><i class="fa fa-th"></i><span
                                class="menu-title" data-i18n="nav.changelog.main">Monitoring Yard</span></a>
                    </li>
                <?php }
                if (array_search("6", $column) !== false) { ?>
                    <li class=" navigation-header"><span data-i18n="nav.category.general">OPERATION</span><i
                            class="ft-more-horizontal ft-minus" data-toggle="tooltip" data-placement="right"
                            data-original-title="General"></i>
                    </li>
                    <li class="nav-item"><a href="{{ url('admin/eor') }}"><i class="fa fa-cogs"></i><span
                                class="menu-title" data-i18n="nav.changelog.main">EOR</span></a>
                    </li>


                    <!-- Menu OPERATION -->
                <?php }
                if (array_search("7", $column) !== false) { ?>
                    <!-- Menu IN -->
                    <li class=" navigation-header"><span data-i18n="nav.category.general">IN</span><i
                            class="ft-more-horizontal ft-minus" data-toggle="tooltip" data-placement="right"
                            data-original-title="General"></i>
                    </li>
                    <li class="nav-item"><a href="{{ url('admin/gatein') }}"><i class="fa fa-mail-forward"></i><span
                                class="menu-title" data-i18n="nav.changelog.main">Gate IN</span></a>
                    </li>
                    <li class="nav-item"><a href="{{ url('admin/surveyin') }}"><i class="fa fa-child"></i><span
                                class="menu-title" data-i18n="nav.changelog.main">Survey IN</span></a>
                    </li>
                    <!-- Menu IN -->
                <?php }
                if (array_search("8", $column) !== false) { ?>
                    <!-- Menu OUT -->
                    <li class=" navigation-header"><span data-i18n="nav.category.general">OUT</span><i
                            class="ft-more-horizontal ft-minus" data-toggle="tooltip" data-placement="right"
                            data-original-title="General"></i>
                    </li>
                    <li class="nav-item"><a href="{{ url('admin/surveyout') }}"><i class="fa fa-child"></i><span
                                class="menu-title" data-i18n="nav.changelog.main">Survey OUT</span></a>
                    </li>
                    <li class="nav-item"><a href="{{ url('admin/gateout') }}"><i class="fa fa-mail-reply"></i><span
                                class="menu-title" data-i18n="nav.changelog.main">Gate OUT</span></a>
                    </li>
                    <!-- Menu OUT -->
                <?php }
                if (array_search("9", $column) !== false) { ?>
                    <!-- Menu FINANCE -->
                    <li class=" navigation-header"><span data-i18n="nav.category.general">Finance</span><i
                            class="ft-more-horizontal ft-minus" data-toggle="tooltip" data-placement="right"
                            data-original-title="General"></i>
                    </li>
                    <li class="nav-item"><a href="{{ url('admin/billing') }}"><i class="fa fa-tags"></i><span
                                class="menu-title" data-i18n="nav.changelog.main">Billing</span></a></li>
                    <li class="nav-item"><a href="{{ url('admin/payment') }}"><i class="fa fa-money"></i><span
                                class="menu-title" data-i18n="nav.changelog.main">Payment</span></a></li>
                    <!-- Menu FINANCE -->
                <?php }
                if (array_search("10", $column) !== false) { ?>
                    <!-- Menu REPORT -->
                    <li class=" navigation-header"><span data-i18n="nav.category.general">Report</span><i
                            class="ft-more-horizontal ft-minus" data-toggle="tooltip" data-placement="right"
                            data-original-title="General"></i>
                    </li>
                    <li class="nav-item"><a href="{{ url('admin/reportin') }}"><i class="fa fa-file"></i><span
                                class="menu-title" data-i18n="nav.changelog.main">Report Stock</span></a></li>
                    <!-- <li class="nav-item"><a href="{{ url('admin/reportcustomer') }}"><i class="fa fa-file"></i><span
                            class="menu-title" data-i18n="nav.changelog.main">Report by Customer</span></a></li> -->
                    <!-- Menu REPORT -->
                <?php } ?>


                <?php /* @endif */ ?>
            </ul>
        </div>
    </div>

    @yield('content')

    <footer class="z-n1 footer footer-light navbar-border fixed-bottom">
        <p class="clearfix blue-grey lighten-2 text-sm-center mb-0 px-2"><span
                class="float-md-left d-block d-md-inline-block">Copyright &copy; 2024 <a
                    class="text-bold-800 grey darken-2" target="_blank">PMO & IT </a>, All rights reserved.
            </span><span class="float-md-right d-block d-md-inline-blockd-none d-lg-block">Hand-crafted & Made with <i
                    class="ft-heart pink"></i></span></p>
    </footer>

    <script>
        function showConfirmation() {
            $('#confirmationModal').modal('show');
        }

        function submitForm() {
            $('#deleteForm').submit();
        }
    </script>



    <!-- BEGIN VENDOR JS-->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="{{ url('app-assets') }}/vendors/js/vendors.min.js"></script>

    <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script src="{{ url('app-assets') }}/vendors/js/forms/select/selectivity-full.min.js"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->
    <script src="{{ url('app-assets') }}/vendors/js/extensions/jquery.knob.min.js"></script>
    <script src="{{ url('app-assets') }}/vendors/js/charts/raphael-min.js"></script>
    <script src="{{ url('app-assets') }}/vendors/js/charts/morris.min.js"></script>
    <script src="{{ url('app-assets') }}/vendors/js/charts/chartist.min.js"></script>
    <script src="{{ url('app-assets') }}/vendors/js/charts/chartist-plugin-tooltip.js"></script>
    <script src="{{ url('app-assets') }}/vendors/js/charts/chart.min.js"></script>
    <script src="{{ url('app-assets') }}/vendors/js/charts/jquery.sparkline.min.js"></script>
    <script src="{{ url('app-assets') }}/vendors/js/extensions/moment.min.js"></script>
    <script src="{{ url('app-assets') }}/vendors/js/extensions/underscore-min.js"></script>
    <script src="{{ url('app-assets') }}/vendors/js/extensions/clndr.min.js"></script>
    <script src="{{ url('app-assets') }}/vendors/js/extensions/unslider-min.js"></script>
    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN ROBUST JS-->
    <script src="{{ url('app-assets') }}/js/core/app-menu.js"></script>
    <script src="{{ url('app-assets') }}/js/core/app.js"></script>
    <script src="{{ url('app-assets') }}/vendors/js/forms/select/select2.full.min.js"></script>
    <!-- END ROBUST JS-->
    <!-- BEGIN PAGE LEVEL JS-->
    <script src="{{ url('app-assets') }}/js/scripts/pages/dashboard-project.js"></script>
    <script src="{{ url('app-assets') }}/js/scripts/forms/select/form-selectivity.js"></script>
    <script src="{{ url('app-assets') }}/js/scripts/forms/select/form-select2.js"></script>
    <!-- END PAGE LEVEL JS-->



    @yield('script')
</body>

</html>
