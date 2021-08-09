<template>
    <div id="full">
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
    },
}
</script>

<style>

html, body {
    background: linear-gradient(45deg, #FC466B, #3F5EFB);
    flex-direction:column;
    font-family: 'Montserrat', sans-serif;
    top: 0;
    bottom:0;
    left:0;
    width: 100%;
    height: 100%;
}

#full {
    display: flex;
    flex-direction: column;
}

.card {
    background-color: rgba(255, 255, 255, 0.5);
    display: flex;
    flex-grow: 1;
    flex-direction: column;
    overflow: hidden;
    width: 100%;
    border-radius: 14px;
    opacity: .9;
    backdrop-filter: blur(s20px);
    font-size: 15px;
    font-weight: 500;
}

.btn {border-radius: 15px;}

form {
    background: $white;
    padding: 3em;
    height: 320px;
    border-radius: 20px;
    border-left: 1px solid $white;
    border-top: 1px solid $white;
    backdrop-filter: blur(10px);
    text-align: center;
    position: relative;
    transition: all 0.2s ease-in-out;

p {
    font-weight: 500;
    color: #fff;
    opacity: 0.7;
    font-size: 1.4rem;
    margin-top: 0;
    margin-bottom: 60px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
}

a {
    text-decoration: none;
    color: #ddd;
    font-size: 12px;

&:hover {
     text-shadow: 2px 2px 6px #00000040;
 }

&:active {
     text-shadow: none;
 }
}

input {
    background: transparent;
    width: 200px;
    padding: 1em;
    margin-bottom: 2em;
    border: none;
    border-left: 1px solid $white;
    border-top: 1px solid $white;
    border-radius: 5000px;
    backdrop-filter: blur(5px);
    box-shadow: 4px 4px 60px rgba(0, 0, 0, 0.2);
    color: #fff;
    font-family: Montserrat, sans-serif;
    font-weight: 500;
    transition: all 0.2s ease-in-out;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);

&:hover {
     background: rgba(255, 255, 255, 0.1);
     box-shadow: 4px 4px 60px 8px rgba(0, 0, 0, 0.2);
 }

&[type="email"],
&[type="password"] {
&:focus {
     background: rgba(255, 255, 255, 0.1);
     box-shadow: 4px 4px 60px 8px rgba(0, 0, 0, 0.2);
 }
}

&[type="button"] {
     margin-top: 10px;
     width: 150px;
     font-size: 1rem;

&:hover {
     cursor: pointer;
 }

&:active {
     background: rgba(255, 255, 255, 0.2);
 }
}
}

&:hover {
     margin: 4px;
 }
}

</style>
