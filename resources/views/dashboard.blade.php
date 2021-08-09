@extends('layouts.app')

@section('content')
    <body>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container-fluid">
        <!--------------------------Graph component-------------------------->
        <graphic-component v-bind:device='@json($device)'></graphic-component>
    </div>
    </body>
@endsection
