<template>
    <div>
        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <h3>Create New Treasurer</h3>
                        <form class="row g-3">
                            <div class="col-md-12">
                                <label for="project_uuid" class="form-label">Select Project</label>
                                <select class="form-control" v-model="createForm.project_uuid" v-on:change="selectAccount('create')">
                                    <option v-for="project in projects.data" :value="project.uuid">{{ project.name }}</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="account_id" class="form-label">Select Account</label>
                                <select class="form-control" v-model="createForm.account_uuid">
                                    <option v-for="account in accounts.data" :value="account.uuid">{{ account.name }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" v-model="createForm.name">
                            </div>
                            <div class="col-md-6">
                                <label for="msisdn" class="form-label">Msisdn/Phone</label>
                                <input type="text" class="form-control" v-model="createForm.msisdn">
                            </div>
                            <div class="col-12">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" v-model="createForm.email">
                            </div>
                            <button v-if="createForm.processing" class="btn btn-primary btn-rounded btn-block" type="button" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Processing...
                            </button>
                            <button v-else v-on:click.prevent="createTreasurer()" type="button" class="btn btn-primary btn-block">Create Treasurer</button>
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
                                    <th>Name</th>
                                    <th>Msisdn/Phone</th>
                                    <th>Email</th>
                                    <th>Project Name</th>
                                    <th>Account Name</th>
                                    <th>Account Number</th>
                                    <th colspan="1">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="treasurer in pagination.data" :key="treasurer.uuid">
                                    <td>{{ treasurer.user.name }}</td>
                                    <td>{{ treasurer.user.msisdn }}</td>
                                    <td>{{ treasurer.user.email }}</td>
                                    <td>{{ treasurer.account.project.name }}</td>
                                    <td>{{ treasurer.account.name }}</td>
                                    <td>{{ treasurer.account.account_no }}</td>
                                    <td>
                                        <button @click.prevent="showUpdateTreasurerModal(treasurer)" class="btn btn-primary btn-block"><i class="bx bx-edit"></i> Update Treasurer</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <ul class="pagination justify-content-center" @paginate="loadTreasurers()">
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

        <!-- Update Treasure Modal -->
        <div class="modal fade" id="updateTreasurerModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg bg-light">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="card border-top border-0 border-4 border-primary">
                            <div class="card-body p-5">
                                <form class="row g-3">
                                    <div class="col-md-12">
                                        <label for="project_uuid" class="form-label">Select Project</label>
                                        <select class="form-control" v-model="updateForm.project_uuid" v-on:change="selectAccount('update')">
                                            <option v-for="project in projects.data" :value="project.uuid">{{ project.name }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="account_id" class="form-label">Select Account</label>
                                        <select class="form-control" v-model="updateForm.account_uuid">
                                            <option v-for="account in accounts.data" :value="account.uuid">{{ account.name }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control" v-model="updateForm.name">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="msisdn" class="form-label">Msisdn/Phone</label>
                                        <input type="text" class="form-control" v-model="updateForm.msisdn">
                                    </div>
                                    <div class="col-12">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" v-model="updateForm.email">
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
                        <button v-else type="button" class="btn btn-primary" @click="updateTreasurer()">
                            Update Treasurer
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
        name: "TreasurersComponent",
        data() {
            return {
                projects: {},
                accounts: {},
                createForm: {
                    project_uuid: undefined,
                    account_uuid: undefined,
                    name: undefined,
                    msisdn: undefined,
                    email: undefined,
                    processing: false
                },
                updateForm: {
                    project_uuid: undefined,
                    treasure_uuid: undefined,
                    user_uuid: undefined,
                    account_uuid: undefined,
                    name: undefined,
                    msisdn: undefined,
                    email: undefined,
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
                offset: 10,
            }
        },
        mounted() {
            this.loadProjects();
            this.loadTreasurers();
        },
        methods: {
            selectAccount(type){
                if (type === 'create'){
                    this.loadAccount(this.createForm.project_uuid);
                } else {
                    this.loadAccount(this.updateForm.project_uuid);
                }
            },
            loadProjects(){
                const payLoad = {
                    sort_by : 'latest'
                };
                this.createForm.processing = true;
                axios.post(`/load-projects?page=${this.pagination.current_page}`, payLoad).then(response => {
                    this.projects = response.data;
                }).catch(error => {
                    //
                }).finally(() => {
                    this.createForm.processing = false;
                });
            },
            loadAccount(project_uuid){
                const payLoad = {
                    sort_by : 'latest',
                    project_uuid : project_uuid
                };
                this.createForm.processing = true;
                axios.post(`/load-accounts?page=${this.pagination.current_page}`, payLoad).then(response => {
                    this.accounts = response.data;
                }).catch(error => {
                    //
                }).finally(() => {
                    this.createForm.processing = false;
                });
            },
            loadTreasurers(){
                const payLoad = {
                    sort_by : 'latest'
                };
                this.createForm.processing = true;
                axios.post(`/load-treasurers?page=${this.pagination.current_page}`, payLoad).then(response => {
                    this.pagination = response.data;
                }).catch(error => {
                    //
                }).finally(() => {
                    this.createForm.processing = false;
                });
            },
            createTreasurer(){
                if (!this.createForm.account_uuid){
                    Swal.fire('Error!', 'Account is required', 'warning');
                    return;
                }
                if (!this.createForm.name){
                    Swal.fire('Error!', 'Name is required', 'warning');
                    return;
                }
                if (!this.createForm.msisdn){
                    Swal.fire('Error!', 'Msisdn/Phone is required', 'warning');
                    return;
                }
                if (!this.createForm.email){
                    Swal.fire('Error!', 'Email is required', 'warning');
                    return;
                }
                const payLoad = {
                    name : this.createForm.name,
                    msisdn : this.createForm.msisdn,
                    email : this.createForm.email
                };
                this.createForm.processing = true;
                axios.post(`create-treasurer/${this.createForm.account_uuid}`, payLoad).then(response => {
                    if (response.data.status){
                        Swal.fire('Success!', response.data.message, 'success');
                        this.createForm.account_uuid = undefined;
                        this.createForm.name = undefined;
                        this.createForm.msisdn = undefined;
                        this.createForm.email = undefined;
                        this.loadProjects();
                        this.loadTreasurers();
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
            showUpdateTreasurerModal(treasure){
                this.updateForm.project_uuid = treasure.account.project.uuid;
                this.loadAccount(this.updateForm.project_uuid);
                this.updateForm.treasure_uuid = treasure.uuid;
                this.updateForm.user_uuid = treasure.user.uuid;
                this.updateForm.account_uuid = treasure.account.uuid;
                this.updateForm.name = treasure.user.name;
                this.updateForm.msisdn = treasure.user.msisdn;
                this.updateForm.email = treasure.user.email;
                $('#updateTreasurerModal').modal('show');
            },
            updateTreasurer(){
                if (!this.updateForm.account_uuid){
                    Swal.fire('Error!', 'Account is required', 'warning');
                    return;
                }
                if (!this.updateForm.name){
                    Swal.fire('Error!', 'Name is required', 'warning');
                    return;
                }
                if (!this.updateForm.msisdn){
                    Swal.fire('Error!', 'Msisdn/Phone is required', 'warning');
                    return;
                }
                if (!this.updateForm.email){
                    Swal.fire('Error!', 'Email is required', 'warning');
                    return;
                }
                const payLoad = {
                    name : this.updateForm.name,
                    msisdn : this.updateForm.msisdn,
                    email : this.updateForm.email
                };
                this.updateForm.processing = true;
                axios.post(`update-treasurer/${this.updateForm.treasure_uuid}/${this.updateForm.user_uuid}/${this.updateForm.account_uuid}`, payLoad).then(response => {
                    if (response.data.status){
                        Swal.fire('Success!', response.data.message, 'success');
                        this.updateForm.account_uuid = undefined;
                        this.updateForm.name = undefined;
                        this.updateForm.msisdn = undefined;
                        this.updateForm.email = undefined;
                        $('#updateTreasurerModal').modal('hide');
                        this.loadProjects();
                        this.loadTreasurers();
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
                this.loadTreasurers();
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
