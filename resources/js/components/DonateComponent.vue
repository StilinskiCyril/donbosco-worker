<template>
    <div>
        <section class="form3 cid-syzQzQiWjz" id="form3-l">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-7 col-12">
                        <div class="image-wrapper">
                            <img class="w-100" :src="'ui-kit2/assets/images/logo-5ed774920caab-1206x836.png'" alt="MSSC Logo">
                        </div>
                    </div>
                    <div class="col-lg-3 offset-lg-1 mbr-form">
                        <form class="mbr-form form-with-styler">
                            <div class="dragArea row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <h1 class="mbr-section-title mb-4 display-2"><strong>Donate Now</strong></h1>
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <p class="mbr-text mbr-fonts-style mb-4 display-7">Fill in your details to donate</p>
                                </div>

                                <div class="col-lg-12 col-md col-sm-12 form-group">
                                    <label>Select Payment Mode</label>
                                    <select v-model="createForm.payment_mode" class="form-control">
                                        <option value="mpesa">M-pesa</option>
                                        <option value="paypal">PayPal</option>
                                        <option value="card">Card</option>
                                    </select>
                                </div>

                                <div class="col-lg-12 col-md col-sm-12 form-group" v-if="createForm.payment_mode === 'mpesa'">
                                    <input type="text" v-model="createForm.msisdn" placeholder="Enter Safaricom M-pesa Phone/ Msisdn" class="form-control">
                                </div>

                                <div class="col-lg-12 col-md col-sm-12 form-group" v-if="createForm.payment_mode === 'card'">
                                    <input type="text" disabled placeholder="Card Integration Coming Soon..." class="form-control">
                                </div>

                                <div class="col-lg-12 col-md col-sm-12 form-group">
                                    <label>Select Account</label>
                                    <select v-model="createForm.account_no" class="form-control">
                                        <option :value="account.account_no" v-for="account in accounts.data">{{ account.name }}</option>
                                    </select>
                                </div>

                                <div class="col-lg-12 col-md col-sm-12 form-group">
                                    <input type="number" v-model="createForm.amount" placeholder="Amount (KES)" class="form-control">
                                </div>

                                <div class="col-md-auto col-12 mbr-section-btn">
                                    <button v-if="createForm.processing" class="btn btn-primary display-4" type="button" disabled>
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Processing...
                                    </button>
                                    <button v-else v-on:click.prevent="donateNow()" type="button" class="btn btn-black display-4">Donate Now</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="offset-lg-1"></div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import Swal from "sweetalert2";

export default {
        name: "DonateComponent",
        data() {
            return {
                accounts: {},
                createForm: {
                    payment_mode: 'mpesa',
                    msisdn: undefined,
                    account_no: undefined,
                    amount: undefined,
                    processing: false
                }
            }
        },
    mounted() {
        this.loadAccounts();
    },
    methods: {
        loadAccounts(){
            const payLoad = {
                sort_by: 'latest'
            };
            this.createForm.processing = true;
            axios.post(`/load-accounts-without-project`, payLoad).then(response => {
                this.accounts = response.data;
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
        donateNow(){
            if (!this.createForm.payment_mode){
                Swal.fire('Error!', 'Payment mode is required', 'warning');
                return;
            }
            if (this.createForm.payment_mode === 'card'){
                Swal.fire('Error!', 'Card Payment Integration Coming Soon...', 'warning');
                return;
            }
            if (this.createForm.payment_mode === 'mpesa' && !this.createForm.msisdn){
                Swal.fire('Error!', 'Phone/ Msisdn is required if payment mode is mpesa', 'warning');
                return;
            }
            if (!this.createForm.account_no){
                Swal.fire('Error!', 'Account No is required', 'warning');
                return;
            }
            if (!this.createForm.amount){
                Swal.fire('Error!', 'Amount is required', 'warning');
                return;
            }
            if (this.createForm.amount < 5){
                Swal.fire('Error!', 'Amount should be more than 5 KES', 'warning');
                return;
            }
            const payLoad = {
                payment_mode: this.createForm.payment_mode,
                msisdn: this.createForm.msisdn,
                account_no: this.createForm.account_no,
                amount: this.createForm.amount,
            };
            this.createForm.processing = true;
            axios.post(`/donate-now`, payLoad).then(response => {
                if (response.data.status){
                    Swal.fire('Success!', response.data.message, 'success');
                    this.createForm.payment_mode = 'mpesa';
                    this.createForm.msisdn = undefined;
                    this.createForm.account_no = undefined;
                    this.createForm.amount = undefined;
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
        }
        }
    }
</script>
