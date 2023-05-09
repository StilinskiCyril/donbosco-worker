@extends('layouts.master')
@section('title', 'Pledge Donations History')
@section('content')
    <div id="app">
        <div class="page-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-xl-10 mx-auto">
                        <div class="card border-top border-0 border-4 border-primary">
                            <div class="card-body p-5">
                                <h3>Pledge Donations For {{ $pledge->name }}</h3>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Receipt No</th>
                                            <th>Name</th>
                                            <th>Msisdn/ Phone</th>
                                            <th>Amount (Ksh)</th>
                                            <th>Account No</th>
                                            <th>Trans Date</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($donations as $donation)
                                            <tr>
                                                <td>{{ $donation->trans_id }}</td>
                                                <td>{{ $donation->name }}</td>
                                                <td>{{ $donation->msisdn }}</td>
                                                <td>{{ $donation->amount }}</td>
                                                <td>{{ $donation->account_no }}</td>
                                                <td>{{ $donation->trans_time }}</td>
                                            </tr>
                                        @empty
                                            No records found
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            {{ $donations->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
