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

    <div class="container-fluid">
        <div class="dashboard">
            <div class="grid-container">
                <div class="grid-item item0">
                    <div class="card">
                        <div class="card-header">Kitchen lights<span class="badge badge-info float-right">192.168.2.10</span></div>

                        <div class="card-body d-flex justify-content-center align-items-center">
                            <div class="color-picker-0"></div>
                            <script>
                                var colorPicker = new iro.ColorPicker('.color-picker-0', {
                                    // Set the size of the color picker
                                    width: 200,
                                    // Set the initial color to pure red
                                    color: "#0f0",

                                    layoutDirection: 'vertical',

                                    layout: [
                                        {
                                            component: iro.ui.Box,
                                            options: {}
                                        },
                                        {
                                            component: iro.ui.Slider,
                                            options: {
                                                // can also be 'saturation', 'value', 'red', 'green', 'blue', 'alpha' or 'kelvin'
                                                sliderType: 'saturation'
                                            }
                                        },
                                        {
                                            component: iro.ui.Slider,
                                            options: {
                                                sliderType: 'blue'
                                            }
                                        },
                                        {
                                            component: iro.ui.Slider,
                                            options: {
                                                sliderType: 'green'
                                            }
                                        },
                                        {
                                            component: iro.ui.Slider,
                                            options: {
                                                sliderType: 'red'
                                            }
                                        },
                                    ]
                                });
                            </script>
                        </div>
                    </div>
                </div>

                <div class="grid-item item1">
                    <div class="card">
                        <div class="card-header">{{ __('Dashboard') }}</div>

                        <div class="card-body">
                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif

                            Welcome back!
                        </div>
                    </div>
                </div>

                <div class="grid-item item2">
                    <div class="card">
                        <div class="card-header">Bedroom temperature<span class="badge badge-info float-right">192.168.2.1</span></div>

                        <div class="card-body">
                            <canvas id="bar-chart" class="chartjs"></canvas>
                            <script>
                                new Chart(document.getElementById('bar-chart'), {
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
                            </script>
                        </div>
                    </div>
                </div>

                <div class="grid-item item3">
                    <div class="card">
                        <div class="card-header">Living room lights<span class="badge badge-info float-right">192.168.2.2</span></div>

                        <div class="card-body d-flex justify-content-center align-items-center">
                            <div class="color-picker"></div>
                            <script>
                                var colorPicker = new iro.ColorPicker('.color-picker', {
                                    // Set the size of the color picker
                                    width: 200,
                                    // Set the initial color to pure red
                                    color: "#f00",

                                    layoutDirection: 'horizontal',

                                    layout: [
                                        {
                                            component: iro.ui.Box,
                                            options: {}
                                        },
                                        {
                                            component: iro.ui.Slider,
                                            options: {
                                                // can also be 'saturation', 'value', 'red', 'green', 'blue', 'alpha' or 'kelvin'
                                                sliderType: 'saturation'
                                            }
                                        },
                                        {
                                            component: iro.ui.Slider,
                                            options: {
                                                sliderType: 'blue'
                                            }
                                        },
                                        {
                                            component: iro.ui.Slider,
                                            options: {
                                                sliderType: 'green'
                                            }
                                        },
                                        {
                                            component: iro.ui.Slider,
                                            options: {
                                                sliderType: 'red'
                                            }
                                        },
                                    ]
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
