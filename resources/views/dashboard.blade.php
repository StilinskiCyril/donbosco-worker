@extends('admin.layouts.master')
@section('title', 'Dashboard')
@section('content')
    <div id="app">
        @role('super-admin')
        <super-admin-dashboard-component></super-admin-dashboard-component>
        @endrole
        @role('admin')
        <admin-dashboard-component></admin-dashboard-component>
        @endrole
        @role('treasurer')
        <treasurer-dashboard-component></treasurer-dashboard-component>
        @endrole
        @role('user')
        <user-dashboard-component></user-dashboard-component>
        @endrole
    </div>
@endsection
