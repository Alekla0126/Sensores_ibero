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
                            Bienvenido de vuelta
                        </div>
                    </div>
                </div>
                <script>
                    window.Laravel.csrfToken = {{ csrf_token() }};
                </script>
                <example-component></example-component>
            </div>
        </div>
    </div>
    </body>
@endsection
