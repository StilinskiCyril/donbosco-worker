<template>
    <div>
        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <h3>Filter Donations</h3>
                        <form class="row g-3">
                            <div class="col-md-12">
                                <label for="frequency" class="form-label">Frequency</label>
                                <select class="form-control" v-model="filterForm.frequency">
                                    <option value="0">Once</option>
                                    <option value="1">Daily</option>
                                    <option value="2">Weekly</option>
                                    <option value="3">Monthly</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="payment_status" class="form-label">Payment Status</label>
                                <select class="form-control" v-model="filterForm.payment_status">
                                    <option value="0">Pending</option>
                                    <option value="1">Completed</option>
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
                                    <th>Name</th>
                                    <th>Msisdn/ Phone</th>
                                    <th>Email</th>
                                    <th>Target Amount (KES)</th>
                                    <th>Target Date</th>
                                    <th>Frequency</th>
                                    <th>Frequency Amount</th>
                                    <th>Last Alert Time</th>
                                    <th>Account No</th>
                                    <th>Amount Donated</th>
                                    <th>Balance</th>
                                    <th>Payment Status</th>
                                    <th colspan="1">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="pledge in pagination.data" :key="pledge.uuid">
                                    <td>{{ pledge.name }}</td>
                                    <td>{{ pledge.msisdn }}</td>
                                    <td>{{ pledge.email }}</td>
                                    <td>{{ pledge.target_amount }}</td>
                                    <td>{{ pledge.target_date }}</td>
                                    <td>{{ pledge.frequency }}</td>
                                    <td>{{ pledge.frequency_amount }}</td>
                                    <td>{{ pledge.last_alert_time }}</td>
                                    <td>{{ pledge.account_no }}</td>
                                    <td>{{ pledge.amount_donated }}</td>
                                    <td>{{ pledge.balance }}</td>
                                    <td v-if="pledge.payment_status"><span class="badge bg-gradient-quepal text-white shadow-sm w-100">Fully Paid</span></td>
                                    <td v-else><span class="badge bg-gradient-bloody text-white shadow-sm w-100">Pending</span></td>
                                    <td>
                                        <a :href="'/pledge-donations/'+pledge.uuid">
                                            <button class="btn btn-primary btn-block"><i class="bx bx-credit-card"></i> View Payment History</button>
                                        </a>
                                    </td>
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
        name: "PledgeDonationsReportComponent",
        data() {
            return {
                projects: {},
                accounts: {},
                filterForm: {
                    frequency: undefined,
                    payment_status: undefined,
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
        methods: {
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
                    frequency: this.filterForm.frequency,
                    payment_status: this.filterForm.payment_status,
                    start: this.filterForm.start,
                    end: this.filterForm.end
                };
                this.filterForm.processing = true;
                axios.post(`generate-pledge-donations-report?page=${this.pagination.current_page}`, payLoad).then(response => {
                    this.pagination = response.data;
                }).catch(error => {
                    //
                }).finally(() => {
                    this.filterForm.processing = false;
                });
            },
            clearFilter(){
                this.filterForm.processing = true;
                this.filterForm.frequency = undefined;
                this.filterForm.payment_status = undefined;
                this.filterForm.start = undefined;
                this.filterForm.end = undefined;
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
