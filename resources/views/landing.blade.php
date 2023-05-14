@extends('layouts.master2')
@section('title', 'Marian Shrine Spirituality Center')
@section('content')
    <script>
        $(document).ready(function(){
            $('.toast').toast('show');
        });
    </script>
    <div id="app">
        <landing-component></landing-component>
    </div>
@endsection
