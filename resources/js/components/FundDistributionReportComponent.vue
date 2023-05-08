<template>
    <div>
        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <h3>Filter Fund Distribution</h3>
                        <form class="row g-3">
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
                            <button v-else v-on:click.prevent="filterReport()" type="button" class="btn btn-primary btn-block">Filter</button>
                            <button v-on:click.prevent="clearFilter()" type="button" class="btn btn-danger btn-block">Clear Filter</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <table class="table-striped table-bordered" style="width:100%">
                            <tr>
                                <th>From:</th>
                                <td>{{ stats.start }}</td>
                            </tr>
                            <tr>
                                <th>To:</th>
                                <td>{{ stats.to }}</td>
                            </tr>
                            <tr>
                                <th>Total Collected:</th>
                                <td>{{ stats.total_collected }}</td>
                            </tr>
                            <tr>
                                <th>Charges:</th>
                                <td>{{ stats.charges }}</td>
                            </tr>
                            <tr>
                                <th>Net Collected:</th>
                                <td>{{ stats.net_collected }}</td>
                            </tr>
                            <tr>
                                <th>Bitwise Revenue Share:</th>
                                <td>{{ stats.bitwise_revenue_share }}</td>
                            </tr>
                            <tr>
                                <th>Expenses:</th>
                                <td>{{ stats.expenses }}</td>
                            </tr>
                            <tr>
                                <th>Net Amount:</th>
                                <td>{{ stats.net_amount }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Swal from "sweetalert2";
    export default {
        name: "FundDistributionReportComponent",
        data() {
            return {
                stats: {
                    total_collected: 0,
                    charges: 0,
                    net_collected: 0,
                    bitwise_revenue_share: 0,
                    expenses: 0,
                    net_amount: 0,
                    start: undefined,
                    end: undefined
                },
                filterForm: {
                    start: undefined,
                    end: undefined,
                    processing: false
                }
            }
        },
        methods: {
            filterReport(){
                if (!this.filterForm.start){
                    Swal.fire('Error!', 'Start is required', 'warning');
                    return;
                }
                if (!this.filterForm.end){
                    Swal.fire('Error!', 'End is required', 'warning');
                    return;
                }
                const payLoad = {
                    start: this.filterForm.start,
                    end: this.filterForm.end
                };
                this.filterForm.processing = true;
                axios.post(`generate-fund-distribution-report`, payLoad).then(response => {
                    this.stats.total_collected = response.data.total_collected;
                    this.stats.charges = response.data.charges;
                    this.stats.net_collected = response.data.net_collected;
                    this.stats.bitwise_revenue_share = response.data.bitwise_revenue_share;
                    this.stats.expenses = response.data.expenses;
                    this.stats.net_amount = response.data.net_amount;
                    this.stats.start = response.data.start;
                    this.stats.end = response.data.end;
                }).catch(error => {
                    //
                }).finally(() => {
                    this.filterForm.processing = false;
                });
            },
            clearFilter(){
                this.filterForm.processing = true;
                this.filterForm.start = undefined;
                this.filterForm.end = undefined;
                this.filterForm.processing = false;
            }
        }
    }
</script>
