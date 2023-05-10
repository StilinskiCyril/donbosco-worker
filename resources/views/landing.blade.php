@extends('layouts.master2')
@section('title', 'Marian Shrine Spirituality Center')
@section('content')
    <script>
        $(document).ready(function(){
            $('.toast').toast('show');
        });
    </script>
    <section class="form3 cid-syzQzQiWjz" id="form3-l">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-7 col-12">
                    <div class="image-wrapper">
                        <img class="w-100" src="{{ asset('ui-kit2/assets/images/logo-5ed774920caab-1206x836.png') }}" alt="MSSC">
                    </div>
                </div>
                <div class="col-lg-3 offset-lg-1 mbr-form" data-form-type="formoid">
                    <form action="" method="post" class="mbr-form form-with-styler" data-form-title="Form Name">
                        @csrf
                        @if (count($errors))
                            @foreach($errors->all() as $error)
                                <div class="row">
                                    <div data-form-alert-danger="" class="alert alert-danger col-12">{{ $error }}</div>
                                </div>
                            @endforeach
                        @endif

                        @if ($message = Session::get('success'))
                            <div class="row">
                                <div data-form-alert="" class="alert alert-success col-12">{{ $message }}</div>
                            </div>
                        @endif
                        @if ($message = Session::get('error'))
                            <div class="row">
                                <div data-form-alert-danger="" class="alert alert-danger col-12">{{ $message }}</div>
                            </div>
                        @endif

                        <div class="dragArea row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <h1 class="mbr-section-title mb-4 display-2"><strong>Apply for a Pledge</strong></h1>
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <p class="mbr-text mbr-fonts-style mb-4 display-7">Fill in your details to pledge</p>
                            </div>

                            <div data-for="name" class="col-lg-12 col-md col-sm-12 form-group">
                                <input type="text" name="name" placeholder="Name" data-form-field="name" class="form-control" value="{{ old('name') }}" id="name-form3-l" required>
                            </div>


                            <div data-for="email" class="col-lg-12 col-md col-sm-12 form-group">
                                <input type="email" name="email" placeholder="Email" data-form-field="email" class="form-control" value="{{ old('email') }}" id="email-form3-l" required>
                            </div>

                            <div class="col-lg-12 col-md col-sm-12 form-group" data-for="phone">
                                <input type="number" name="phone" placeholder="Phone" data-form-field="phone" class="form-control" value="{{ old('phone') }}" id="phone-form3-l" required>
                            </div>

                            <div class="col-lg-12 col-md col-sm-12 form-group" data-for="target_amount">
                                <input type="number" name="target_amount" placeholder="Target Amount (KES)" min="50" data-form-field="target_amount" class="form-control" value="{{ old('target_amount') }}" id="target_amount-form3-l" required>
                            </div>

                            <div class="col-lg-12 col-md col-sm-12 form-group" data-for="frequency_amount">
                                <input type="number" name="frequency_amount" placeholder="Frequency Amount (KES)" min="10" data-form-field="frequency_amount" class="form-control" value="{{ old('frequency_amount') }}" id="frequency_amount-form3-l" required>
                            </div>

                            <div class="toast" data-autohide="false">
                                <div class="toast-header">
                                    <strong class="mr-auto text-primary">Frequency Amount</strong>
                                    <small class="text-muted">Mpesa Stk</small>
                                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">&times;</button>
                                </div>
                                <div class="toast-body">
                                    This is the amount to be forwarded via the mpesa stk
                                </div>
                            </div>

                            <div class="col-lg-12 col-md col-sm-12 form-group">
                                <label for="frequency">Frequency</label>
                                <select name="frequency" onchange="ToggleMenu(this.value)" class="form-control" required>
                                    <option value="">---Select---</option>
                                    <option value="0">Once</option>
                                    <option value="1">Daily</option>
                                    <option value="2">Weekly</option>
                                    <option value="3">Monthly</option>
                                </select>
                            </div>

                            <div class="col-lg-12 col-md col-sm-12 form-group hide" id="date">
                                <label for="date">Notification Day (1-28)</label>
                                <select name="date" class="form-control">
                                    <option value="">---Select Day---</option>
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

                            <div class="col-lg-12 col-md col-sm-12 form-group hide" id="day_of_the_week">
                                <label for="day_of_the_week">Day Of The Week</label>
                                <select name="day_of_the_week" class="form-control">
                                    <option value="">---Select Day---</option>
                                    <option value="Sunday">Sunday</option>
                                    <option value="Monday">Monday</option>
                                    <option value="Tuesday">Tuesday</option>
                                    <option value="Wednesday">Wednesday</option>
                                    <option value="Thursday">Thursday</option>
                                    <option value="Friday">Friday</option>
                                    <option value="Saturday">Saturday</option>
                                </select>
                            </div>

                            <div class="col-lg-12 col-md col-sm-12 form-group">
                                <label for="end_date">End Date</label>
                                <input type="date" name="end_date" min="{{ now()->toDateString() }}" class="form-control" required/>
                            </div>

                            <div class="col-md-auto col-12 mbr-section-btn">
                                <button type="submit" class="btn btn-black display-4">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="offset-lg-1"></div>
            </div>
        </div>
    </section>
@endsection
