<template>
    <div class="grid-item item3">
        <div class="card">
            <div class="card-body">
                <b-alert
                    :show="dismissCountDown"
                    dismissible
                    variant="danger"
                    @dismissed="dismissCountDown=0"
                    @dismiss-count-down="countDownChanged"
                >
                    <p>El salón {{ this.salon }} excedió los niveles de CO2, el mensaje se cerrará en:
                        {{ dismissCountDown }}</p>
                    <b-progress
                        variant="danger"
                        :max="dismissSecs"
                        :value="dismissCountDown"
                        height="4px"
                    ></b-progress>
                </b-alert>
                <b-row>
                    <b-col lg="6" class="my-1">
                        <b-form-group
                            label="Buscar"
                            label-for="filter-input"
                            label-cols-sm="3"
                            label-align-sm="right"
                            label-size="sm"
                            class="mb-0"
                        >
                            <b-input-group size="sm">
                                <b-form-input
                                    id="filter-input"
                                    v-model="filter"
                                    type="search"
                                    placeholder="Escribe algo para buscar"
                                ></b-form-input>
                                <b-input-group-append>
                                    <b-button :disabled="!filter" @click="filter = ''">Borrar</b-button>
                                </b-input-group-append>
                            </b-input-group>
                        </b-form-group>
                    </b-col>
                    <b-col lg="6" class="my-1">
                        <b-form-group
                            label="Filtrado: "
                            description="Selecciona para filtrar"
                            label-cols-sm="3"
                            label-align-sm="right"
                            label-size="sm"
                            class="mb-0"
                            v-slot="{ ariaDescribedby }"
                        >
                            <b-form-checkbox-group
                                v-model="filterOn"
                                :aria-describedby="ariaDescribedby"
                                class="mt-1"
                            >
                                <b-form-checkbox value="id">Id</b-form-checkbox>
                                <b-form-checkbox value="device_id">Salón</b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-table
                    responsive
                    id="my-table"
                    ref="table"
                    hover
                    sticky-header
                    :items="items"
                    :fields="fields"
                    :current-page="currentPage"
                    :per-page="perPage"
                    :filter="filter"
                    :filter-included-fields="filterOn"
                    @row-clicked="onRowClick"
                >
                </b-table>
                <p class="mt-3">Página actual: {{ currentPage }}</p>
                <b-pagination
                    aria-controls="my-table"
                    v-model="currentPage"
                    :per-page="perPage"
                    :total-rows="rows"
                    first-number
                    last-number
                ></b-pagination>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['devices'],
    data()
    {
        return {
            n_devices: [],
            fields: [
                {
                    label: 'Id',
                    key: 'id',
                    sortable: true
                },
                {
                    label: 'Número del salón',
                    key: 'device_id',
                    sortable: true
                },
                {
                    label: 'CO2',
                    key: 'value',
                    sortable: true,
                },
                {
                    label: 'Creado en ',
                    key: 'created_at',
                    sortable: true,
                },
                {
                    label: 'Actualizado en',
                    key: 'updated_at',
                    sortable: true,
                },
            ],
            items: this.devices,
            filter: null,
            filterOn: [],
            currentPage: 1,
            perPage: 10,
            dismissSecs: 10,
            dismissCountDown: 0,
            salon: '',
        }
    },
    created()
    {
        window.Echo.channel('table').listen('TableUpdater', (e) =>
        {
            for (let index = 0; index < e.message.length; index++)
            {
                // Change the sensors limit.
                if (e.message[index]['value'] > 50)
                {
                    e.message[index]['_rowVariant'] = 'danger';
                }
            }
            // Change the alert limit.
            if (e.message[0]['value'] > 50)
            {
                this.salon = e.message[0]['device_id'];
                this.showAlert();
            }
            this.items = e.message;
            this.updateTable();
        });
    },
    mounted()
    {
        this.initializeTable(this.items);
    },
    methods: {
        initializeTable(entry_data)
        {
            for (let index = 0; index < entry_data.length; index++)
            {
                if (entry_data[index]['value'] > 50)
                {
                    entry_data[index]['_rowVariant'] = 'danger';
                }
            }
            this.updateTable();
        },
        updateTable()
        {
            this.$refs.table.refresh();
        },
        onRowClick(record, index)
        {
            window.location.href = '/' + 'graph' + '/' + record['device_id'];
        },
        onFiltered(filteredItems)
        {
            this.totalRows = filteredItems.length;
            this.currentPage = 1;
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
        rows()
        {
            return this.items.length
        }
    }
}
</script>

<style>
html,
body {
    height: 100%;
}

body {
    margin: 0;
    background: #e0e5ec;
    font-family: Arial, Helvetica, sans-serif;
    font-weight: 100;
}

.container {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

table.b-table {
    width: 600px;
    border-spacing: 0;
    color: #212121;
    text-align: center;
    overflow: hidden;
    box-shadow: 9px 9px 16px rgba(163, 177, 198, 0.6),
    -9px -9px 16px rgba(255, 255, 255, 0.6);
}

table.b-table thead {
    box-shadow: 9px 9px 16px rgba(163, 177, 198, 0.6);
}

table.b-table th {
    padding: 7px;
}

table.b-table > tbody > tr > td {
    padding: 10px;
    font-size: 14px;
    position: relative;
}


table.b-table > tbody > tr:hover {
    padding: 20px;
    box-shadow: 9px 9px 16px rgba(163, 177, 198, 0.6),
    -9px -9px 16px rgba(255, 255, 255, 0.6);
}

table.b-table tr td:first-child::before {
    content: "";
    position: absolute;
    padding: 7px;
    top: 0;
    left: -5000px;
    width: 10000px;
    height: 100%;
    z-index: -10;
}

table.b-table td:hover::after {
    content: "";
    position: absolute;
    box-shadow: 9px 9px 16px rgba(163, 177, 198, 0.6),
    -9px -9px 16px rgba(255, 255, 255, 0.6);
    left: 0;
    top: -5000px;
    height: 10000px;
    width: 100%;
    z-index: -1;
}
</style>
