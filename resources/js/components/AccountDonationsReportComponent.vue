<template>
    <div>
        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
<!--                        <h3>Create New Account</h3>-->
                        <form class="row g-3">
                            <div class="col-md-12">
                                <label for="project_id" class="form-label">Select Project</label>
                                <select class="form-control" v-model="filterForm.project_uuid">
                                    <option v-for="project in projects.data" :value="project.uuid">{{ project.name }}</option>
                                </select>
                            </div>
                            <button v-if="filterForm.processing" class="btn btn-primary btn-rounded btn-block" type="button" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Processing...
                            </button>
                            <button v-else v-on:click.prevent="filterReport()" type="button" class="btn btn-primary btn-block">Filter</button>
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
                                    <th>Account Name</th>
                                    <th>Account Number</th>
                                    <th>Account Target</th>
                                    <th>Project Name</th>
                                    <th>Project Target</th>
                                    <th>Amount Donated</th>
                                    <th>Balance</th>
                                    <th>% Donations Against Account Target</th>
                                    <th>% Donations Against Project Target</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="account in reports" :key="account.uuid">
                                    <td>{{ account.name }}</td>
                                    <td>{{ account.account_no }}</td>
                                    <td>{{ account.target_amount }}</td>
                                    <td>{{ account.project_name }}</td>
                                    <td>{{ account.project_target }}</td>
                                    <td>{{ account.amount_donated }}</td>
                                    <td>{{ account.balance }}</td>
                                    <td>{{ account.donations_against_account_target }} %</td>
                                    <td>{{ account.donations_against_project_target }} %</td>
                                </tr>
                                </tbody>
                            </table>
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
        name: "AccountDonationsReportComponent",
        data() {
            return {
                projects: {},
                reports: {},
                filterForm: {
                    project_uuid: undefined,
                    processing: false
                }
            }
        },
        mounted() {
            this.loadProjects();
        },
        methods: {
            loadProjects(){
                const payLoad = {
                    sort_by : 'latest'
                };
                this.filterForm.processing = true;
                axios.post(`/load-projects`, payLoad).then(response => {
                    this.projects = response.data;
                }).catch(error => {
                    //
                }).finally(() => {
                    this.filterForm.processing = false;
                });
            },
            filterReport(){
                if (!this.filterForm.project_uuid){
                    Swal.fire('Error!', 'Project is required', 'warning');
                    return;
                }
                this.filterForm.processing = true;
                axios.post(`generate-account-donations-report/${this.filterForm.project_uuid}`).then(response => {
                    this.reports = response.data;
                }).catch(error => {
                    //
                }).finally(() => {
                    this.filterForm.processing = false;
                });
            }
        }
    }
</script>
