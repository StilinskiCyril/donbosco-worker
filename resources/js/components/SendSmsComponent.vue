<template>
    <div>
        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <h3>Send SMS</h3>
                        <form class="row g-3">
                            <div class="col-md-12">
                                <label for="channel" class="form-label">Channel/ Send To</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="form-check form-check-success">
                                        <input class="form-check-input" type="radio" v-model="sendSmsForm.channel" value="specific-user">
                                        <label class="form-check-label">
                                            Specific User
                                        </label>
                                    </div>
                                    <div class="form-check form-check-danger">
                                        <input class="form-check-input" type="radio" v-model="sendSmsForm.channel" value="pledged-users">
                                        <label class="form-check-label">
                                            Pledged Users
                                        </label>
                                    </div>
                                    <div class="form-check form-check-warning">
                                        <input class="form-check-input" type="radio" v-model="sendSmsForm.channel" value="all-donors">
                                        <label class="form-check-label">
                                            All Donors
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" v-if="sendSmsForm.channel === 'specific-user'">
                                <label for="msisdn" class="form-label">Msisdn/ Phone</label>
                                <input type="text" class="form-control" v-model="sendSmsForm.msisdn">
                            </div>
                            <div class="col-md-12">
                                <label for="message" class="form-label">Message</label>
                                <textarea v-model="sendSmsForm.message" class="form-control" rows="5"></textarea>
                            </div>
                            <button v-if="sendSmsForm.processing" class="btn btn-primary btn-rounded btn-block" type="button" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Processing...
                            </button>
                            <button v-else v-on:click.prevent="sendSms()" type="button" class="btn btn-primary btn-block">Send SMS</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Swal from "sweetalert2";

export default {
        name: "SendSmsComponent",
        data() {
            return {
                sendSmsForm: {
                    channel: 'specific-user',
                    msisdn: undefined,
                    message: undefined,
                    processing: false
                }
            }
        },

        methods: {
            sendSms(){
                if (!this.sendSmsForm.channel){
                    Swal.fire('Error!', 'Channel is required', 'warning');
                    return;
                }
                if (this.sendSmsForm.channel === 'specific-user' && !this.sendSmsForm.msisdn){
                    Swal.fire('Error!', 'Msisdn/ Phone is required if channel is specific-user', 'warning');
                    return;
                }
                if (!this.sendSmsForm.message){
                    Swal.fire('Error!', 'Message is required', 'warning');
                    return;
                }
                const payLoad = {
                    channel : this.sendSmsForm.channel,
                    msisdn : this.sendSmsForm.msisdn,
                    message : this.sendSmsForm.message
                };
                this.sendSmsForm.processing = true;
                axios.post(`/send-sms`, payLoad).then(response => {
                    if (response.data.status){
                        Swal.fire('Success!', response.data.message, 'success');
                        this.sendSmsForm.channel = 'specific-user';
                        this.sendSmsForm.msisdn = undefined;
                        this.sendSmsForm.message = undefined;
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
                    this.sendSmsForm.processing = false;
                });
            }
        }
    }
</script>
