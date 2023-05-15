<template>
    <div>
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
            <div class="col">
                <div class="card radius-10 border-start border-0 border-3 border-info">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">Today (KES)</p>
                                <h4 class="my-1 text-info">{{ stats.donations_today }}</h4>
                                <p class="mb-0 font-13">Total Donations</p>
                            </div>
                            <div class="widgets-icons-2 rounded-circle bg-gradient-scooter text-white ms-auto"><i class='bx bxs-credit-card'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 border-start border-0 border-3 border-danger">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">This Month (KES)</p>
                                <h4 class="my-1 text-danger">{{ stats.donations_this_month }}</h4>
                                <p class="mb-0 font-13">Total Donations</p>
                            </div>
                            <div class="widgets-icons-2 rounded-circle bg-gradient-bloody text-white ms-auto"><i class='bx bxs-wallet'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 border-start border-0 border-3 border-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">Active Projects</p>
                                <h4 class="my-1 text-success">{{ stats.active_projects }}</h4>
                                <p class="mb-0 font-13">Ongoing</p>
                            </div>
                            <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto"><i class='bx bxs-calendar-alt' ></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 border-start border-0 border-3 border-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">Total Donations (KES)</p>
                                <h4 class="my-1 text-warning">{{ stats.total_donations }}</h4>
                                <p class="mb-0 font-13">From active projects</p>
                            </div>
                            <div class="widgets-icons-2 rounded-circle bg-gradient-blooker text-white ms-auto"><i class='bx bxs-group'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-8 col-lg-8 col-xl-8">
                <div class="card radius-10">
                    <div class="card-body">
                        <div id="chart">
                            <apexchart type="bar" height="350" :options="stats.barChartOptions" :series="stats.barSeries"></apexchart>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-4 col-lg-4 col-xl-4">
                <div class="card radius-10">
                    <div class="card-header bg-transparent">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0">Active Projects</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="chart">
                            <apexchart type="donut" :options="stats.donutChartOptions" :series="stats.donutSeries"></apexchart>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
import Swal from "sweetalert2";
    export default {
        name: "DashboardComponent",
        data() {
            return {
                stats: {
                    processing: false,
                    donations_today: 0,
                    donations_this_month: 0,
                    active_projects: 0,
                    total_donations: 0,
                    barChartOptions: {
                        chart: {
                            type: 'bar',
                            height: 350
                        },
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: '55%',
                                endingShape: 'rounded'
                            },
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            show: true,
                            width: 2,
                            colors: ['transparent']
                        },
                        xaxis: {
                            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        },
                        yaxis: {
                            title: {
                                text: 'KES (thousands)'
                            }
                        },
                        fill: {
                            opacity: 1
                        },
                        tooltip: {
                            y: {
                                formatter: function (val) {
                                    return "KES " + val
                                }
                            }
                        }
                    },
                    barSeries: [{
                        name: 'M-pesa',
                        data: [76, 85, 101, 98, 87, 105, 91, 114, 94, 45, 87, 36]
                    }],
                    donutChartOptions: {
                        chart: {
                            type: 'donut',
                        },
                        labels: ['Total Donations', 'Balance', 'Administration Fees'],
                        responsive: [{
                            breakpoint: 480,
                            options: {
                                chart: {
                                    width: 200
                                },
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }]
                    },
                    donutSeries: [10000, 3000, 5000],
                }
            }
        },
        mounted() {
            this.loadStats();
        },
        methods: {
            loadStats(){
                this.stats.processing = true;
                axios.post(`/load-stats`).then(response => {
                    this.stats.donations_today = response.data.donations_today;
                    this.stats.donations_this_month = response.data.donations_this_month;
                    this.stats.active_projects = response.data.active_projects;
                    this.stats.total_donations = response.data.total_donations;
                    this.stats.donutChartOptions = {
                        chart: {
                            type: 'donut',
                        },
                        labels: response.data.labels,
                        responsive: [{
                            breakpoint: 480,
                            options: {
                                chart: {
                                    width: 200
                                },
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }]
                    };
                    this.stats.donutSeries = response.data.series;
                    this.stats.barSeries = [{
                        name: 'M-pesa',
                        data: response.data.donations_summary
                    }];
                }).catch(error => {
                    Swal.fire('Error!', 'Server Error... Try Again Later', 'error');
                }).finally(() => {
                    this.stats.processing = false;
                });
            }
        },
    }
</script>
