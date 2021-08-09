<template>
    <div class="card">
        <div class="card-header">Gráfica de los niveles de CO2 con id: {{ this.id }}<span
            class="badge badge-info float-right">Salón: {{ this.room }}
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
            <canvas id="line-chart"></canvas>
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
            values: [],
            labels: [],
            temp: [],
            myChart,
            labeled: 'CO2',
            dismissSecs: 10,
            dismissCountDown: 0,
            id: this.device[0]['id'],
            room: this.device[0]['device_id'],
            windowHeight: window.innerHeight
        }
    },
    created()
    {
        window.Echo.channel('chat').listen('TemperatureUpdater', (e) =>
        {
            if(this.device[0]['device_id'] === e.message['device_id'])
            {
                if (this.temp.length <= 6)
                {
                    this.temp.push(0);
                }
                else
                {
                    this.temp = [];
                }
                this.id = e.message['id'];
                this.values[this.temp.length - 1] = e.message['value'];
                this.labels[this.temp.length - 1] = e.message['updated_at'];
                this.myChart.data.datasets[0].data = this.values;
                this.myChart.data.labels = this.labels;
                this.myChart.update();
                // Change the tolerance that triggers the alarm notifications.
                if(e.message['value'] > 50)
                {
                    this.showAlert();
                }
            }
        });
    },
    mounted()
    {
        window.onresize = () => {
            this.windowWidth = window.innerWidth
        };
        this.extractData();
        this.drawChart();
    },
    methods: {
        extractData()
        {
            for (let index = 0; index < this.device.length; index++)
            {
                this.values.push(this.device[index]['value']);
                this.labels.push(this.device[index]['updated_at']);
            }
        },
        drawChart()
        {
            let ctx = document.getElementById("line-chart");
            ctx.height = this.windowHeight;
            this.myChart = new Chart(ctx, {
                'type': 'line',
                'data': {
                    'labels': this.labels,
                    'datasets': [
                        {
                            'label': [this.labeled],
                            'data': this.values,
                            'fill': false,
                            'borderColor': 'rgb(59,145,101)',
                            'lineTension': 0.1,
                        }],
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
    }
}
</script>
