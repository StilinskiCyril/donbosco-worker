<template>
    <div>
        <div class="row">
            <div class="col-xl-6 mx-auto">
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <h3>Create New Account</h3>
                        <form class="row g-3">
                            <div class="col-md-12">
                                <label for="name" class="form-label">Account Name</label>
                                <input type="text" class="form-control" v-model="createForm.name">
                            </div>
                            <div class="col-md-12">
                                <label for="project_id" class="form-label">Select Project</label>
                                <select class="form-control" v-model="createForm.project_uuid">
                                    <option v-for="project in projects.data" :value="project.uuid">{{ project.name }}</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label"> Account Description</label>
                                <textarea class="form-control" v-model="createForm.description" rows="4"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="account_number" class="form-label">Account Number</label>
                                <input type="text" class="form-control" v-model="createForm.account_no">
                            </div>
                            <div class="col-md-6">
                                <label for="target_amount" class="form-label">Target Amount</label>
                                <input type="number" class="form-control" v-model="createForm.target_amount">
                            </div>
                            <div class="col-12">
                                <label for="target_date" class="form-label">Target Date</label>
                                <input type="date" class="form-control" v-model="createForm.target_date">
                            </div>
                            <div class="col-12">
                                <label for="message_to_donor" class="form-label">Message To Donor</label>
                                <textarea class="form-control" v-model="createForm.message_to_donor" rows="4" placeholder="Thank you [1] for donating KES [2]  towards [7]. Your total donation is KES [3]. The Grand Total is KES [5]."></textarea>
                            </div>
                            <div class="col-12">
                                <label for="message_to_treasurer" class="form-label">Message To Treasurer</label>
                                <textarea class="form-control" v-model="createForm.message_to_treasurer" placeholder="[1] has donated KES [2] towards [7] totaling KES [3]. New Grand Total is KES [5]." rows="4"></textarea>
                            </div>
                            <button v-if="createForm.processing" class="btn btn-primary btn-rounded btn-block" type="button" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Processing...
                            </button>
                            <button v-else v-on:click.prevent="createAccount()" type="button" class="btn btn-primary btn-block">Create Account</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 mx-auto">
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <h3>Create New Account From Existing Account</h3>
                        <form class="row g-3">
                            <div class="col-md-12">
                                <label for="name" class="form-label">Account Name</label>
                                <input type="text" class="form-control" v-model="createFromExistingAccountForm.name">
                            </div>
                            <div class="col-md-12">
                                <label for="account_uuid" class="form-label">Select Account</label>
                                <select class="form-control" v-model="createFromExistingAccountForm.account_uuid">
                                    <option v-for="account in pagination.data" :value="account.uuid">{{ account.name }}</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="account_number" class="form-label">Account Number</label>
                                <input type="text" class="form-control" v-model="createFromExistingAccountForm.account_no">
                            </div>
                            <button v-if="createFromExistingAccountForm.processing" class="btn btn-primary btn-rounded btn-block" type="button" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Processing...
                            </button>
                            <button v-else v-on:click.prevent="createAccountFromExisting()" type="button" class="btn btn-primary btn-block">Create Account</button>
                        </form>
                    </div>
                </div>
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <h5>Sample Donor Message</h5>
                        <p>Thank you [1] for donating KES [2]  towards [7]. Your total donation is KES [3]. The Grand Total is KES [5].</p>
                        <hr>
                        <h5>Sample Treasurer Message</h5>
                        <p>[1] has donated KES [2] towards [7] totaling KES [3]. New Grand Total is KES [5].</p>
                        <hr>
                        <p>[1] Donor name</p>
                        <p>[2] Amount paid</p>
                        <p>[3] Total individual donation</p>
                        <p>[5] Grand total for project</p>
                        <p>[7] Project name</p>
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
                                    <th>Project Name</th>
                                    <th>Account Name</th>
                                    <th>Account Number</th>
                                    <th>Description</th>
                                    <th>Target Amount (Ksh)</th>
                                    <th>Target Date</th>
                                    <th>Msg To Donor</th>
                                    <th>Msg To Treasurer</th>
                                    <th colspan="1">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="account in pagination.data" :key="account.uuid">
                                    <td>{{ account.project.name }}</td>
                                    <td>{{ account.name }}</td>
                                    <td>{{ account.account_no }}</td>
                                    <td>{{ account.description }}</td>
                                    <td>{{ account.target_amount }}</td>
                                    <td>{{ account.target_date }}</td>
                                    <td>{{ account.message_to_donor }}</td>
                                    <td>{{ account.message_to_treasurer }}</td>
                                    <td>
                                        <button @click.prevent="showUpdateAccountModal(account)" class="btn btn-primary btn-block"><i class="bx bx-edit"></i> Update Account</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <ul class="pagination justify-content-center" @paginate="loadAccounts()">
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

        <!-- Update Account Modal -->
        <div class="modal fade" id="updateAccountModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg bg-light">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="card border-top border-0 border-4 border-primary">
                            <div class="card-body p-5">
                                <form class="row g-3">
                                    <div class="col-md-12">
                                        <label for="name" class="form-label">Account Name</label>
                                        <input type="text" class="form-control" v-model="updateForm.name">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="project_id" class="form-label">Select Project</label>
                                        <select class="form-control" v-model="updateForm.project_uuid">
                                            <option v-for="project in projects.data" :value="project.uuid">{{ project.name }}</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label for="description" class="form-label"> Account Description</label>
                                        <textarea class="form-control" v-model="updateForm.description" rows="4"></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="account_number" class="form-label">Account Number</label>
                                        <input type="text" class="form-control" v-model="updateForm.account_no">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="target_amount" class="form-label">Target Amount</label>
                                        <input type="number" class="form-control" v-model="updateForm.target_amount">
                                    </div>
                                    <div class="col-12">
                                        <label for="target_date" class="form-label">Target Date</label>
                                        <input type="date" class="form-control" v-model="updateForm.target_date">
                                    </div>
                                    <div class="col-12">
                                        <label for="message_to_donor" class="form-label">Message To Donor</label>
                                        <textarea class="form-control" v-model="updateForm.message_to_donor" rows="4" placeholder="Thank you [1] for donating KES [2]  towards [7]. Your total donation is KES [3]. The Grand Total is KES [5]."></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label for="message_to_treasurer" class="form-label">Message To Treasurer</label>
                                        <textarea class="form-control" v-model="updateForm.message_to_treasurer" placeholder="[1] has donated KES [2] towards [7] totaling KES [3]. New Grand Total is KES [5]." rows="4"></textarea>
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
                        <button v-else type="button" class="btn btn-primary" @click="updateAccount()">
                            Update Account
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
        name: "AccountsComponent",
        data() {
            return {
                projects: {},
                createForm: {
                    name: undefined,
                    project_uuid: undefined,
                    description: undefined,
                    account_no: undefined,
                    target_amount: undefined,
                    target_date: undefined,
                    message_to_donor: undefined,
                    message_to_treasurer: undefined,
                    processing: false
                },
                createFromExistingAccountForm: {
                    account_uuid: undefined,
                    name: undefined,
                    account_no: undefined,
                    processing: false
                },
                updateForm: {
                    account_uuid: undefined,
                    name: undefined,
                    project_uuid: undefined,
                    description: undefined,
                    account_no: undefined,
                    target_amount: undefined,
                    target_date: undefined,
                    message_to_donor: undefined,
                    message_to_treasurer: undefined,
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
            this.loadAccounts();
        },
        methods: {
            loadProjects(){
                const payLoad = {
                    sort_by : 'latest',
                    direction : 2
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
            loadAccounts(){
                const payLoad = {
                    sort_by : 'latest',
                    direction : 2
                };
                this.createForm.processing = true;
                axios.post(`/load-accounts?page=${this.pagination.current_page}`, payLoad).then(response => {
                    this.pagination = response.data;
                }).catch(error => {
                    //
                }).finally(() => {
                    this.createForm.processing = false;
                });
            },
            createAccount(){
                if (!this.createForm.name){
                    Swal.fire('Error!', 'Account name is required', 'warning');
                    return;
                }
                if (!this.createForm.project_uuid){
                    Swal.fire('Error!', 'Project is required', 'warning');
                    return;
                }
                if (!this.createForm.description){
                    Swal.fire('Error!', 'Description is required', 'warning');
                    return;
                }
                if (!this.createForm.account_no){
                    Swal.fire('Error!', 'Account no is required', 'warning');
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
                if (!this.createForm.message_to_donor){
                    Swal.fire('Error!', 'Message to donor is required', 'warning');
                    return;
                }
                if (!this.createForm.message_to_treasurer){
                    Swal.fire('Error!', 'Message to treasurer is required', 'warning');
                    return;
                }
                const payLoad = {
                    name : this.createForm.name,
                    description : this.createForm.description,
                    account_no : this.createForm.account_no,
                    target_amount : this.createForm.target_amount,
                    target_date : this.createForm.target_date,
                    message_to_donor : this.createForm.message_to_donor,
                    message_to_treasurer : this.createForm.message_to_treasurer
                };
                this.createForm.processing = true;
                axios.post(`create-account/${this.createForm.project_uuid}`, payLoad).then(response => {
                    if (response.data.status){
                        Swal.fire('Success!', response.data.message, 'success');
                        this.createForm.name = undefined;
                        this.createForm.description = undefined;
                        this.createForm.account_no = undefined;
                        this.createForm.target_amount = undefined;
                        this.createForm.target_date = undefined;
                        this.createForm.message_to_donor = undefined;
                        this.createForm.message_to_treasurer = undefined;
                        this.loadProjects();
                        this.loadAccounts();
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
            createAccountFromExisting(){
                if (!this.createFromExistingAccountForm.account_uuid){
                    Swal.fire('Error!', 'Account is required', 'warning');
                    return;
                }
                if (!this.createFromExistingAccountForm.name){
                    Swal.fire('Error!', 'Name is required', 'warning');
                    return;
                }
                if (!this.createFromExistingAccountForm.account_no){
                    Swal.fire('Error!', 'Account no is required', 'warning');
                    return;
                }
                const payLoad = {
                    name : this.createFromExistingAccountForm.name,
                    account_no : this.createFromExistingAccountForm.account_no
                };
                this.createFromExistingAccountForm.processing = true;
                axios.post(`create-account-existing/${this.createFromExistingAccountForm.account_uuid}`, payLoad).then(response => {
                    if (response.data.status){
                        Swal.fire('Success!', response.data.message, 'success');
                        this.createFromExistingAccountForm.name = undefined;
                        this.createFromExistingAccountForm.account_no = undefined;
                        this.loadProjects();
                        this.loadAccounts();
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
                    this.createFromExistingAccountForm.processing = false;
                });
            },
            showUpdateAccountModal(account){
                this.updateForm.account_uuid = account.uuid;
                this.updateForm.name = account.name;
                this.updateForm.project_uuid = account.project.uuid;
                this.updateForm.description = account.description;
                this.updateForm.account_no = account.account_no;
                this.updateForm.target_amount = account.target_amount;
                this.updateForm.target_date = account.target_date;
                this.updateForm.message_to_donor = account.message_to_donor;
                this.updateForm.message_to_treasurer = account.message_to_treasurer;
                $('#updateAccountModal').modal('show');
            },
            updateAccount(){
                if (!this.updateForm.name){
                    Swal.fire('Error!', 'Account name is required', 'warning');
                    return;
                }
                if (!this.updateForm.project_uuid){
                    Swal.fire('Error!', 'Project is required', 'warning');
                    return;
                }
                if (!this.updateForm.description){
                    Swal.fire('Error!', 'Description is required', 'warning');
                    return;
                }
                if (!this.updateForm.account_no){
                    Swal.fire('Error!', 'Account no is required', 'warning');
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
                if (!this.updateForm.message_to_donor){
                    Swal.fire('Error!', 'Message to donor is required', 'warning');
                    return;
                }
                if (!this.updateForm.message_to_treasurer){
                    Swal.fire('Error!', 'Message to treasurer is required', 'warning');
                    return;
                }
                const payLoad = {
                    name : this.updateForm.name,
                    description : this.updateForm.description,
                    account_no : this.updateForm.account_no,
                    target_amount : this.updateForm.target_amount,
                    target_date : this.updateForm.target_date,
                    message_to_donor : this.updateForm.message_to_donor,
                    message_to_treasurer : this.updateForm.message_to_treasurer
                };
                this.updateForm.processing = true;
                axios.post(`update-account/${this.updateForm.account_uuid}/${this.updateForm.project_uuid}`, payLoad).then(response => {
                    if (response.data.status){
                        Swal.fire('Success!', response.data.message, 'success');
                        this.updateForm.name = undefined;
                        this.updateForm.description = undefined;
                        this.updateForm.account_no = undefined;
                        this.updateForm.target_amount = undefined;
                        this.updateForm.target_date = undefined;
                        this.updateForm.message_to_donor = undefined;
                        this.updateForm.message_to_treasurer = undefined;
                        $('#updateAccountModal').modal('hide');
                        this.loadProjects();
                        this.loadAccounts();
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
                this.loadAccounts();
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
