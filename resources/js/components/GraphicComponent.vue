<template>
    <div class="grid-item item3">
        <div class="card">
            <div class="card-header">Temperatura<span
                class="badge badge-info float-right">{{ device['device_id'] }}</span></div>
            <div class="card-body">
                <canvas id="line-chart"></canvas>
            </div>
        </div>
    </div>
</template>

<script>
import Chart from 'chart.js'

export default {
    props: ['device'],
    data()
    {
        let myChart;
        return {
            values: [this.device['value'], 0, 0, 0, 0, 0, 0],
            labels: [this.device['updated_at'], '00:00', '00:00', '00:00', '00:00', '00:00', '00:00'],
            temp: [0],
            myChart,
            labeled: 'Temperatura'
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
            let ctx = document.getElementById("line-chart");
            this.myChart = new Chart(ctx, {
                'type': 'line',
                'data': {
                    'labels': [this.device['updated_at'], '00:00', '00:00', '00:00', '00:00', '00:00', '00:00'],
                    'datasets': [
                        {
                            'label': [this.labeled],
                            'data': [this.device['value'], 0, 0, 0, 0, 0, 0],
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
            Echo.channel('chat').listen('TemperatureUpdater', (e) =>
            {
                if(this.device['device_id'] === e.message['device_id'])
                {
                    if (this.temp.length <= 6)
                    {
                        this.temp.push(0);
                    }
                    else
                    {
                        this.temp = [0];
                        this.values = [0, 0, 0, 0, 0, 0, 0];
                    }
                    this.values[this.temp.length - 1] = e.message['value'];
                    this.labels[this.temp.length - 1] = e.message['updated_at'];
                    this.myChart.data.labels = this.labels;
                    this.myChart.data.datasets[0].data = this.values;
                    this.myChart.update();
                }
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
