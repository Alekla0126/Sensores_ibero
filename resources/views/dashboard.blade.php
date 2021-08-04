@extends('layouts.app')

@section('content')
    <body>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container-fluid">
        <div class="dashboard">
            <div class="grid-container">
                <div class="grid-item item1">
                    <div class="card">
                        <div class="card-header">{{ __('Gráfica del sensor') }}</div>
                        <div class="card-body">
                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <graphic-component v-bind:device='@json($device)'></graphic-component>
            </div>
        </div>
    </div>
    </body>
@endsection
