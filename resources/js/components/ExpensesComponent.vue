<template>
    <div>
        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <h3>Create New Expense</h3>
                        <form class="row g-3">
                            <div class="col-md-6">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" v-model="createForm.title">
                            </div>
                            <div class="col-md-6">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" v-model="createForm.date">
                            </div>
                            <div class="col-md-12">
                                <label for="amount" class="form-label">Amount (KES)</label>
                                <input type="number" class="form-control" v-model="createForm.amount">
                            </div>
                            <div class="col-md-12">
                                <label for="date" class="form-label">Description</label>
                                <textarea v-model="createForm.description" class="form-control" rows="5"></textarea>
                            </div>
                            <button v-if="createForm.processing" class="btn btn-primary btn-rounded btn-block" type="button" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Processing...
                            </button>
                            <button v-else v-on:click.prevent="createExpense()" type="button" class="btn btn-primary btn-block">Create Expense</button>
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
                                    <th>Expense Title</th>
                                    <th>Date</th>
                                    <th>Amount (KES)</th>
                                    <th>Description</th>
                                    <th colspan="1">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="expense in pagination.data" :key="expense.uuid">
                                    <td>{{ expense.title }}</td>
                                    <td>{{ expense.date }}</td>
                                    <td>{{ expense.amount }}</td>
                                    <td>{{ expense.description }}</td>
                                    <td>
                                        <button @click.prevent="showUpdateExpenseModal(expense)" class="btn btn-primary btn-block"><i class="bx bx-edit"></i> Update Expense</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <ul class="pagination justify-content-center" @paginate="loadExpenses()">
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

        <!-- Update Group Modal -->
        <div class="modal fade" id="updateExpenseModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg bg-light">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="card border-top border-0 border-4 border-primary">
                            <div class="card-body p-5">
                                <form class="row g-3">
                                    <div class="col-md-6">
                                        <label for="title" class="form-label">Title</label>
                                        <input type="text" class="form-control" v-model="updateForm.title">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="date" class="form-label">Date</label>
                                        <input type="date" class="form-control" v-model="updateForm.date">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="amount" class="form-label">Amount (KES)</label>
                                        <input type="number" class="form-control" v-model="updateForm.amount">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="date" class="form-label">Description</label>
                                        <textarea v-model="updateForm.description" class="form-control" rows="5"></textarea>
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
                        <button v-else type="button" class="btn btn-primary" @click="updateExpense()">
                            Update Expense
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
        name: "ExpensesComponent",
        data() {
            return {
                createForm: {
                    title: undefined,
                    date: undefined,
                    amount: undefined,
                    description: undefined,
                    processing: false
                },
                updateForm: {
                    expense_uuid: undefined,
                    title: undefined,
                    date: undefined,
                    amount: undefined,
                    description: undefined,
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
            this.loadExpenses();
        },
        methods: {
            loadExpenses(){
                const payLoad = {
                    sort_by : 'latest'
                };
                this.createForm.processing = true;
                axios.post(`/load-expenses?page=${this.pagination.current_page}`, payLoad).then(response => {
                    this.pagination = response.data;
                }).catch(error => {
                    //
                }).finally(() => {
                    this.createForm.processing = false;
                });
            },
            createExpense(){
                if (!this.createForm.title){
                    Swal.fire('Error!', 'Title is required', 'warning');
                    return;
                }
                if (!this.createForm.date){
                    Swal.fire('Error!', 'Date is required', 'warning');
                    return;
                }
                if (!this.createForm.amount){
                    Swal.fire('Error!', 'Amount is required', 'warning');
                    return;
                }
                if (!this.createForm.description){
                    Swal.fire('Error!', 'Description is required', 'warning');
                    return;
                }
                const payLoad = {
                    title : this.createForm.title,
                    date : this.createForm.date,
                    amount : this.createForm.amount,
                    description : this.createForm.description
                };
                this.createForm.processing = true;
                axios.post(`create-expense`, payLoad).then(response => {
                    if (response.data.status){
                        Swal.fire('Success!', response.data.message, 'success');
                        this.createForm.title = undefined;
                        this.createForm.date = undefined;
                        this.createForm.amount = undefined;
                        this.createForm.description = undefined;
                        this.loadExpenses();
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
            showUpdateExpenseModal(expense){
                this.updateForm.expense_uuid = expense.uuid;
                this.updateForm.title = expense.title;
                this.updateForm.date = expense.date;
                this.updateForm.amount = expense.amount;
                this.updateForm.description = expense.description;
                $('#updateExpenseModal').modal('show');
            },
            updateExpense(){
                if (!this.updateForm.title){
                    Swal.fire('Error!', 'Title is required', 'warning');
                    return;
                }
                if (!this.updateForm.date){
                    Swal.fire('Error!', 'Date is required', 'warning');
                    return;
                }
                if (!this.updateForm.amount){
                    Swal.fire('Error!', 'Amount is required', 'warning');
                    return;
                }
                if (!this.updateForm.description){
                    Swal.fire('Error!', 'Description is required', 'warning');
                    return;
                }
                const payLoad = {
                    title : this.updateForm.title,
                    date : this.updateForm.date,
                    amount : this.updateForm.amount,
                    description : this.updateForm.description
                };
                this.updateForm.processing = true;
                axios.post(`update-expense/${this.updateForm.expense_uuid}`, payLoad).then(response => {
                    if (response.data.status){
                        Swal.fire('Success!', response.data.message, 'success');
                        this.updateForm.title = undefined;
                        this.updateForm.date = undefined;
                        this.updateForm.amount = undefined;
                        this.updateForm.description = undefined;
                        $('#updateExpenseModal').modal('hide');
                        this.loadExpenses();
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
                this.loadExpenses();
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
