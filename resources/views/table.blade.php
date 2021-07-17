@extends('layouts.app')

@section('content')
    <body>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <div class="container-fluid">
            <table-devices v-bind:devices='@json($devices)'></table-devices>
        </div>
    </body>
@endsection
