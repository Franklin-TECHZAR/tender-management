@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')

    <div class="main-container">
        <div class="xs-pd-20-10 pd-ltr-20">
            <div class="title pb-20">
                <h2 class="h3 mb-0">Dashboard</h2>
            </div>

            <div class="row pb-10">
                <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                    <div class="card-box height-100-p widget-style3">
                        <div class="d-flex flex-wrap">
                            <div class="widget-data">
                                <div class="weight-700 font-24 text-dark">75</div>
                                <div class="font-14 text-secondary weight-500">
                                    Job Orders
                                </div>
                            </div>
                            <div class="widget-icon">
                                <div class="icon" data-color="#00eccf">
                                    <i class="icon-copy dw dw-calendar1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                    <div class="card-box height-100-p widget-style3">
                        <div class="d-flex flex-wrap">
                            <div class="widget-data">
                                <div class="weight-700 font-24 text-dark">56</div>
                                <div class="font-14 text-secondary weight-500">
                                    Labours
                                </div>
                            </div>
                            <div class="widget-icon">
                                <div class="icon" data-color="#ff5b5b">
                                    <span class="icon-copy fa fa-users"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                    <div class="card-box height-100-p widget-style3">
                        <div class="d-flex flex-wrap">
                            <div class="widget-data">
                                <div class="weight-700 font-24 text-dark">1,24,551</div>
                                <div class="font-14 text-secondary weight-500">
                                    Expenses
                                </div>
                            </div>
                            <div class="widget-icon">
                                <div class="icon">
                                    <i class="icon-copy fa fa-money" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                    <div class="card-box height-100-p widget-style3">
                        <div class="d-flex flex-wrap">
                            <div class="widget-data">
                                <div class="weight-700 font-24 text-dark">₹5,50,000</div>
                                <div class="font-14 text-secondary weight-500">Purchase</div>
                            </div>
                            <div class="widget-icon">
                                <div class="icon" data-color="#09cc06">
                                    <i class="icon-copy bi bi-cart" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row pb-10">
                <div class="col-md-6 mb-20">
                    <div class="card-box height-100-p pd-20">
                        <div class="d-flex flex-wrap justify-content-between align-items-center pb-0 pb-md-3">
                            <div class="h5 mb-md-0">Purchase</div>
                            <div class="form-group mb-md-0">
                                <select class="form-control form-control-sm selectpicker">
                                    <option value="">Last Week</option>
                                    <option value="">Last Month</option>
                                    <option value="">Last 6 Month</option>
                                    <option value="">Last 1 year</option>
                                </select>
                            </div>
                        </div>
                        <div id="purchase-chart"></div>
                    </div>
                </div>
                <div class="col-md-6 mb-20">
                    <div class="card-box height-100-p pd-20">
                        <div class="d-flex flex-wrap justify-content-between align-items-center pb-0 pb-md-3">
                            <h2 class="h4 mb-20">Activity</h2>
                            <div class="form-group mb-md-0">
                                <select class="form-control form-control-sm  selectpicker" name="job_orders" id="job_orders" required>
                                    <option value="" selected>Select Job Order</option>
                                    @foreach ($tenders as $id => $tenderName)
                                        <option value="{{ $id }}">{{ $tenderName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="activity-chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('addscript')
    <script src="{{ url('theme/src/plugins/apexcharts/apexcharts.min.js') }}"></script>
    {{-- <script src="{{ url('theme/vendors/scripts/dashboard3.js') }}"></script> --}}
    <script src="{{ url('theme/vendors/scripts/dashboard.js') }}"></script>
    <script>
        var purchaseData = {!! json_encode($purchaseData) !!};
        var salaryData = {!! json_encode($salaryData) !!};
        var expenseData = {!! json_encode($expenseData) !!};

        var allMonths = Object.keys({
            ...purchaseData,
            ...salaryData,
            ...expenseData
        });
        var sortedMonths = allMonths.sort((a, b) => {
            return new Date('01 ' + a) - new Date('01 ' + b);
        });
        var jobOrders = {!! json_encode($tenders) !!};
        console.log('jobOrders',jobOrders);
        console.log('jobOrders2',Object.keys(jobOrders).map(key => jobOrders[key]));

        var options = {
            series: [{
                    name: "Purchase",
                    data: sortedMonths.map(month => purchaseData[month] || 0)
                },
                {
                    name: "Expenses",
                    data: sortedMonths.map(month => expenseData[month] || 0)
                },
                {
                    name: "Salary",
                    data: sortedMonths.map(month => salaryData[month] || 0)
                }
            ],
            chart: {
                height: 300,
                type: 'line',
                zoom: {
                    enabled: false,
                },
                dropShadow: {
                    enabled: true,
                    color: '#000',
                    top: 18,
                    left: 7,
                    blur: 16,
                    opacity: 0.2
                },
                toolbar: {
                    show: false
                }
            },
            colors: ['#ff7f0e', '#1f77b4', '#2ca02c'],
            dataLabels: {
                enabled: false,
            },
            stroke: {
                width: [3, 3, 3],
                curve: 'smooth'
            },
            grid: {
                show: false,
            },
            markers: {
                colors: ['#ff7f0e', '#1f77b4', '#2ca02c'],
                size: 5,
                strokeColors: '#ffffff',
                strokeWidth: 2,
                hover: {
                    sizeOffset: 2
                }
            },
            xaxis: {
                categories: sortedMonths,
                labels: {
                    style: {
                        colors: '#8c9094'
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                floating: true,
                offsetY: 0,
                labels: {
                    useSeriesColors: true
                },
                markers: {
                    width: 10,
                    height: 10,
                }
            }
        };
        var options2 = {
            series: [{
                    name: "Purchase",
                    data: sortedMonths.map(month => purchaseData[month] || 0)
                },
                {
                    name: "Expenses",
                    data: sortedMonths.map(month => expenseData[month] || 0)
                },
                {
                    name: "Salary",
                    data: sortedMonths.map(month => salaryData[month] || 0)
                }
            ],
            chart: {
                height: 300,
                type: 'bar',
                zoom: {
                    enabled: false,
                },
                dropShadow: {
                    enabled: true,
                    color: '#000',
                    top: 18,
                    left: 7,
                    blur: 16,
                    opacity: 0.2
                },
                toolbar: {
                    show: false
                }
            },
            colors: ['#ff7f0e', '#1f77b4', '#2ca02c'],
            dataLabels: {
                enabled: false,
            },
            stroke: {
                width: [3, 3, 3],
                curve: 'smooth'
            },
            grid: {
                show: false,
            },
            markers: {
                colors: ['#ff7f0e', '#1f77b4', '#2ca02c'],
                size: 5,
                strokeColors: '#ffffff',
                strokeWidth: 2,
                hover: {
                    sizeOffset: 2
                }
            },
            xaxis: {
                categories: Object.keys(jobOrders).map(key => jobOrders[key]),
                labels: {
                    style: {
                        colors: '#8c9094'
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                floating: true,
                offsetY: 0,
                labels: {
                    useSeriesColors: true
                },
                markers: {
                    width: 10,
                    height: 10,
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#purchase-chart"), options);
        chart.render();

        var chart2 = new ApexCharts(document.querySelector("#activity-chart"), options2);
        chart2.render();

        var chart3 = new ApexCharts(document.querySelector("#surgery-chart"), options3);
        chart3.render();

        var chart4 = new ApexCharts(document.querySelector("#diseases-chart"), options4);

        $('document').ready(function() {
            $('.data-table').DataTable({
                scrollCollapse: false,
                autoWidth: false,
                responsive: true,
                searching: false,
                bLengthChange: false,
                bPaginate: true,
                bInfo: false,
                columnDefs: [{
                    targets: "datatable-nosort",
                    orderable: false,
                }],
                "lengthMenu": [
                    [5, 25, 50, -1],
                    [5, 25, 50, "All"]
                ],
                "language": {
                    "info": "_START_-_END_ of _TOTAL_ entries",
                    searchPlaceholder: "Search",
                    paginate: {
                        next: '<i class="ion-chevron-right"></i>',
                        previous: '<i class="ion-chevron-left"></i>'
                    }
                },
            });
        });
    </script>
@endsection
