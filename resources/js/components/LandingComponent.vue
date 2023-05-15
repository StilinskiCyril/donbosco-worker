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
                                    <h1 class="mbr-section-title mb-4 display-2"><strong>Apply for a Pledge</strong></h1>
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <p class="mbr-text mbr-fonts-style mb-4 display-7">Fill in your details to pledge</p>
                                </div>

                                <div class="col-lg-12 col-md col-sm-12 form-group">
                                    <input type="text" v-model="createForm.name" placeholder="Name" class="form-control">
                                </div>

                                <div class="col-lg-12 col-md col-sm-12 form-group">
                                    <input type="text" v-model="createForm.msisdn" placeholder="Phone" class="form-control">
                                </div>

                                <div class="col-lg-12 col-md col-sm-12 form-group">
                                    <input type="email" v-model="createForm.email" placeholder="Email" class="form-control">
                                </div>

                                <div class="col-lg-12 col-md col-sm-12 form-group">
                                    <input type="number" v-model="createForm.target_amount" placeholder="Target Amount (KES)" class="form-control">
                                </div>

                                <div class="col-lg-12 col-md col-sm-12 form-group">
                                    <input type="number" v-model="createForm.frequency_amount" placeholder="Frequency Amount (KES)" class="form-control">
                                </div>

                                <div class="toast" data-autohide="false">
                                    <div class="toast-header">
                                        <strong class="mr-auto text-primary">Frequency Amount</strong>
                                        <small class="text-muted">M-pesa Stk Prompt</small>
                                        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">&times;</button>
                                    </div>
                                    <div class="toast-body">
                                        This is the amount to be forwarded via the mpesa stk
                                    </div>
                                </div>

                                <div class="col-lg-12 col-md col-sm-12 form-group">
                                    <label>Frequency</label>
                                    <select v-model="createForm.frequency" class="form-control">
                                        <option value="0">Once</option>
                                        <option value="1">Daily</option>
                                        <option value="2">Weekly</option>
                                        <option value="3">Monthly</option>
                                    </select>
                                </div>

                                <div class="col-lg-12 col-md col-sm-12 form-group" v-if="createForm.frequency === '2'">
                                    <label>Day Of The Week</label>
                                    <select v-model="createForm.day_of_the_week" class="form-control">
                                        <option value="Sunday">Sunday</option>
                                        <option value="Monday">Monday</option>
                                        <option value="Tuesday">Tuesday</option>
                                        <option value="Wednesday">Wednesday</option>
                                        <option value="Thursday">Thursday</option>
                                        <option value="Friday">Friday</option>
                                        <option value="Saturday">Saturday</option>
                                    </select>
                                </div>

                                <div class="col-lg-12 col-md col-sm-12 form-group" v-if="createForm.frequency === '3'">
                                    <label>Notification Day (1-28)</label>
                                    <select v-model="createForm.notification_day" class="form-control">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                        <option value="11">11</option>
                                        <option value="12">12</option>
                                        <option value="13">13</option>
                                        <option value="14">14</option>
                                        <option value="15">15</option>
                                        <option value="16">16</option>
                                        <option value="17">17</option>
                                        <option value="18">18</option>
                                        <option value="19">19</option>
                                        <option value="20">20</option>
                                        <option value="21">21</option>
                                        <option value="22">22</option>
                                        <option value="23">23</option>
                                        <option value="24">24</option>
                                        <option value="25">25</option>
                                        <option value="26">26</option>
                                        <option value="27">27</option>
                                        <option value="28">28</option>
                                    </select>
                                </div>

                                <div class="col-lg-12 col-md col-sm-12 form-group">
                                    <label>Target Date</label>
                                    <input type="date" v-model="createForm.target_date" class="form-control"/>
                                </div>

                                <div class="col-md-auto col-12 mbr-section-btn">
                                    <button v-if="createForm.processing" class="btn btn-primary display-4" type="button" disabled>
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Processing...
                                    </button>
                                    <button v-else v-on:click.prevent="createPledge()" type="button" class="btn btn-black display-4">Create Pledge</button>
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
        name: "LandingComponent",
        data() {
            return {
                createForm: {
                    name: undefined,
                    msisdn: undefined,
                    email: undefined,
                    target_amount: undefined,
                    frequency_amount: undefined,
                    frequency: undefined,
                    day_of_the_week: undefined,
                    notification_day: undefined,
                    target_date: undefined,
                    processing: false
                }
            }
        },
        methods: {
            createPledge(){
                if (!this.createForm.name){
                    Swal.fire('Error!', 'Name is required', 'warning');
                    return;
                }
                if (!this.createForm.msisdn){
                    Swal.fire('Error!', 'Msisdn/ Phone is required if channel is specific-user', 'warning');
                    return;
                }
                if (!this.createForm.email){
                    Swal.fire('Error!', 'Message is required', 'warning');
                    return;
                }
                if (!this.createForm.target_amount){
                    Swal.fire('Error!', 'Target amount is required', 'warning');
                    return;
                }
                if (this.createForm.target_amount < 50){
                    Swal.fire('Error!', 'Target amount should be more than 50 KES', 'warning');
                    return;
                }
                if (!this.createForm.frequency_amount){
                    Swal.fire('Error!', 'Frequency amount is required', 'warning');
                    return;
                }
                if (this.createForm.frequency_amount < 10){
                    Swal.fire('Error!', 'Frequency amount should be more than 10 KES', 'warning');
                    return;
                }
                if (this.createForm.frequency_amount > this.createForm.target_amount){
                    Swal.fire('Error!', 'Frequency amount should be less than Target amount', 'warning');
                    return;
                }
                if (!this.createForm.frequency){
                    Swal.fire('Error!', 'Frequency is required', 'warning');
                    return;
                }
                if (this.createForm.frequency === '2' && !this.createForm.day_of_the_week){
                    Swal.fire('Error!', 'Day of the weak is required if frequency is weakly', 'warning');
                    return;
                }
                if (this.createForm.frequency === '3' && !this.createForm.notification_day){
                    Swal.fire('Error!', 'Notification day is required if frequency is monthly', 'warning');
                    return;
                }
                if (!this.createForm.target_date){
                    Swal.fire('Error!', 'Target date is required', 'warning');
                    return;
                }
                const payLoad = {
                    name: this.createForm.name,
                    msisdn: this.createForm.msisdn,
                    email: this.createForm.email,
                    target_amount: this.createForm.target_amount,
                    frequency_amount: this.createForm.frequency_amount,
                    frequency: this.createForm.frequency,
                    day_of_the_week: this.createForm.day_of_the_week,
                    notification_day: this.createForm.notification_day,
                    target_date: this.createForm.target_date
                };
                this.createForm.processing = true;
                axios.post(`/create-pledge`, payLoad).then(response => {
                    if (response.data.status){
                        Swal.fire('Success!', response.data.message, 'success');
                        this.createForm.name = undefined;
                        this.createForm.msisdn = undefined;
                        this.createForm.email = undefined;
                        this.createForm.target_amount = undefined;
                        this.createForm.frequency_amount = undefined;
                        this.createForm.frequency = undefined;
                        this.createForm.day_of_the_week = undefined;
                        this.createForm.notification_day = undefined;
                        this.createForm.target_date = undefined;
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
