<template>
    <div class="grid-item item3">
        <div class="card">
            <div class="card-header">Temperatura<span
                class="badge badge-info float-right">Salon: A205</span></div>
            <div class="card-body">
                <canvas id="bar-chart"></canvas>
            </div>
        </div>
    </div>
</template>


<script>
    import Chart from 'chart.js'
    export default
    {
        data()
        {
            return {
                values: [65, 59, 80, 81, 56, 55, 40],
                labeled: 'Temperature'
            }
        },
        mounted()
        {
            this.update();
            this.drawChart();
        },
        methods: {
            drawChart()
            {
                let ctx = document.getElementById("bar-chart");
                let myChart = new Chart(ctx, {
                    'type': 'line',
                    'data': {
                        'labels': ['06:00', '10:00', '14:00', '18:00', '22:00', '02:00', '04:00'],
                        'datasets': [
                            {
                                'label': [this.labeled],
                                'data': [this.values],
                                'fill': false,
                                'borderColor': 'rgb(75, 192, 192)',
                                'lineTension': 0.1,
                            }],
                    },
                    'options': {},
                });

            },
            update()
            {
                Echo.channel('chat').listen('TemperatureUpdater', (e) => {
                    console.log('event', e.message);
                    alert(e);
                    this.values = e;
                    this.drawChart();
                });
            }
        }
    }
</script>

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
