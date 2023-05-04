<template>
    <div>
        <div class="row">
            <div class="col-xl-7 mx-auto">
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <form class="row g-3">
                            <div class="col-md-12">
                                <label for="name" class="form-label">Project Name</label>
                                <input type="text" class="form-control" v-model="createForm.name">
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label">Project Description</label>
                                <textarea class="form-control" v-model="createForm.description" rows="3"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="target_amount" class="form-label">Target Amount</label>
                                <input type="number" class="form-control" v-model="createForm.target_amount">
                            </div>
                            <div class="col-md-6">
                                <label for="target_date" class="form-label">Target Date</label>
                                <input type="date" class="form-control" v-model="createForm.target_date">
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Create Project</button>
                        </form>
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
                createForm: {
                    name: undefined,
                    description: undefined,
                    target_amount: undefined,
                    target_date: undefined,
                    processing: false
                },
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
