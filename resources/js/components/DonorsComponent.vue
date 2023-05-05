<template>
    <div>
        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <h3>Filter Donors</h3>
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
                                <label for="account_no" class="form-label">First Time Donation Account No</label>
                                <input type="text" class="form-control" v-model="filterForm.account_no">
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
                            <button v-else v-on:click.prevent="loadDonors()" type="button" class="btn btn-primary btn-block">Filter Donors</button>
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
                                    <th>First Donation Date</th>
                                    <th>First Donation Account No</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="donor in pagination.data" :key="donor.uuid">
                                    <td>{{ donor.name }}</td>
                                    <td>{{ donor.msisdn }}</td>
                                    <td>{{ donor.created_at }}</td>
                                    <td>{{ donor.account_no }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <ul class="pagination justify-content-center" @paginate="loadDonors()">
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
export default {
        name: "DonorsComponent",
        data() {
            return {
                filterForm: {
                    name: undefined,
                    msisdn: undefined,
                    account_no: undefined,
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
                offset: 10,
            }
        },
        mounted() {
            this.loadDonors();
        },
        methods: {
            loadDonors(){
                const payLoad = {
                    sort_by : 'latest',
                    name : this.filterForm.name,
                    msisdn : this.filterForm.msisdn,
                    account_no : this.filterForm.account_no,
                    start : this.filterForm.start,
                    end : this.filterForm.end
                };
                this.filterForm.processing = true;
                axios.post(`/load-donors?page=${this.pagination.current_page}`, payLoad).then(response => {
                    this.pagination = response.data;
                }).catch(error => {
                    //
                }).finally(() => {
                    this.filterForm.processing = false;
                });
            },
            changePage(page) {
                this.pagination.current_page = page;
                this.loadDonors();
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
