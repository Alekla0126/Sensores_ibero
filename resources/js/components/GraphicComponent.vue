<template>
    <div class="card">
        <div class="card-header">Gráfica de los niveles de CO2 con id: {{ device['id'] }}<span
            class="badge badge-info float-right">{{ device['device_id'] }}
        </span>
        </div>
        <div class="card-body">
            <b-alert
                :show="dismissCountDown"
                dismissible
                variant="danger"
                @dismissed="dismissCountDown=0"
                @dismiss-count-down="countDownChanged"
            >
                <p>El salón {{ this.device['device_id'] }} excedió los niveles de CO2, el mensaje se cerrará en: {{ dismissCountDown }}</p>
                <b-progress
                    variant="danger"
                    :max="dismissSecs"
                    :value="dismissCountDown"
                    height="4px"
                ></b-progress>
            </b-alert>
            <canvas id="line-chart" :styles="myStyles"></canvas>
        </div>
    </div>
</template>

<script>
import Chart from 'chart.js';

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
            labeled: 'CO2',
            dismissSecs: 10,
            dismissCountDown: 0,
        }
    },
    created()
    {
        window.Echo.channel('chat').listen('TemperatureUpdater', (e) =>
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
                this.myChart.data.datasets[0].data = this.values;
                this.myChart.data.labels = this.labels;
                this.myChart.update();
                // Change the tolerance that triggers the alarm notifications.
                if(this.device['value'] > 50)
                {
                    this.showAlert();
                }
            }
        });
    },
    mounted()
    {
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
                'options': {
                    maintainAspectRatio: false,
                    height: 300,
                },
            });
        },
        countDownChanged(dismissCountDown)
        {
            this.dismissCountDown = dismissCountDown
        },
        showAlert()
        {
            this.dismissCountDown = this.dismissSecs
        }
    },
    computed: {
        myStyles() {
            return {
                height: '20vh',
                position: 'relative',
            };
        },
    }
}
</script>
