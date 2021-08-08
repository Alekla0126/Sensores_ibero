<template>
    <div class="grid-item item3">
        <div class="card">
            <svg viewBox="0 0 512 512" fill="currentColor">
                <path
                    d="M448.773 235.551A135.893 135.893 0 00451 211c0-74.443-60.557-135-135-135-47.52 0-91.567 25.313-115.766 65.537-32.666-10.59-66.182-6.049-93.794 12.979-27.612 19.013-44.092 49.116-45.425 82.031C24.716 253.788 0 290.497 0 331c0 7.031 1.703 13.887 3.006 20.537l.015.015C12.719 400.492 56.034 436 106 436h300c57.891 0 106-47.109 106-105 0-40.942-25.053-77.798-63.227-95.449z"/>
            </svg>
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

body {
    background: linear-gradient(45deg, #FC466B, #3F5EFB);
    height: 100vh;
    font-family: 'Montserrat', sans-serif;
}

.card {
    background-color: var(rgba(16, 18, 27, 40));
    max-width: 1250px;
    max-height: 860px;
    height: 90vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    width: 100%;
    border-radius: 14px;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    font-size: 15px;
    font-weight: 500;
}

.overlay-app {
    width: 100%;
    height: 100%;
    position: fixed;
    left: 0;
    top: 0;
    pointer-events: all;
    background-color: rgba(36, 39, 59, 0.8);
    opacity: 0;
    visibility: hidden;
    transition: 0.3s;

&
.is-active {
    visibility: visible;
    opacity: 1;
}

}

</style>
