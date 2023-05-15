@extends('layouts.master2')
@section('title', env('APP_NAME'). ' | Donate Now')
@section('content')
    <div id="app">
        <donate-component></donate-component>
    </div>
@endsection
