<template>
    <div>
        <div class="row">
            <div class="col-xl-7 mx-auto">
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <h3>Filter Unknown M-pesa Donations</h3>
                        <form class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" v-model="filterForm.name">
                            </div>
                            <div class="col-md-6">
                                <label for="msisdn" class="form-label">Msisdn/Phone</label>
                                <input type="text" class="form-control" v-model="filterForm.msisdn">
                            </div>
                            <div class="col-md-12">
                                <label for="trans_id" class="form-label">Transaction ID</label>
                                <input type="text" class="form-control" v-model="filterForm.trans_id">
                            </div>
                            <div class="col-6">
                                <label for="start" class="form-label">Start</label>
                                <input type="date" class="form-control" v-model="filterForm.start">
                            </div>
                            <div class="col-6">
                                <label for="end" class="form-label">End</label>
                                <input type="date" class="form-control" v-model="filterForm.end">
                            </div>
                            <button v-if="filterForm.processing" class="btn btn-primary btn-rounded btn-block" type="button" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Processing...
                            </button>
                            <button v-else v-on:click.prevent="loadUnknownDonations()" type="button" class="btn btn-primary btn-block">Filter Unknown Donations</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-5 mx-auto">
                <div class="card border-top border-0 border-4 border-success">
                    <div class="card-body p-5">
                        <h3>M-pesa Reconciliation</h3>
                        <form class="row g-3">
                            <div class="col-md-12">
                                <label for="title" class="form-label">Upload M-pesa Statement</label>
                                <input type="file" class="form-control" v-on:change="onFileChange" name="uploaded_file">
                            </div>
                            <button v-if="mpesaStatementForm.processing" class="btn btn-primary btn-rounded btn-block" type="button" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Processing...
                            </button>
                            <button v-else v-on:click.prevent="uploadMpesaStatement()" type="button" class="btn btn-success btn-block">Upload M-pesa Statement</button>
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
                                    <th>Trans ID</th>
                                    <th>Msisdn/Phone</th>
                                    <th>Account No</th>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Amount (KES)</th>
                                    <th colspan="1">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="donation in pagination.data" :key="donation.uuid">
                                    <td>{{ donation.trans_id }}</td>
                                    <td>{{ donation.msisdn }}</td>
                                    <td>{{ donation.account_no }}</td>
                                    <td>{{ donation.created_at }}</td>
                                    <td>{{ donation.name }}</td>
                                    <td>{{ donation.amount }}</td>
                                    <td>
                                        <button @click.prevent="showAssignDonationToUserModal(donation)" class="btn btn-primary btn-block"><i class="bx bx-edit"></i> Assign To User</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <ul class="pagination justify-content-center" @paginate="loadUnknownDonations()">
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
        name: "DonorsComponent",
        data() {
            return {
                filterForm: {
                    name: undefined,
                    msisdn: undefined,
                    trans_id: undefined,
                    start: undefined,
                    end: undefined,
                    processing: false
                },
                mpesaStatementForm: {
                    uploaded_file: undefined,
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
            this.loadUnknownDonations();
        },
        methods: {
            loadUnknownDonations(){
                const payLoad = {
                    sort_by : 'latest',
                    channel : 'mpesa',
                    name : this.filterForm.name,
                    msisdn : this.filterForm.msisdn,
                    trans_id : this.filterForm.trans_id,
                    start : this.filterForm.start,
                    end : this.filterForm.end
                };
                this.filterForm.processing = true;
                axios.post(`/load-unknown-donations?page=${this.pagination.current_page}`, payLoad).then(response => {
                    this.pagination = response.data;
                }).catch(error => {
                    //
                }).finally(() => {
                    this.filterForm.processing = false;
                });
            },
            onFileChange(event){
                this.mpesaStatementForm.uploaded_file = event.target.files[0];
            },
            uploadMpesaStatement(){
                if (!this.mpesaStatementForm.uploaded_file){
                    Swal.fire('Error!', 'M-pesa statement is required', 'warning');
                    return;
                }
                let payLoad = new FormData();
                payLoad.append("uploaded_file", this.mpesaStatementForm.uploaded_file);
                this.mpesaStatementForm.processing = true;
                axios.post(`upload-mpesa-statement`, payLoad).then(response => {
                    if (response.data.status){
                        Swal.fire('Success!', response.data.message, 'success');
                        this.mpesaStatementForm.uploaded_file = undefined;
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
                    this.mpesaStatementForm.processing = false;
                });
            },
            showAssignDonationToUserModal(donation){

            },
            changePage(page) {
                this.pagination.current_page = page;
                this.loadUnknownDonations();
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
