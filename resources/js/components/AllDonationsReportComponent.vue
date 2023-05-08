<template>
    <div>
        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <h3>Filter Donations</h3>
                        <form class="row g-3">
                            <div class="col-md-12">
                                <label for="project_id" class="form-label">Select Project</label>
                                <select class="form-control" v-model="filterForm.project_uuid" v-on:change="selectAccount()">
                                    <option v-for="project in projects.data" :value="project.uuid">{{ project.name }}</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="account_id" class="form-label">Select Account</label>
                                <select class="form-control" v-model="filterForm.account_uuid">
                                    <option v-for="account in accounts.data" :value="account.uuid">{{ account.name }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="start" class="form-label">Start</label>
                                <input type="date" v-model="filterForm.start" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="end" class="form-label">End</label>
                                <input type="date" v-model="filterForm.end" class="form-control">
                            </div>
                            <button v-if="filterForm.processing" class="btn btn-primary btn-rounded btn-block" type="button" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Processing...
                            </button>
                            <button v-else v-on:click.prevent="filterDonations()" type="button" class="btn btn-primary btn-block">Filter</button>
                            <button v-on:click.prevent="clearFilter()" type="button" class="btn btn-danger btn-block">Clear Filter</button>
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
                            <table class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Receipt No</th>
                                    <th>Name</th>
                                    <th>Msisdn/ Phone</th>
                                    <th>Amount (Ksh)</th>
                                    <th>Account No</th>
                                    <th>Trans Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="donation in pagination.data" :key="donation.uuid">
                                    <td>{{ donation.trans_id }}</td>
                                    <td>{{ donation.name }}</td>
                                    <td>{{ donation.msisdn }}</td>
                                    <td>{{ donation.amount }}</td>
                                    <td>{{ donation.account_no }}</td>
                                    <td>{{ donation.trans_time }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <ul class="pagination justify-content-center" @paginate="filterDonations()">
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
    </div>
</template>

<script>
import Swal from "sweetalert2";
    export default {
        name: "AllDonationsReportComponent",
        data() {
            return {
                projects: {},
                accounts: {},
                filterForm: {
                    project_uuid: undefined,
                    account_uuid: undefined,
                    start: undefined,
                    end: undefined,
                    processing: false
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
                offset: 10
            }
        },
        mounted() {
            this.loadProjects();
        },
        methods: {
            selectAccount(){
                this.loadAccount(this.filterForm.project_uuid);
            },
            loadAccount(project_uuid){
                const payLoad = {
                    sort_by : 'latest',
                    project_uuid : project_uuid
                };
                this.filterForm.processing = true;
                axios.post(`/load-accounts?page=${this.pagination.current_page}`, payLoad).then(response => {
                    this.accounts = response.data;
                }).catch(error => {
                    //
                }).finally(() => {
                    this.filterForm.processing = false;
                });
            },
            loadProjects(){
                const payLoad = {
                    sort_by : 'latest'
                };
                this.filterForm.processing = true;
                axios.post(`/load-projects?page=${this.pagination.current_page}`, payLoad).then(response => {
                    this.projects = response.data;
                }).catch(error => {
                    //
                }).finally(() => {
                    this.filterForm.processing = false;
                });
            },
            filterDonations(){
                if (!this.filterForm.start){
                    Swal.fire('Error!', 'Start is required', 'warning');
                    return;
                }
                if (!this.filterForm.end){
                    Swal.fire('Error!', 'End is required', 'warning');
                    return;
                }
                const payLoad = {
                    sort_by : 'latest',
                    project_uuid: this.filterForm.project_uuid,
                    account_uuid: this.filterForm.account_uuid,
                    start: this.filterForm.start,
                    end: this.filterForm.end
                };
                this.filterForm.processing = true;
                axios.post(`generate-all-donations-report`, payLoad).then(response => {
                    this.reports = response.data;
                }).catch(error => {
                    //
                }).finally(() => {
                    this.filterForm.processing = false;
                });
            },
            clearFilter(){
                this.filterForm.processing = true;
                this.filterForm.project_uuid = undefined;
                this.filterForm.account_uuid = undefined;
                this.filterForm.start = undefined;
                this.filterForm.end = undefined;
                this.loadProjects();
                this.filterForm.processing = false;
            },
            changePage(page) {
                this.pagination.current_page = page;
                this.filterDonations();
            }
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
