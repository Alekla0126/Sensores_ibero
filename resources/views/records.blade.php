@extends('layouts.app')

@section('content')
    <body>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <div class="container-fluid">
            <records-component v-bind:devices='@json($devices)'></records-component>
        </div>
    </body>
@endsection
