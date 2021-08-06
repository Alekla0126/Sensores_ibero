@extends('layouts.app')

@section('content')
    <body>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <div class="container-fluid">
            <!--------------------Table component-------------------------->
            <table-devices v-bind:devices='@json($devices)'></table-devices>
        </div>
    </body>
    <style>
        body{
            background-color: #282A36;
        }
    </style>
@endsection
