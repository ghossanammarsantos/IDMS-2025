@extends('admin.layouts.app', [
    'activePage' => 'dashboard',
])

@section('content')
<div class="app-content content">
      <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body"><!-- project stats -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="row">
                            <div class="col-lg-6 col-md-12 col-sm-12 border-right-blue-grey border-right-lighten-5">
                                <div class="p-1 text-center">
                                    <div>
                                        <h3 class="font-large-1 grey darken-1 text-bold-400">Rp. {{ number_format($totals['total'] ?? 0, 2, ',', ',') }}</h3>
                                        <span class="font-medium-3 grey darken-1">Total Profit</span>
                                    </div>
                                    <div class="card-content overflow-hidden">
                                        <div id="morris-comments" class="height-75"></div>
                                        <ul class="list-inline clearfix mb-0">
                                            <li class="pl-2 pr-2">
                                                <h3 class="success">Rp. {{ number_format($totals['LOLO'] ?? 0, 2, ',', ',') }}</h3>
                                                <span class="font-medium-3 grey darken-1"><i></i>LOLO</span>
                                            </li>
                                            <li class="pl-2 border-right-grey border-left-grey border-right-lighten-2 border-left-lighten-2 pr-2">
                                                <h3 class="success text-center">Rp. {{ number_format($totals['WASH'] ?? 0, 2, ',', ',') }}</h3>
                                                <span class="font-medium-3 grey darken-1"><i></i>Washing</span>
                                            </li>
                                            <li class="pl-2">
                                                <h3 class="success">Rp. {{ number_format($totals['STORAGE'] ?? 0, 2, ',', ',') }}</h3>
                                                <span class="font-medium-3 grey darken-1"><i></i>Storage</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-12 col-sm-12 border-right-blue-grey border-right-lighten-5">
                                <div class="p-1 text-center">
                                    <div>
                                        <h3 class="font-large-1 grey darken-1 text-bold-400">{{ $dataTotalToday }}</h3>
                                        <span class="font-medium-3 grey darken-1">Kegiatan / Hari {{ $currentDay }}</span>
                                    </div>
                                    <div class="card-content overflow-hidden">
                                        <div id="morris-likes" class="height-75"></div>
                                        <div class="row justify-content-between">
                                            <div class="col-6 border-right-grey border-right-lighten-2 pr-2">
                                                <h3 class="primary text-bold-400">{{ $dataIn }}</h3>
                                                <span class="font-medium-3 grey darken-1"><i class="ft-chevron-up primary"></i> IN</span>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <h3 class="primary text-bold-400">{{ $dataIn20ft }}</h3>
                                                        <span class="font-medium-3 grey darken-1"><i class="ft-chevron-up primary"></i> 20ft</span>
                                                    </div>
                                                    <div class="col-6">
                                                        <h3 class="primary text-bold-400">{{ $dataIn40ft }}</h3>
                                                        <span class="font-medium-3 grey darken-1"><i class="ft-chevron-up primary"></i> 40ft</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <h3 class="primary text-bold-400">{{ $dataOut }}</h3>
                                                <span class="font-medium-3 grey darken-1"><i class="ft-chevron-down primary"></i> OUT</span>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <h3 class="primary text-bold-400">{{ $dataOut20ft }}</h3>
                                                        <span class="font-medium-3 grey darken-1"><i class="ft-chevron-down primary"></i> 20ft</span>
                                                    </div>
                                                    <div class="col-6">
                                                        <h3 class="primary text-bold-400">{{ $dataOut40ft }}</h3>
                                                        <span class="font-medium-3 grey darken-1"><i class="ft-chevron-down primary"></i> 40ft</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-12 col-sm-12">
                                <div class="p-1 text-center">
                                    <div>
                                        <h3 class="font-large-1 grey darken-1 text-bold-400">{{ $dataTotalMonth }}</h3>
                                        <span class="font-medium-3 grey darken-1">Kegiatan / Bulan {{ $currentMonth }} {{ $currentYear }}</span>
                                    </div>
                                    <div class="card-content overflow-hidden">
                                        <div id="morris-views" class="height-75"></div>
                                        <ul class="list-inline clearfix mb-0">
                                            <li class="border-right-grey border-right-lighten-2 pr-2">
                                                <h3 class="danger text-bold-400">{{ $dataInMonth }}</h3>
                                                <span class="font-medium-3 grey darken-1"><i class="ft-chevron-up danger"></i> IN</span>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <h3 class="danger text-bold-400">{{ $dataInMonth20ft }}</h3>
                                                        <span class="font-medium-3 grey darken-1"><i class="ft-chevron-up danger"></i> 20ft</span>
                                                    </div>
                                                    <div class="col-6">
                                                        <h3 class="danger text-bold-400">{{ $dataInMonth40ft }}</h3>
                                                        <span class="font-medium-3 grey darken-1"><i class="ft-chevron-up danger"></i> 40ft</span>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="pl-2">
                                                <h3 class="danger text-bold-400">{{ $dataOutMonth }}</h3>
                                                <span class="font-medium-3 grey darken-1"><i class="ft-chevron-down danger"></i> OUT</span>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <h3 class="danger text-bold-400">{{ $dataOutMonth20ft }}</h3>
                                                        <span class="font-medium-3 grey darken-1"><i class="ft-chevron-down danger"></i> 20ft</span>
                                                    </div>
                                                    <div class="col-6">
                                                        <h3 class="danger text-bold-400">{{ $dataOutMonth40ft }}</h3>
                                                        <span class="font-medium-3 grey darken-1"><i class="ft-chevron-down danger"></i> 40ft</span>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ project stats -->
        <!--/ project charts -->
        <div class="row match-height">
            <div class="col-xl-6 col-lg-12">
                <div class="card card-inverse bg-white">
                    <div class="card-content">
                        <div class="card-body sales-growth-chart">
                            <div class="chart-title mb-1 text-center">
                                <span class="dark">2024 Container Out</span>
                            </div>
                            <canvas id="monthly-out" class="height-250" style="width: 100%; height: 250px;"></canvas>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <div class="chart-stats mt-1 dark">
                            <a href="#" class="btn bg-dark bg-darken-3 mr-1 white">Details <i class="ft-bar-chart"></i></a> for the last year.
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-12">
                <div class="card card-inverse bg-white">
                    <div class="card-content">
                        <div class="card-body sales-growth-chart">
                            <div class="chart-title mb-1 text-center">
                                <span class="dark">2024 Container IN</span>
                            </div>
                            <canvas id="monthly-in" class="height-250" style="width: 100%; height: 250px;"></canvas>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <div class="chart-stats mt-1 dark">
                            <a href="#" class="btn bg-dark bg-darken-3 mr-1 white">Details <i class="ft-bar-chart"></i></a> for the last year.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row match-height">
        <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-content">
                        <ul class="list-inline text-center pt-2 m-0">
                            <h2>Repairing</h2>
                            <li class="mr-1">
                                <h6><i class="ft-circle warning"></i> <span class="grey darken-1">Remaining</span></h6>
                            </li>
                            <li class="mr-1">
                                <h6><i class="ft-circle success"></i> <span class="grey darken-1">Completed</span></h6>
                            </li>
                        </ul>
                        <div class="chartjs height-250">
                            <canvas id="line-stacked-area" height="250"></canvas>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-3 text-center">
                                <span class="text-muted">Total Proyek Perbaikan</span>
                                <h2 class="block font-weight-normal">{{ $totalProjects }}</h2>
                                <div class="progress mt-2" style="height: 7px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($totalProjects > 0) ? ($completedTasks / $totalProjects) * 100 : 0 }}%" aria-valuenow="{{ $completedTasks }}" aria-valuemin="0" aria-valuemax="{{ $totalProjects }}"></div>
                                </div>
                            </div>
                            <div class="col-3 text-center">
                                <span class="text-muted">Total Waktu Pekerjaan</span>
                                <h2 class="block font-weight-normal">{{ $totalTasks }}</h2>
                                <div class="progress mt-2" style="height: 7px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ ($totalTasks > 0) ? ($remainingTasks / $totalTasks) * 100 : 0 }}%" aria-valuenow="{{ $remainingTasks }}" aria-valuemin="0" aria-valuemax="{{ $totalTasks }}"></div>
                                </div>
                            </div>
                            <div class="col-3 text-center">
                                <span class="text-muted">Pekerjaan Selesai</span>
                                <h2 class="block font-weight-normal">{{ $completedTasks }}</h2>
                                <div class="progress mt-2" style="height: 7px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($completedTasks > 0) ? ($completedTasks / $totalProjects) * 100 : 0 }}%" aria-valuenow="{{ $completedTasks }}" aria-valuemin="0" aria-valuemax="{{ $totalProjects }}"></div>
                                </div>
                            </div>
                            <div class="col-3 text-center">
                                <span class="text-muted">Total Biaya</span>
                                <h2 class="block font-weight-normal">{{ number_format($totalRevenue, 2) }}</h2>
                                <div class="progress mt-2" style="height: 7px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
        </div>
        <!--/ project charts -->

        <!--project health, featured & chart-->
        <!-- <div class="row">
            <div class="col-xl-4 col-lg-6 col-md-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body text-center">
                            <div class="card-header mb-2">
                                <span class="success darken-1">Total Budget</span>
                                <h3 class="font-large-2 grey darken-1 text-bold-200">$24,879</h3>
                            </div>
                            <div class="card-content">
                                <input type="text" value="75" class="knob hide-value responsive angle-offset" data-angleOffset="0" data-thickness=".15" data-linecap="round" data-width="150" data-height="150" data-inputColor="#e1e1e1" data-readOnly="true" data-fgColor="#37BC9B" data-knob-icon="ft-trending-up">
                                <ul class="list-inline clearfix mt-2 mb-0">
                                    <li class="border-right-grey border-right-lighten-2 pr-2">
                                        <h2 class="grey darken-1 text-bold-400">75%</h2>
                                        <span class="success">Completed</span>
                                    </li>
                                    <li class="pl-2">
                                        <h2 class="grey darken-1 text-bold-400">25%</h2>
                                        <span class="danger">Remaining</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-12">
                <div class="card white bg-warning text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <img src="../../../app-assets/images/elements/04.png" alt="element 05" height="170" class="mb-1">
                            <h4 class="card-title white">Storage Device</h4>
                            <p class="card-text white">945 items</p>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-content">
                        <div class="media align-items-stretch">
                            <div class="p-2 bg-warning text-white media-body text-left rounded-left">
                                <h5 class="text-white">New Orders</h5>
                                <h5 class="text-white text-bold-400 mb-0">4,65,879</h5>
                            </div>
                            <div class="p-2 text-center bg-warning bg-darken-2 rounded-right">
                                <i class="icon-camera font-large-2 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-12 col-md-12">
                <div class="card card-inverse bg-info">
                    <div class="card-content">
                        <div class="position-relative">
                            <div class="chart-title position-absolute mt-2 ml-2 white">
                                <h1 class="font-large-2 text-bold-200 white">84%</h1>
                                <span>Employees Satisfied</span>
                            </div>
                            <canvas id="emp-satisfaction" class="height-400 block"></canvas>
                            <div class="chart-stats position-absolute position-bottom-0 position-right-0 mb-3 mr-3 white">
                                <a href="#" class="btn bg-info bg-darken-3 mr-1 white">Statistics <i class="ft-bar-chart"></i></a> for the last year.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- projects table with monthly chart -->
        <!-- <div class="row">
            <div class="col-xl-8 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Ongoing Projects</h4>
                        <a class="heading-elements-toggle"><i class="ft-more-horizontal font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <p class="m-0">Total ongoing projects 6<span class="float-right"><a href="project-summary.html" target="_blank">Project Summary <i class="ft-arrow-right"></i></a></span></p>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Project</th>
                                        <th>Owner</th>
                                        <th>Priority</th>
                                        <th>Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-truncate">ReactJS App</td>
                                        <td class="text-truncate">
                                            <span class="avatar avatar-xs"><img src="../../../app-assets/images/portrait/small/avatar-s-4.png" alt="avatar"></span> <span>Sarah W.</span>
                                        </td>
                                        <td class="text-truncate"><span class="tag tag-success">Low</span></td>
                                        <td class="valign-middle">
                                            <div class="progress m-0" style="height: 7px;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: 88%" aria-valuenow="88" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-truncate">Fitness App</td>
                                        <td class="text-truncate">
                                            <span class="avatar avatar-xs"><img src="../../../app-assets/images/portrait/small/avatar-s-5.png" alt="avatar"></span> <span>Edward C.</span>
                                        </td>
                                        <td class="text-truncate"><span class="tag tag-warning">Medium</span></td>
                                        <td class="valign-middle">
                                            <div class="progress m-0" style="height: 7px;">
                                                <div class="progress-bar bg-warning" role="progressbar" style="width: 55%" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-truncate">SOU plugin</td>
                                        <td class="text-truncate">
                                            <span class="avatar avatar-xs"><img src="../../../app-assets/images/portrait/small/avatar-s-6.png" alt="avatar"></span> <span>Carol E.</span>
                                        </td>
                                        <td class="text-truncate"><span class="tag tag-danger">Critical</span></td>
                                        <td class="valign-middle">
                                            <div class="progress m-0" style="height: 7px;">
                                                <div class="progress-bar bg-danger" role="progressbar" style="width: 55%" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-truncate">Android App</td>
                                        <td class="text-truncate">
                                            <span class="avatar avatar-xs"><img src="../../../app-assets/images/portrait/small/avatar-s-7.png" alt="avatar"></span> <span>Gregory L.</span>
                                        </td>
                                        <td class="text-truncate"><span class="tag tag-success">Low</span></td>
                                        <td class="valign-middle">
                                            <div class="progress m-0" style="height: 7px;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: 95%" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-truncate">ABC Inc. UI/UX</td>
                                        <td class="text-truncate">
                                            <span class="avatar avatar-xs"><img src="../../../app-assets/images/portrait/small/avatar-s-8.png" alt="avatar"></span> <span>Susan S.</span>
                                        </td>
                                        <td class="text-truncate"><span class="tag tag-warning">Medium</span></td>
                                        <td class="valign-middle">
                                            <div class="progress m-0" style="height: 7px;">
                                                <div class="progress-bar bg-warning" role="progressbar" style="width: 45%" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-truncate">Product UI</td>
                                        <td class="text-truncate">
                                            <span class="avatar avatar-xs"><img src="../../../app-assets/images/portrait/small/avatar-s-9.png" alt="avatar"></span> <span>Walter K.</span>
                                        </td>
                                        <td class="text-truncate"><span class="tag tag-danger">Critical</span></td>
                                        <td class="valign-middle">
                                            <div class="progress m-0" style="height: 7px;">
                                                <div class="progress-bar bg-danger" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-12">
                <div class="card">
                    <div class="card-content bg-success">
                        <div class="card-body sales-growth-chart">
                            <div id="completed-project" class="height-250"></div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="chart-title">
                            <span class="text-muted">Total completed project and earning.</span>
                        </div>
                        <ul class="list-inline text-center clearfix mt-2 mb-0">
                            <li class="border-right-grey border-right-lighten-2 pr-1"><span class="text-muted">Completed Projects</span>
                                <h3 class="block">250</h3></li>
                            <li class="pl-2"><span class="text-muted">Total Earnings</span>
                                <h3 class="block">64.54 M</h3></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div> -->
        <!--/ projects table with monthly chart -->


        <!-- Recent invoice with Statistics -->
        <!-- <div class="row match-height">
            <div class="col-xl-4 col-lg-12">
                <div class="card">
                    <div class="card-header no-border-bottom">
                        <h4 class="card-title">Invoices Stats</h4>
                        <a class="heading-elements-toggle"><i class="ft-more-horizontal font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <ul class="list-inline text-right pr-2 mb-0">
                                <li>
                                    <h6><i class="ft-circle grey lighten-1"></i> Paid</h6>
                                </li>
                                <li>
                                    <h6><i class="ft-circle danger"></i> Unpaid</h6>
                                </li>
                            </ul>
                        </div>
                        <div id="project-invoices" class="height-250"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-8 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Recent Invoices</h4>
                        <a class="heading-elements-toggle"><i class="ft-more-horizontal font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <p>Total paid invoices 240, unpaid 150. <span class="float-right"><a href="project-summary.html" target="_blank">Invoice Summary <i class="ft-arrow-right"></i></a></span></p>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Invoice#</th>
                                        <th>Customer Name</th>
                                        <th>Status</th>
                                        <th>Due</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-truncate"><a href="#">INV-001001</a></td>
                                        <td class="text-truncate">Elizabeth W.</td>
                                        <td class="text-truncate"><span class="tag tag-default tag-success">Paid</span></td>
                                        <td class="text-truncate">10/05/2016</td>
                                        <td class="text-truncate">$ 1200.00</td>
                                    </tr>
                                    <tr>
                                        <td class="text-truncate"><a href="#">INV-001012</a></td>
                                        <td class="text-truncate">Andrew D.</td>
                                        <td class="text-truncate"><span class="tag tag-default tag-success">Paid</span></td>
                                        <td class="text-truncate">20/07/2016</td>
                                        <td class="text-truncate">$ 152.00</td>
                                    </tr>
                                    <tr>
                                        <td class="text-truncate"><a href="#">INV-001401</a></td>
                                        <td class="text-truncate">Megan S.</td>
                                        <td class="text-truncate"><span class="tag tag-default tag-success">Paid</span></td>
                                        <td class="text-truncate">16/11/2016</td>
                                        <td class="text-truncate">$ 1450.00</td>
                                    </tr>
                                    <tr>
                                        <td class="text-truncate"><a href="#">INV-01112</a></td>
                                        <td class="text-truncate">Doris R.</td>
                                        <td class="text-truncate"><span class="tag tag-default tag-warning">Overdue</span></td>
                                        <td class="text-truncate">11/12/2016</td>
                                        <td class="text-truncate">$ 5685.00</td>
                                    </tr>
                                    <tr>
                                        <td class="text-truncate"><a href="#">INV-008101</a></td>
                                        <td class="text-truncate">Walter R.</td>
                                        <td class="text-truncate"><span class="tag tag-default tag-warning">Overdue</span></td>
                                        <td class="text-truncate">18/05/2016</td>
                                        <td class="text-truncate">$ 685.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <!-- Recent invoice with Statistics -->


        <!-- Emp of month & social cards -->
        <!-- <div class="row">
            <div class="col-xl-4 col-lg-12">
                <div class="card profile-card-with-cover border-grey border-lighten-2">
                    <div class="card-img-top img-fluid bg-cover height-200" style="background: url('../../../app-assets/images/carousel/16.jpg');"></div>
                    <div class="card-profile-image">
                        <img src="../../../app-assets/images/portrait/small/avatar-s-9.png" class="rounded-circle img-border" alt="Card image">
                    </div>
                    <div class="profile-card-with-cover-content text-center">
                        <div class="card-body">
                            <h4 class="card-title">Philip Garrett</h4>
                            <p class="text-muted m-0">Employee of the month</p>
                        </div>
                        <div class="card-body">
                            <a href="#" class="btn btn-social-icon mr-1 mb-1 btn-outline-facebook"><span class="ft-facebook"></span></a>
                            <a href="#" class="btn btn-social-icon mr-1 mb-1 btn-outline-twitter"><span class="ft-twitter"></span></a>
                            <a href="#" class="btn btn-social-icon mr-1 mb-1 btn-outline-linkedin"><span class="ft-github font-medium-4"></span></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-12">
                <div class="card bg-twitter white height-200">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="text-center my-1">
                                <i class="ft-twitter font-large-3"></i>
                            </div>
                            <div class="tweet-slider">
                                <ul class="text-center">
                                    <li>Congratulations to Rob Jones in accounting for winning our <span class="yellow">#NFL</span> football pool!</li>
                                    <li>Contests are a great thing to partner on. Partnerships immediately <span class="yellow">#DOUBLE</span> the reach.</li>
                                    <li>Puns, humor, and quotes are great content on <span class="yellow">#Twitter</span>. Find some related to your business.</li>
                                    <li>Are there <span class="yellow">#common-sense</span> facts related to your business? Combine them with a great photo.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card bg-warning text-center height-200">
                    <div class="card-content">
                        <div class="card-body">
                            <img src="../../../app-assets/images/elements/04.png" alt="element 05" height="110" class="mb-1">
                            <h4 class="card-title m-0 white">Storage Device</h4>
                            <p class="card-text white">Best Design</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-12">
                <div class="card card-inverse card-success text-center height-200">
                    <div class="card-content">
                        <div class="card-body">
                            <img src="../../../app-assets/images/elements/06.png" alt="element 05" height="110" class="mb-1">
                            <h4 class="card-title m-0">Ceramic Bottle</h4>
                            <p class="card-text">Best UI</p>
                        </div>
                    </div>
                </div>
                <div class="card bg-facebook white height-200">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="text-center my-1">
                                <i class="ft-facebook font-large-3"></i>
                            </div>
                            <div class="fb-post-slider" dir="rtl">
                                <ul class="text-center">
                                    <li>Congratulations to Rob Jones in accounting for winning our #NFL football pool!</li>
                                    <li>Contests are a great thing to partner on. Partnerships immediately #DOUBLE the reach.</li>
                                    <li>Puns, humor, and quotes are great content on #Twitter. Find some related to your business.</li>
                                    <li>Are there #common-sense facts related to your business? Combine them with a great photo.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        / Emp of month & social cards -->
        <!--/CLNDR Wrapper -->

        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var ctx = document.getElementById('monthly-in').getContext('2d');

            // Set fixed labels for months Jan - Dec
            var labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

            // Data from the database
            var months = @json($months); // e.g., ['2023-01', '2023-02', ...]
            var counts = @json($counts); // e.g., [15, 20, ...]

            // Create a mapping from month (YYYY-MM) to count
            var dataMap = {};
            for (var i = 0; i < months.length; i++) {
                dataMap[months[i]] = counts[i];
            }

            // Initialize data array with zeros for all months
            var data = new Array(12).fill(0);

            // Populate data array with counts from dataMap
            labels.forEach((label, index) => {
                var monthKey = new Date(new Date().getFullYear(), index, 1).toISOString().substr(0, 7); // Create YYYY-MM key for the current year
                if (dataMap[monthKey]) {
                    data[index] = dataMap[monthKey];
                }
            });

            var maxDataValue = Math.max(...data);

            var monthlySalesChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels, // Use fixed labels
                    datasets: [{
                        label: 'Monthly Data In',
                        data: data, // Use mapped data
                        backgroundColor: 'rgba(0, 123, 255, 0.5)',
                        borderColor: 'rgba(0, 123, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        x: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Month'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Count'
                            },
                            ticks: {
                                stepSize: 5,
                                min: 0,
                                max: Math.ceil(maxDataValue / 5) * 5
                            }
                        }
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var ctx = document.getElementById('monthly-out').getContext('2d');

            // Set fixed labels for months Jan - Dec
            var labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

            // Data from the database
            var months = @json($monthsout); // e.g., ['2023-01', '2023-02', ...]
            var counts = @json($countsout); // e.g., [15, 20, ...]

            // Create a mapping from month (YYYY-MM) to count
            var dataMap = {};
            for (var i = 0; i < months.length; i++) {
                dataMap[months[i]] = counts[i];
            }

            // Initialize data array with zeros for all months
            var data = new Array(12).fill(0);

            // Populate data array with counts from dataMap
            labels.forEach((label, index) => {
                var monthKey = new Date(new Date().getFullYear(), index, 1).toISOString().substr(0, 7); // Create YYYY-MM key for the current year
                if (dataMap[monthKey]) {
                    data[index] = dataMap[monthKey];
                }
            });

            var maxDataValue = Math.max(...data);

            var monthlySalesChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels, // Use fixed labels
                    datasets: [{
                        label: 'Monthly Data Out',
                        data: data, // Use mapped data
                        backgroundColor: 'rgba(0, 123, 255, 0.5)',
                        borderColor: 'rgba(0, 123, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        x: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Month'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Count'
                            },
                            ticks: {
                                stepSize: 5,
                                min: 0,
                                max: Math.ceil(maxDataValue / 5) * 5
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection