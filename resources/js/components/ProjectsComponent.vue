<template>
    <div>
        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <form class="row g-3">
                            <div class="col-md-12">
                                <label for="name" class="form-label">Project Name (e.g. Land Contributions)</label>
                                <input type="text" class="form-control" v-model="createForm.name">
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label">Project Description (e.g. These are the contributions towards land)</label>
                                <textarea class="form-control" v-model="createForm.description" rows="3"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="target_amount" class="form-label">Target Amount (KES)</label>
                                <input type="number" class="form-control" v-model="createForm.target_amount">
                            </div>
                            <div class="col-md-6">
                                <label for="target_date" class="form-label">Target Date</label>
                                <input type="date" class="form-control" v-model="createForm.target_date">
                            </div>
                            <button v-if="createForm.processing" class="btn btn-primary btn-rounded btn-block" type="button" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Processing...
                            </button>
                            <button v-else v-on:click.prevent="createProject()" type="button" class="btn btn-primary btn-block">Create Project</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <div class="table-responsive">
                            <table id="example" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Target Amount (Ksh)</th>
                                    <th>Target Date</th>
                                    <th colspan="1">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="project in pagination.data" :key="project.uuid">
                                    <td>{{ project.name }}</td>
                                    <td>{{ project.description }}</td>
                                    <td>{{ project.target_amount }}</td>
                                    <td>{{ project.target_date }}</td>
                                    <td>
                                        <button @click.prevent="showUpdateProjectModal(project)" class="btn btn-success btn-block"><i class="bx bx-edit"></i> Update Project</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <ul class="pagination justify-content-center" @paginate="loadProjects()">
                            <li class="page-item" v-if="pagination.current_page > 1">
                                <a href="javascript:void(0)" class="page-link btn btn-rounded btn-inverse-dark" aria-label="Previous" v-on:click.prevent="changePage(pagination.current_page - 1)">
                                    <span aria-hidden="true">Previous</span>
                                </a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumber" :class="{'active': page === pagination.current_page}">
                                <a class="page-link btn btn-rounded btn-inverse-dark" href="javascript:void(0)" v-on:click.prevent="changePage(page)">{{ page }}</a>
                            </li>
                            <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                <a href="javascript:void(0)" class="page-link btn btn-rounded btn-dark" aria-label="Next" v-on:click.prevent="changePage(pagination.current_page + 1)">
                                    <span aria-hidden="true">Next</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Project Modal -->
        <div class="modal fade" id="updateProjectModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg bg-light">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="card border-top border-0 border-4 border-primary">
                            <div class="card-body p-5">
                                <form class="row g-3">
                                    <div class="col-md-12">
                                        <label for="name" class="form-label">Project Name (e.g. Land Contributions)</label>
                                        <input type="text" class="form-control" v-model="updateForm.name">
                                    </div>
                                    <div class="col-12">
                                        <label for="description" class="form-label">Project Description (e.g. These are the contributions towards land)</label>
                                        <textarea class="form-control" v-model="updateForm.description" rows="3"></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="target_amount" class="form-label">Target Amount (KES)</label>
                                        <input type="number" class="form-control" v-model="updateForm.target_amount">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="target_date_update" class="form-label">Target Date</label>
                                        <input type="date" class="form-control" id="target_date_update" v-model="updateForm.target_date">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button v-if="updateForm.processing" class="btn btn-primary btn-rounded" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Processing...
                        </button>
                        <button v-else type="button" class="btn btn-primary" @click="updateProject()">
                            Update Project
                        </button>
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
                updateForm: {
                    project_uuid: undefined,
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
                },
                pagination: {
                    total: 0,
                    per_page: 2,
                    from: 1,
                    to: 0,
                    current_page: 1,
                    data: [],
                    last_page: 0
                },
                offset: 10,
            }
        },
        mounted() {
            this.loadStats();
            this.loadProjects();
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
            },
            loadProjects(){
                const payLoad = {
                    sort_by : 'latest',
                    direction : 2
                };
                this.createForm.processing = true;
                axios.post(`/load-projects?page=${this.pagination.current_page}`, payLoad).then(response => {
                    this.pagination = response.data;
                }).catch(error => {
                    //
                }).finally(() => {
                    this.createForm.processing = false;
                });
            },
            createProject(){
                if (!this.createForm.name){
                    Swal.fire('Error!', 'Name is required', 'warning');
                    return;
                }
                if (!this.createForm.description){
                    Swal.fire('Error!', 'Description is required', 'warning');
                    return;
                }
                if (!this.createForm.target_amount){
                    Swal.fire('Error!', 'Target amount is required', 'warning');
                    return;
                }
                if (!this.createForm.target_date){
                    Swal.fire('Error!', 'Target date is required', 'warning');
                    return;
                }
                const payLoad = {
                    name : this.createForm.name,
                    description : this.createForm.description,
                    target_amount : this.createForm.target_amount,
                    target_date : this.createForm.target_date
                };
                this.createForm.processing = true;
                axios.post(`create-project`, payLoad).then(response => {
                    if (response.data.status){
                        Swal.fire('Success!', response.data.message, 'success');
                        this.createForm.name = undefined;
                        this.createForm.description = undefined;
                        this.createForm.target_amount = undefined;
                        this.createForm.target_date = undefined;
                        this.loadProjects();
                    } else {
                        Swal.fire('Error!', response.data.message, 'error');
                    }
                }).catch(error => {
                    if (error.response && error.response.status === 422) {
                        Swal.fire('Error!', JSON.stringify(error.response.data.errors), 'error');
                    } else {
                        Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                    }
                }).finally(() => {
                    this.createForm.processing = false;
                });
            },
            showUpdateProjectModal(project){
                this.updateForm.project_uuid = project.uuid;
                this.updateForm.name = project.name;
                this.updateForm.description = project.description;
                this.updateForm.target_amount = project.target_amount;
                this.updateForm.target_date = project.target_date;
                $('#updateProjectModal').modal('show');
            },
            updateProject(){
                if (!this.updateForm.name){
                    Swal.fire('Error!', 'Name is required', 'warning');
                    return;
                }
                if (!this.updateForm.description){
                    Swal.fire('Error!', 'Description is required', 'warning');
                    return;
                }
                if (!this.updateForm.target_amount){
                    Swal.fire('Error!', 'Target amount is required', 'warning');
                    return;
                }
                if (!this.updateForm.target_date){
                    Swal.fire('Error!', 'Target date is required', 'warning');
                    return;
                }
                const payLoad = {
                    name : this.updateForm.name,
                    description : this.updateForm.description,
                    target_amount : this.updateForm.target_amount,
                    target_date : this.updateForm.target_date
                };
                this.updateForm.processing = true;
                axios.post(`update-project/${this.updateForm.project_uuid}`, payLoad).then(response => {
                    if (response.data.status){
                        Swal.fire('Success!', response.data.message, 'success');
                        this.updateForm.name = undefined;
                        this.updateForm.description = undefined;
                        this.updateForm.target_amount = undefined;
                        this.updateForm.target_date = undefined;
                        $('#updateProjectModal').modal('hide');
                        this.loadProjects();
                    } else {
                        Swal.fire('Error!', response.data.message, 'error');
                    }
                }).catch(error => {
                    if (error.response && error.response.status === 422) {
                        Swal.fire('Error!', JSON.stringify(error.response.data.errors), 'error');
                    } else {
                        Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                    }
                }).finally(() => {
                    this.updateForm.processing = false;
                });
            },
            changePage(page) {
                this.pagination.current_page = page;
                this.loadProjects();
            },
        },
        computed: {
            pagesNumber() {
                if (!this.pagination.to) {
                    return [];
                }
                let from = this.pagination.current_page - this.offset;
                if (from < 1) {
                    from = 1;
                }
                let to = from + (this.offset * 2);
                if (to >= this.pagination.last_page) {
                    to = this.pagination.last_page;
                }
                let pagesArray = [];
                for (let page = from; page <= to; page++) {
                    pagesArray.push(page);
                }
                return pagesArray;
            },
        }
    }
</script>
