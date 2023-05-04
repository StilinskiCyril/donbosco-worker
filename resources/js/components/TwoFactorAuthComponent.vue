<template>
    <div class="d-flex flex-column-fluid">
        <div class="container">
            <div class="card card-custom gutter-b">
                <div class="card-body">
                    <p class="text-warning">Kindly take note of the following;</p>
                    <ul>
                        <li>We usually charge client account for every OTP message sent out</li>
                        <li>Only one OTP message can be sent within a 5-minute interval</li>
                    </ul>
                    <form>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Resend OTP</label>
                            <div class="col-md-9">
                                <button v-if="otpSendForm.processing" class="btn btn-success" type="button" disabled>
                                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                                    <span class="sr-only">Sending OTP...</span>
                                </button>
                                <button v-else @click.prevent="sendOtp" class="btn btn-primary btn-block">Resend OTP</button>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Enter OTP</label>
                            <div class="col-md-9">
                                <input id="edit-group-name" type="text" class="form-control rounded-md" v-model="otpVerifyForm.otp" placeholder="Enter OTP Here...">
                            </div>
                            <span class="form-text text-muted">This will be sent to the mobile number registered to your account. Check your inbox.</span>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3"></div>
                            <div class="col-md-9">
                                <button v-if="otpVerifyForm.processing" class="btn btn-success" type="button" disabled>
                                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                                    <span class="sr-only">Verifying OTP...</span>
                                </button>
                                <button v-else @click.prevent="verifyOtp" type="button" class="btn btn-primary btn-block">Verify OTP</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Swal from 'sweetalert2/dist/sweetalert2.js'
export default {
    name: "TwoFactorAuth",
    data() {
        return {
            otpSendForm:{
                processing: false
            },
            otpVerifyForm:{
                otp: undefined,
                processing: false
            }
        }
    },
    methods: {
        sendOtp() {
            this.otpSendForm.processing = true;
            axios.post('/send-login-otp').then(response => {
                if (response.data.status){
                    Swal.fire('Success!', response.data.message, 'success');
                } else {
                    Swal.fire('Error!', response.data.message, 'error');
                }
            }).catch(error => {
                if (error && error.response && typeof error.response.data === 'object') {
                    Swal.fire('Error', error.response.data.message, 'warning');
                } else {
                    Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                }
            }).finally(() => {
                this.otpSendForm.processing = false;
            });
        },
        verifyOtp(){
            this.errors = [];
            if (!this.otpVerifyForm.otp) {
                this.errors.push('OTP is required.')
            }
            let payload = {
                otp: this.otpVerifyForm.otp
            };
            if (this.errors.length > 0) {
                Swal.fire('Error', this.errors.join(','), 'error');
                return;
            }
            this.otpVerifyForm.processing = true;
            axios.post('/verify-login-otp', payload).then(response => {
                if (response.data.status){
                    this.otpVerifyForm.otp = '';
                    Swal.fire('Success!', response.data.message, 'success');
                    // redirect to homepage
                    setTimeout(function(){
                        window.location.href = "https://sms.mobilesasa.com";
                    }, 3000);
                } else {
                    Swal.fire('Error!', response.data.message, 'error');
                }
            }).catch(error => {
                if (error && error.response && typeof error.response.data === 'object') {
                    Swal.fire('Error', error.response.data.message, 'danger');
                } else {
                    Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                }
            }).finally(() => {
                this.otpVerifyForm.processing = false;
            });
        }
    },
}
</script>

<style scoped>

</style>
