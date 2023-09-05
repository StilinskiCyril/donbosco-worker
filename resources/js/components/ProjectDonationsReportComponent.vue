<template>
    <div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Project Name</th>
                                    <th>Project Target</th>
                                    <th>Amount Donated</th>
                                    <th>Balance</th>
                                    <th>% Donations Against Project Target</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="project in reports" :key="project.uuid">
                                    <td>{{ project.name }}</td>
                                    <td>{{ project.target_amount }}</td>
                                    <td>{{ project.amount_donated }}</td>
                                    <td>{{ project.balance }}</td>
                                    <td>{{ (project.amount_donated/project.target_amount) * 100 }} %</td>
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
        name: "ProjectDonationsReportComponent",
        data() {
            return {
                reports: {},
                filterForm: {
                    processing: false
                }
            }
        },
        mounted() {
            this.filterReport();
        },
        methods: {
            filterReport(){
                this.filterForm.processing = true;
                axios.post(`generate-project-donations-report`).then(response => {
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
