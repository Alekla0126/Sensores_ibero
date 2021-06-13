@extends('layouts.app')

@section('content')

    <style>
        .grid-container {
            display: grid;
            grid-gap: 20px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        @media only screen and (max-width: 765px) {
            .grid-container {
                grid-template-columns: 1fr;
            }
        }

        @media only screen and (max-width: 614px) {
            .grid-container {
                grid-template-columns: 100%;
            }

            .grid-item {
                grid-row-start: initial !important;
                grid-row-end: initial !important;
                grid-column-start: initial !important;
                grid-column-end: initial !important;
            }
        }

        .grid-item {
            width: 100%;
            height: 100%;
        }

        .item0 {
            grid-row-start: 1;
            grid-row-end: 3;
            grid-column-start: 3;
            grid-column-end: 4;
        }

        .item1 {
            grid-row-start: 1;
            grid-row-end: 1;
            grid-column-start: 1;
            grid-column-end: 3;
        }

        .item2 {
            grid-row-start: 2;
            grid-row-end: 2;
            grid-column-start: 1;
            grid-column-end: 3;
        }

        .item3 {
            grid-row-start: 3;
            grid-row-end: 3;
            grid-column-start: 1;
            grid-column-end: 4;
        }
    </style>
    <body>
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

                <div class="grid-item item3">
                    <div class="card">
                        <div class="card-header">Temperatura<span
                                class="badge badge-info float-right">Salon: A205</span></div>

                        <div class="card-body">
                            <canvas id="bar-chart" class="chartjs"></canvas>
                            <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
                            <script>
                                let chart = new Chart(document.getElementById('bar-chart'), {
                                    'type': 'line',
                                    'data': {
                                        'labels': ['10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00'],
                                        'datasets': [
                                            {
                                                'label': 'Temperature',
                                                'data': [65, 59, 80, 81, 56, 55, 40],
                                                'fill': false,
                                                'borderColor': 'rgb(75, 192, 192)',
                                                'lineTension': 0.1,
                                            }],
                                    },
                                    'options': {},
                                });

                                function updateChart(val)
                                {
                                    chart.data.datasets[0].data = val;
                                    chart.update();
                                }

                                Pusher.logToConsole = true;

                                let pusher = new Pusher('612f7932fcad1179ede3', {
                                    cluster: 'mt1'
                                });

                                let channel = pusher.subscribe('my-channel');
                                channel.bind('pusher:subscription_succeeded', function (members)
                                {
                                    alert('Successfully subscribed!');
                                });
                                channel.bind('App\\Events\\DataUpdater', function (data)
                                {
                                    updateChart(data.value);
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </body>
@endsection
