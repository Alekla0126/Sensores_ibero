@extends('layouts.app')

@section('content')
    <body>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container-fluid">
        <div class="dashboard">
            <div class="grid-container">
                    <graphic-component v-bind:device='@json($device)'></graphic-component>
            </div>
        </div>
    </div>
    </body>
@endsection
