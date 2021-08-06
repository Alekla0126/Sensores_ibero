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
table {
    border-collapse: collapse;
}
.table.b-table {
    width: 100%;
    margin-bottom: 1rem;
    color: #44476A;
    background-color: transparent;
}

.table.b-table th,
.table.b-table td {
    padding: 1rem;
    vertical-align: top;
    border-top: 0.0625rem solid #D1D9E6;
}

.table.b-table thead th {
    vertical-align: bottom;
    border-bottom: 0.125rem solid #D1D9E6;
}

.table.b-table tbody + tbody {
    border-top: 0.125rem solid #D1D9E6;
}

.table.b-table-sm th,
.table.b-table-sm td {
    padding: 0.3rem;
}

.table.b-table-bordered {
    border: 0.0625rem solid #D1D9E6;
}

.table.b-table-bordered th,
.table.b-table-bordered td {
    border: 0.0625rem solid #D1D9E6;
}

.table.b-table-bordered thead th,
.table.b-table-bordered thead td {
    border-bottom-width: 0.125rem;
}

.table.b-table-borderless th,
.table.b-table-borderless td,
.table.b-table-borderless thead th,
.table.b-table-borderless tbody + tbody {
    border: 0;
}

.table.b-table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(38, 40, 51, 0.05);
}

.table.b-table-hover tbody tr:hover {
    color: #44476A;
    background-color: rgba(38, 40, 51, 0.03);
}

.table.b-table-primary,
.table.b-table-primary > th,
.table.b-table-primary > td {
    background-color: #eaedf2;
}

.table.b-table-primary th,
.table.b-table-primary td,
.table.b-table-primary thead th,
.table.b-table-primary tbody + tbody {
    border-color: #e9ebf0;
}

.table.b-table-hover .table.b-table-primary:hover {
    background-color: #dae0e8;
}

.table.b-table-hover .table.b-table-primary:hover > td,
.table.b-table-hover .table.b-table-primary:hover > th {
    background-color: #dae0e8;
}

.table.b-table-secondary,
.table.b-table-secondary > th,
.table.b-table-secondary > td {
    background-color: #b7c2e7;
}

.table.b-table-secondary th,
.table.b-table-secondary td,
.table.b-table-secondary thead th,
.table.b-table-secondary tbody + tbody {
    border-color: #899bdd;
}

.table.b-table-hover .table.b-table-secondary:hover {
    background-color: #a4b2e1;
}

.table.b-table-hover .table.b-table-secondary:hover > td,
.table.b-table-hover .table.b-table-secondary:hover > th {
    background-color: #a4b2e1;
}

.table.b-table-success,
.table.b-table-success > th,
.table.b-table-success > td {
    background-color: #b1c9c4;
}

.table.b-table-success th,
.table.b-table-success td,
.table.b-table-success thead th,
.table.b-table-success tbody + tbody {
    border-color: #7ea79c;
}

.table.b-table-hover .table.b-table-success:hover {
    background-color: #a2bfb9;
}

.table.b-table-hover .table.b-table-success:hover > td,
.table.b-table-hover .table.b-table-success:hover > th {
    background-color: #a2bfb9;
}

.table.b-table-info,
.table.b-table-info > th,
.table.b-table-info > td {
    background-color: #aac5e1;
}

.table.b-table-info th,
.table.b-table-info td,
.table.b-table-info thead th,
.table.b-table-info tbody + tbody {
    border-color: #71a0d2;
}

.table.b-table-hover .table.b-table-info:hover {
    background-color: #97b8da;
}

.table.b-table-hover .table.b-table-info:hover > td,
.table.b-table-hover .table.b-table-info:hover > th {
    background-color: #97b8da;
}

.table.b-table-warning,
.table.b-table-warning > th,
.table.b-table-warning > td {
    background-color: #eddfaf;
}

.table.b-table-warning th,
.table.b-table-warning td,
.table.b-table-warning thead th,
.table.b-table-warning tbody + tbody {
    border-color: #eed175;
}

.table.b-table-hover .table.b-table-warning:hover {
    background-color: #e8d79a;
}

.table.b-table-hover .table.b-table-warning:hover > td,
.table.b-table-hover .table.b-table-warning:hover > th {
    background-color: #e8d79a;
}

.table.b-table-danger,
.table.b-table-danger > th,
.table.b-table-danger > td {
    background-color: #d9b5bb;
}

.table.b-table-danger th,
.table.b-table-danger td,
.table.b-table-danger thead th,
.table.b-table-danger tbody + tbody {
    border-color: #c9838c;
}

.table.b-table-hover .table.b-table-danger:hover {
    background-color: #d0a4ac;
}

.table.b-table-hover .table.b-table-danger:hover > td,
.table.b-table-hover .table.b-table-danger:hover > th {
    background-color: #d0a4ac;
}

.table.b-table-light,
.table.b-table-light > th,
.table.b-table-light > td {
    background-color: #e4eaef;
}

.table.b-table-light th,
.table.b-table-light td,
.table.b-table-light thead th,
.table.b-table-light tbody + tbody {
    border-color: #dee4ec;
}

.table.b-table-hover .table.b-table-light:hover {
    background-color: #d4dee6;
}

.table.b-table-hover .table.b-table-light:hover > td,
.table.b-table-hover .table.b-table-light:hover > th {
    background-color: #d4dee6;
}

.table.b-table-dark,
.table.b-table-dark > th,
.table.b-table-dark > td {
    background-color: #b8bbc4;
}

.table.b-table-dark th,
.table.b-table-dark td,
.table.b-table-dark thead th,
.table.b-table-dark tbody + tbody {
    border-color: #8b8e9c;
}

.table.b-table-hover .table.b-table-dark:hover {
    background-color: #aaaeb8;
}

.table.b-table-hover .table.b-table-dark:hover > td,
.table.b-table-hover .table.b-table-dark:hover > th {
    background-color: #aaaeb8;
}

.table.b-table-default,
.table.b-table-default > th,
.table.b-table-default > td {
    background-color: #b5b8bd;
}

.table.b-table-default th,
.table.b-table-default td,
.table.b-table-default thead th,
.table.b-table-default tbody + tbody {
    border-color: #85888f;
}

.table.b-table-hover .table.b-table-default:hover {
    background-color: #a8abb1;
}

.table.b-table-hover .table.b-table-default:hover > td,
.table.b-table-hover .table.b-table-default:hover > th {
    background-color: #a8abb1;
}

.table.b-table-white,
.table.b-table-white > th,
.table.b-table-white > td {
    background-color: #ecf0f3;
}

.table.b-table-white th,
.table.b-table-white td,
.table.b-table-white thead th,
.table.b-table-white tbody + tbody {
    border-color: #ecf0f3;
}

.table.b-table-hover .table.b-table-white:hover {
    background-color: #dce4e9;
}

.table.b-table-hover .table.b-table-white:hover > td,
.table.b-table-hover .table.b-table-white:hover > th {
    background-color: #dce4e9;
}

.table.b-table-gray,
.table.b-table-gray > th,
.table.b-table-gray > td {
    background-color: #bdc1cd;
}

.table.b-table-gray th,
.table.b-table-gray td,
.table.b-table-gray thead th,
.table.b-table-gray tbody + tbody {
    border-color: #9598ac;
}

.table.b-table-hover .table.b-table-gray:hover {
    background-color: #aeb3c2;
}

.table.b-table-hover .table.b-table-gray:hover > td,
.table.b-table-hover .table.b-table-gray:hover > th {
    background-color: #aeb3c2;
}

.table.b-table-neutral,
.table.b-table-neutral > th,
.table.b-table-neutral > td {
    background-color: #ecf0f3;
}

.table.b-table-neutral th,
.table.b-table-neutral td,
.table.b-table-neutral thead th,
.table.b-table-neutral tbody + tbody {
    border-color: #ecf0f3;
}

.table.b-table-hover .table.b-table-neutral:hover {
    background-color: #dce4e9;
}

.table.b-table-hover .table.b-table-neutral:hover > td,
.table.b-table-hover .table.b-table-neutral:hover > th {
    background-color: #dce4e9;
}

.table.b-table-soft,
.table.b-table-soft > th,
.table.b-table-soft > td {
    background-color: #eaedf2;
}

.table.b-table-soft th,
.table.b-table-soft td,
.table.b-table-soft thead th,
.table.b-table-soft tbody + tbody {
    border-color: #e9ebf0;
}

.table.b-table-hover .table.b-table-soft:hover {
    background-color: #dae0e8;
}

.table.b-table-hover .table.b-table-soft:hover > td,
.table.b-table-hover .table.b-table-soft:hover > th {
    background-color: #dae0e8;
}

.table.b-table-black,
.table.b-table-black > th,
.table.b-table-black > td {
    background-color: #b5b8bd;
}

.table.b-table-black th,
.table.b-table-black td,
.table.b-table-black thead th,
.table.b-table-black tbody + tbody {
    border-color: #85888f;
}

.table.b-table-hover .table.b-table-black:hover {
    background-color: #a8abb1;
}

.table.b-table-hover .table.b-table-black:hover > td,
.table.b-table-hover .table.b-table-black:hover > th {
    background-color: #a8abb1;
}

.table.b-table-purple,
.table.b-table-purple > th,
.table.b-table-purple > td {
    background-color: #c9bfe5;
}

.table.b-table-purple th,
.table.b-table-purple td,
.table.b-table-purple thead th,
.table.b-table-purple tbody + tbody {
    border-color: #ab96d9;
}

.table.b-table-hover .table.b-table-purple:hover {
    background-color: #baadde;
}

.table.b-table-hover .table.b-table-purple:hover > td,
.table.b-table-hover .table.b-table-purple:hover > th {
    background-color: #baadde;
}

.table.b-table-gray-100,
.table.b-table-gray-100 > th,
.table.b-table-gray-100 > td {
    background-color: #eef2f5;
}

.table.b-table-gray-100 th,
.table.b-table-gray-100 td,
.table.b-table-gray-100 thead th,
.table.b-table-gray-100 tbody + tbody {
    border-color: #f0f4f7;
}

.table.b-table-hover .table.b-table-gray-100:hover {
    background-color: #dee6ec;
}

.table.b-table-hover .table.b-table-gray-100:hover > td,
.table.b-table-hover .table.b-table-gray-100:hover > th {
    background-color: #dee6ec;
}

.table.b-table-gray-200,
.table.b-table-gray-200 > th,
.table.b-table-gray-200 > td {
    background-color: #f0f3f6;
}

.table.b-table-gray-200 th,
.table.b-table-gray-200 td,
.table.b-table-gray-200 thead th,
.table.b-table-gray-200 tbody + tbody {
    border-color: #f3f6f9;
}

.table.b-table-hover .table.b-table-gray-200:hover {
    background-color: #e0e6ec;
}

.table.b-table-hover .table.b-table-gray-200:hover > td,
.table.b-table-hover .table.b-table-gray-200:hover > th {
    background-color: #e0e6ec;
}

.table.b-table-gray-300,
.table.b-table-gray-300 > th,
.table.b-table-gray-300 > td {
    background-color: #eaedf2;
}

.table.b-table-gray-300 th,
.table.b-table-gray-300 td,
.table.b-table-gray-300 thead th,
.table.b-table-gray-300 tbody + tbody {
    border-color: #e9ebf0;
}

.table.b-table-hover .table.b-table-gray-300:hover {
    background-color: #dae0e8;
}

.table.b-table-hover .table.b-table-gray-300:hover > td,
.table.b-table-hover .table.b-table-gray-300:hover > th {
    background-color: #dae0e8;
}

.table.b-table-gray-400,
.table.b-table-gray-400 > th,
.table.b-table-gray-400 > td {
    background-color: #e4eaef;
}

.table.b-table-gray-400 th,
.table.b-table-gray-400 td,
.table.b-table-gray-400 thead th,
.table.b-table-gray-400 tbody + tbody {
    border-color: #dee4ec;
}

.table.b-table-hover .table.b-table-gray-400:hover {
    background-color: #d4dee6;
}

.table.b-table-hover .table.b-table-gray-400:hover > td,
.table.b-table-hover .table.b-table-gray-400:hover > th {
    background-color: #d4dee6;
}

.table.b-table-gray-500,
.table.b-table-gray-500 > th,
.table.b-table-gray-500 > td {
    background-color: #dbe1e9;
}

.table.b-table-gray-500 th,
.table.b-table-gray-500 td,
.table.b-table-gray-500 thead th,
.table.b-table-gray-500 tbody + tbody {
    border-color: #cdd5e0;
}

.table.b-table-hover .table.b-table-gray-500:hover {
    background-color: #cbd4df;
}

.table.b-table-hover .table.b-table-gray-500:hover > td,
.table.b-table-hover .table.b-table-gray-500:hover > th {
    background-color: #cbd4df;
}

.table.b-table-gray-600,
.table.b-table-gray-600 > th,
.table.b-table-gray-600 > td {
    background-color: #d3dbe4;
}

.table.b-table-gray-600 th,
.table.b-table-gray-600 td,
.table.b-table-gray-600 thead th,
.table.b-table-gray-600 tbody + tbody {
    border-color: #bec9d7;
}

.table.b-table-hover .table.b-table-gray-600:hover {
    background-color: #c3ceda;
}

.table.b-table-hover .table.b-table-gray-600:hover > td,
.table.b-table-hover .table.b-table-gray-600:hover > th {
    background-color: #c3ceda;
}

.table.b-table-gray-700,
.table.b-table-gray-700 > th,
.table.b-table-gray-700 > td {
    background-color: #c6cfdb;
}

.table.b-table-gray-700 th,
.table.b-table-gray-700 td,
.table.b-table-gray-700 thead th,
.table.b-table-gray-700 tbody + tbody {
    border-color: #a6b2c7;
}

.table.b-table-hover .table.b-table-gray-700:hover {
    background-color: #b6c2d1;
}

.table.b-table-hover .table.b-table-gray-700:hover > td,
.table.b-table-hover .table.b-table-gray-700:hover > th {
    background-color: #b6c2d1;
}

.table.b-table-gray-800,
.table.b-table-gray-800 > th,
.table.b-table-gray-800 > td {
    background-color: #c1c4d3;
}

.table.b-table-gray-800 th,
.table.b-table-gray-800 td,
.table.b-table-gray-800 thead th,
.table.b-table-gray-800 tbody + tbody {
    border-color: #9c9fb7;
}

.table.b-table-hover .table.b-table-gray-800:hover {
    background-color: #b2b6c8;
}

.table.b-table-hover .table.b-table-gray-800:hover > td,
.table.b-table-hover .table.b-table-gray-800:hover > th {
    background-color: #b2b6c8;
}

.table.b-table-facebook,
.table.b-table-facebook > th,
.table.b-table-facebook > td {
    background-color: #bac6da;
}

.table.b-table-facebook th,
.table.b-table-facebook td,
.table.b-table-facebook thead th,
.table.b-table-facebook tbody + tbody {
    border-color: #90a1c4;
}

.table.b-table-hover .table.b-table-facebook:hover {
    background-color: #a9b8d1;
}

.table.b-table-hover .table.b-table-facebook:hover > td,
.table.b-table-hover .table.b-table-facebook:hover > th {
    background-color: #a9b8d1;
}

.table.b-table-dribbble,
.table.b-table-dribbble > th,
.table.b-table-dribbble > td {
    background-color: #ebc2d5;
}

.table.b-table-dribbble th,
.table.b-table-dribbble td,
.table.b-table-dribbble thead th,
.table.b-table-dribbble tbody + tbody {
    border-color: #eb9bbc;
}

.table.b-table-hover .table.b-table-dribbble:hover {
    background-color: #e5afc8;
}

.table.b-table-hover .table.b-table-dribbble:hover > td,
.table.b-table-hover .table.b-table-dribbble:hover > th {
    background-color: #e5afc8;
}

.table.b-table-github,
.table.b-table-github > th,
.table.b-table-github > td {
    background-color: #b3b6b8;
}

.table.b-table-github th,
.table.b-table-github td,
.table.b-table-github thead th,
.table.b-table-github tbody + tbody {
    border-color: #838586;
}

.table.b-table-hover .table.b-table-github:hover {
    background-color: #a6a9ac;
}

.table.b-table-hover .table.b-table-github:hover > td,
.table.b-table-hover .table.b-table-github:hover > th {
    background-color: #a6a9ac;
}

.table.b-table-behance,
.table.b-table-behance > th,
.table.b-table-behance > td {
    background-color: #aac5f6;
}

.table.b-table-behance th,
.table.b-table-behance td,
.table.b-table-behance thead th,
.table.b-table-behance tbody + tbody {
    border-color: #71a0f9;
}

.table.b-table-hover .table.b-table-behance:hover {
    background-color: #93b5f4;
}

.table.b-table-hover .table.b-table-behance:hover > td,
.table.b-table-hover .table.b-table-behance:hover > th {
    background-color: #93b5f4;
}

.table.b-table-twitter,
.table.b-table-twitter > th,
.table.b-table-twitter > td {
    background-color: #b2daf3;
}

.table.b-table-twitter th,
.table.b-table-twitter td,
.table.b-table-twitter thead th,
.table.b-table-twitter tbody + tbody {
    border-color: #80c7f2;
}

.table.b-table-hover .table.b-table-twitter:hover {
    background-color: #9ccff0;
}

.table.b-table-hover .table.b-table-twitter:hover > td,
.table.b-table-hover .table.b-table-twitter:hover > th {
    background-color: #9ccff0;
}

.table.b-table-slack,
.table.b-table-slack > th,
.table.b-table-slack > td {
    background-color: #baded4;
}

.table.b-table-slack th,
.table.b-table-slack td,
.table.b-table-slack thead th,
.table.b-table-slack tbody + tbody {
    border-color: #8fceba;
}

.table.b-table-hover .table.b-table-slack:hover {
    background-color: #a9d6c9;
}

.table.b-table-hover .table.b-table-slack:hover > td,
.table.b-table-hover .table.b-table-slack:hover > th {
    background-color: #a9d6c9;
}

.table.b-table-active,
.table.b-table-active > th,
.table.b-table-active > td {
    background-color: rgba(38, 40, 51, 0.03);
}

.table.b-table-hover .table.b-table-active:hover {
    background-color: rgba(27, 29, 36, 0.03);
}

.table.b-table-hover .table.b-table-active:hover > td,
.table.b-table-hover .table.b-table-active:hover > th {
    background-color: rgba(27, 29, 36, 0.03);
}

.table.b-table .thead-dark th {
    color: #e6e7ee;
    background-color: #44476A;
    border-color: #535781;
}

.table.b-table .thead-light th {
    color: #66799e;
    background-color: #fafbfe;
    border-color: #D1D9E6;
}

.table.b-table-dark {
    color: #e6e7ee;
    background-color: #44476A;
}

.table.b-table-dark th,
.table.b-table-dark td,
.table.b-table-dark thead th {
    border-color: #535781;
}

.table.b-table-dark.table.b-table-bordered {
    border: 0;
}

.table.b-table-dark.table.b-table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(236, 240, 243, 0.05);
}

.table.b-table-dark.table.b-table-hover tbody tr:hover {
    color: #e6e7ee;
    background-color: rgba(236, 240, 243, 0.075);
}

@media (max-width: 575.98px) {
    .table.b-table-responsive-sm {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table.b-table-responsive-sm > .table.b-table-bordered {
        border: 0;
    }
}

@media (max-width: 767.98px) {
    .table.b-table-responsive-md {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table.b-table-responsive-md > .table.b-table-bordered {
        border: 0;
    }
}

@media (max-width: 991.98px) {
    .table.b-table-responsive-lg {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table.b-table-responsive-lg > .table.b-table-bordered {
        border: 0;
    }
}

@media (max-width: 1199.98px) {
    .table.b-table-responsive-xl {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table.b-table-responsive-xl > .table.b-table-bordered {
        border: 0;
    }
}

.table.b-table-responsive {
    display: block;
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.table.b-table-responsive > .table.b-table-bordered {
    border: 0;
}
</style>
