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
.table {
    width: 100%;
    margin-bottom: 1rem;
    color: #44476A;
    background-color: transparent;
}

.table th,
.table td {
    padding: 1rem;
    vertical-align: top;
    border-top: 0.0625rem solid #D1D9E6;
}

.table thead th {
    vertical-align: bottom;
    border-bottom: 0.125rem solid #D1D9E6;
}

.table tbody + tbody {
    border-top: 0.125rem solid #D1D9E6;
}

.table-sm th,
.table-sm td {
    padding: 0.3rem;
}

.table-bordered {
    border: 0.0625rem solid #D1D9E6;
}

.table-bordered th,
.table-bordered td {
    border: 0.0625rem solid #D1D9E6;
}

.table-bordered thead th,
.table-bordered thead td {
    border-bottom-width: 0.125rem;
}

.table-borderless th,
.table-borderless td,
.table-borderless thead th,
.table-borderless tbody + tbody {
    border: 0;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(38, 40, 51, 0.05);
}

.table-hover tbody tr:hover {
    color: #44476A;
    background-color: rgba(38, 40, 51, 0.03);
}

.table-primary,
.table-primary > th,
.table-primary > td {
    background-color: #eaedf2;
}

.table-primary th,
.table-primary td,
.table-primary thead th,
.table-primary tbody + tbody {
    border-color: #e9ebf0;
}

.table-hover .table-primary:hover {
    background-color: #dae0e8;
}

.table-hover .table-primary:hover > td,
.table-hover .table-primary:hover > th {
    background-color: #dae0e8;
}

.table-secondary,
.table-secondary > th,
.table-secondary > td {
    background-color: #b7c2e7;
}

.table-secondary th,
.table-secondary td,
.table-secondary thead th,
.table-secondary tbody + tbody {
    border-color: #899bdd;
}

.table-hover .table-secondary:hover {
    background-color: #a4b2e1;
}

.table-hover .table-secondary:hover > td,
.table-hover .table-secondary:hover > th {
    background-color: #a4b2e1;
}

.table-success,
.table-success > th,
.table-success > td {
    background-color: #b1c9c4;
}

.table-success th,
.table-success td,
.table-success thead th,
.table-success tbody + tbody {
    border-color: #7ea79c;
}

.table-hover .table-success:hover {
    background-color: #a2bfb9;
}

.table-hover .table-success:hover > td,
.table-hover .table-success:hover > th {
    background-color: #a2bfb9;
}

.table-info,
.table-info > th,
.table-info > td {
    background-color: #aac5e1;
}

.table-info th,
.table-info td,
.table-info thead th,
.table-info tbody + tbody {
    border-color: #71a0d2;
}

.table-hover .table-info:hover {
    background-color: #97b8da;
}

.table-hover .table-info:hover > td,
.table-hover .table-info:hover > th {
    background-color: #97b8da;
}

.table-warning,
.table-warning > th,
.table-warning > td {
    background-color: #eddfaf;
}

.table-warning th,
.table-warning td,
.table-warning thead th,
.table-warning tbody + tbody {
    border-color: #eed175;
}

.table-hover .table-warning:hover {
    background-color: #e8d79a;
}

.table-hover .table-warning:hover > td,
.table-hover .table-warning:hover > th {
    background-color: #e8d79a;
}

.table-danger,
.table-danger > th,
.table-danger > td {
    background-color: #d9b5bb;
}

.table-danger th,
.table-danger td,
.table-danger thead th,
.table-danger tbody + tbody {
    border-color: #c9838c;
}

.table-hover .table-danger:hover {
    background-color: #d0a4ac;
}

.table-hover .table-danger:hover > td,
.table-hover .table-danger:hover > th {
    background-color: #d0a4ac;
}

.table-light,
.table-light > th,
.table-light > td {
    background-color: #e4eaef;
}

.table-light th,
.table-light td,
.table-light thead th,
.table-light tbody + tbody {
    border-color: #dee4ec;
}

.table-hover .table-light:hover {
    background-color: #d4dee6;
}

.table-hover .table-light:hover > td,
.table-hover .table-light:hover > th {
    background-color: #d4dee6;
}

.table-dark,
.table-dark > th,
.table-dark > td {
    background-color: #b8bbc4;
}

.table-dark th,
.table-dark td,
.table-dark thead th,
.table-dark tbody + tbody {
    border-color: #8b8e9c;
}

.table-hover .table-dark:hover {
    background-color: #aaaeb8;
}

.table-hover .table-dark:hover > td,
.table-hover .table-dark:hover > th {
    background-color: #aaaeb8;
}

.table-default,
.table-default > th,
.table-default > td {
    background-color: #b5b8bd;
}

.table-default th,
.table-default td,
.table-default thead th,
.table-default tbody + tbody {
    border-color: #85888f;
}

.table-hover .table-default:hover {
    background-color: #a8abb1;
}

.table-hover .table-default:hover > td,
.table-hover .table-default:hover > th {
    background-color: #a8abb1;
}

.table-white,
.table-white > th,
.table-white > td {
    background-color: #ecf0f3;
}

.table-white th,
.table-white td,
.table-white thead th,
.table-white tbody + tbody {
    border-color: #ecf0f3;
}

.table-hover .table-white:hover {
    background-color: #dce4e9;
}

.table-hover .table-white:hover > td,
.table-hover .table-white:hover > th {
    background-color: #dce4e9;
}

.table-gray,
.table-gray > th,
.table-gray > td {
    background-color: #bdc1cd;
}

.table-gray th,
.table-gray td,
.table-gray thead th,
.table-gray tbody + tbody {
    border-color: #9598ac;
}

.table-hover .table-gray:hover {
    background-color: #aeb3c2;
}

.table-hover .table-gray:hover > td,
.table-hover .table-gray:hover > th {
    background-color: #aeb3c2;
}

.table-neutral,
.table-neutral > th,
.table-neutral > td {
    background-color: #ecf0f3;
}

.table-neutral th,
.table-neutral td,
.table-neutral thead th,
.table-neutral tbody + tbody {
    border-color: #ecf0f3;
}

.table-hover .table-neutral:hover {
    background-color: #dce4e9;
}

.table-hover .table-neutral:hover > td,
.table-hover .table-neutral:hover > th {
    background-color: #dce4e9;
}

.table-soft,
.table-soft > th,
.table-soft > td {
    background-color: #eaedf2;
}

.table-soft th,
.table-soft td,
.table-soft thead th,
.table-soft tbody + tbody {
    border-color: #e9ebf0;
}

.table-hover .table-soft:hover {
    background-color: #dae0e8;
}

.table-hover .table-soft:hover > td,
.table-hover .table-soft:hover > th {
    background-color: #dae0e8;
}

.table-black,
.table-black > th,
.table-black > td {
    background-color: #b5b8bd;
}

.table-black th,
.table-black td,
.table-black thead th,
.table-black tbody + tbody {
    border-color: #85888f;
}

.table-hover .table-black:hover {
    background-color: #a8abb1;
}

.table-hover .table-black:hover > td,
.table-hover .table-black:hover > th {
    background-color: #a8abb1;
}

.table-purple,
.table-purple > th,
.table-purple > td {
    background-color: #c9bfe5;
}

.table-purple th,
.table-purple td,
.table-purple thead th,
.table-purple tbody + tbody {
    border-color: #ab96d9;
}

.table-hover .table-purple:hover {
    background-color: #baadde;
}

.table-hover .table-purple:hover > td,
.table-hover .table-purple:hover > th {
    background-color: #baadde;
}

.table-gray-100,
.table-gray-100 > th,
.table-gray-100 > td {
    background-color: #eef2f5;
}

.table-gray-100 th,
.table-gray-100 td,
.table-gray-100 thead th,
.table-gray-100 tbody + tbody {
    border-color: #f0f4f7;
}

.table-hover .table-gray-100:hover {
    background-color: #dee6ec;
}

.table-hover .table-gray-100:hover > td,
.table-hover .table-gray-100:hover > th {
    background-color: #dee6ec;
}

.table-gray-200,
.table-gray-200 > th,
.table-gray-200 > td {
    background-color: #f0f3f6;
}

.table-gray-200 th,
.table-gray-200 td,
.table-gray-200 thead th,
.table-gray-200 tbody + tbody {
    border-color: #f3f6f9;
}

.table-hover .table-gray-200:hover {
    background-color: #e0e6ec;
}

.table-hover .table-gray-200:hover > td,
.table-hover .table-gray-200:hover > th {
    background-color: #e0e6ec;
}

.table-gray-300,
.table-gray-300 > th,
.table-gray-300 > td {
    background-color: #eaedf2;
}

.table-gray-300 th,
.table-gray-300 td,
.table-gray-300 thead th,
.table-gray-300 tbody + tbody {
    border-color: #e9ebf0;
}

.table-hover .table-gray-300:hover {
    background-color: #dae0e8;
}

.table-hover .table-gray-300:hover > td,
.table-hover .table-gray-300:hover > th {
    background-color: #dae0e8;
}

.table-gray-400,
.table-gray-400 > th,
.table-gray-400 > td {
    background-color: #e4eaef;
}

.table-gray-400 th,
.table-gray-400 td,
.table-gray-400 thead th,
.table-gray-400 tbody + tbody {
    border-color: #dee4ec;
}

.table-hover .table-gray-400:hover {
    background-color: #d4dee6;
}

.table-hover .table-gray-400:hover > td,
.table-hover .table-gray-400:hover > th {
    background-color: #d4dee6;
}

.table-gray-500,
.table-gray-500 > th,
.table-gray-500 > td {
    background-color: #dbe1e9;
}

.table-gray-500 th,
.table-gray-500 td,
.table-gray-500 thead th,
.table-gray-500 tbody + tbody {
    border-color: #cdd5e0;
}

.table-hover .table-gray-500:hover {
    background-color: #cbd4df;
}

.table-hover .table-gray-500:hover > td,
.table-hover .table-gray-500:hover > th {
    background-color: #cbd4df;
}

.table-gray-600,
.table-gray-600 > th,
.table-gray-600 > td {
    background-color: #d3dbe4;
}

.table-gray-600 th,
.table-gray-600 td,
.table-gray-600 thead th,
.table-gray-600 tbody + tbody {
    border-color: #bec9d7;
}

.table-hover .table-gray-600:hover {
    background-color: #c3ceda;
}

.table-hover .table-gray-600:hover > td,
.table-hover .table-gray-600:hover > th {
    background-color: #c3ceda;
}

.table-gray-700,
.table-gray-700 > th,
.table-gray-700 > td {
    background-color: #c6cfdb;
}

.table-gray-700 th,
.table-gray-700 td,
.table-gray-700 thead th,
.table-gray-700 tbody + tbody {
    border-color: #a6b2c7;
}

.table-hover .table-gray-700:hover {
    background-color: #b6c2d1;
}

.table-hover .table-gray-700:hover > td,
.table-hover .table-gray-700:hover > th {
    background-color: #b6c2d1;
}

.table-gray-800,
.table-gray-800 > th,
.table-gray-800 > td {
    background-color: #c1c4d3;
}

.table-gray-800 th,
.table-gray-800 td,
.table-gray-800 thead th,
.table-gray-800 tbody + tbody {
    border-color: #9c9fb7;
}

.table-hover .table-gray-800:hover {
    background-color: #b2b6c8;
}

.table-hover .table-gray-800:hover > td,
.table-hover .table-gray-800:hover > th {
    background-color: #b2b6c8;
}

.table-facebook,
.table-facebook > th,
.table-facebook > td {
    background-color: #bac6da;
}

.table-facebook th,
.table-facebook td,
.table-facebook thead th,
.table-facebook tbody + tbody {
    border-color: #90a1c4;
}

.table-hover .table-facebook:hover {
    background-color: #a9b8d1;
}

.table-hover .table-facebook:hover > td,
.table-hover .table-facebook:hover > th {
    background-color: #a9b8d1;
}

.table-dribbble,
.table-dribbble > th,
.table-dribbble > td {
    background-color: #ebc2d5;
}

.table-dribbble th,
.table-dribbble td,
.table-dribbble thead th,
.table-dribbble tbody + tbody {
    border-color: #eb9bbc;
}

.table-hover .table-dribbble:hover {
    background-color: #e5afc8;
}

.table-hover .table-dribbble:hover > td,
.table-hover .table-dribbble:hover > th {
    background-color: #e5afc8;
}

.table-github,
.table-github > th,
.table-github > td {
    background-color: #b3b6b8;
}

.table-github th,
.table-github td,
.table-github thead th,
.table-github tbody + tbody {
    border-color: #838586;
}

.table-hover .table-github:hover {
    background-color: #a6a9ac;
}

.table-hover .table-github:hover > td,
.table-hover .table-github:hover > th {
    background-color: #a6a9ac;
}

.table-behance,
.table-behance > th,
.table-behance > td {
    background-color: #aac5f6;
}

.table-behance th,
.table-behance td,
.table-behance thead th,
.table-behance tbody + tbody {
    border-color: #71a0f9;
}

.table-hover .table-behance:hover {
    background-color: #93b5f4;
}

.table-hover .table-behance:hover > td,
.table-hover .table-behance:hover > th {
    background-color: #93b5f4;
}

.table-twitter,
.table-twitter > th,
.table-twitter > td {
    background-color: #b2daf3;
}

.table-twitter th,
.table-twitter td,
.table-twitter thead th,
.table-twitter tbody + tbody {
    border-color: #80c7f2;
}

.table-hover .table-twitter:hover {
    background-color: #9ccff0;
}

.table-hover .table-twitter:hover > td,
.table-hover .table-twitter:hover > th {
    background-color: #9ccff0;
}

.table-slack,
.table-slack > th,
.table-slack > td {
    background-color: #baded4;
}

.table-slack th,
.table-slack td,
.table-slack thead th,
.table-slack tbody + tbody {
    border-color: #8fceba;
}

.table-hover .table-slack:hover {
    background-color: #a9d6c9;
}

.table-hover .table-slack:hover > td,
.table-hover .table-slack:hover > th {
    background-color: #a9d6c9;
}

.table-active,
.table-active > th,
.table-active > td {
    background-color: rgba(38, 40, 51, 0.03);
}

.table-hover .table-active:hover {
    background-color: rgba(27, 29, 36, 0.03);
}

.table-hover .table-active:hover > td,
.table-hover .table-active:hover > th {
    background-color: rgba(27, 29, 36, 0.03);
}

.table .thead-dark th {
    color: #e6e7ee;
    background-color: #44476A;
    border-color: #535781;
}

.table .thead-light th {
    color: #66799e;
    background-color: #fafbfe;
    border-color: #D1D9E6;
}

.table-dark {
    color: #e6e7ee;
    background-color: #44476A;
}

.table-dark th,
.table-dark td,
.table-dark thead th {
    border-color: #535781;
}

.table-dark.table-bordered {
    border: 0;
}

.table-dark.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(236, 240, 243, 0.05);
}

.table-dark.table-hover tbody tr:hover {
    color: #e6e7ee;
    background-color: rgba(236, 240, 243, 0.075);
}

@media (max-width: 575.98px) {
    .table-responsive-sm {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-responsive-sm > .table-bordered {
        border: 0;
    }
}

@media (max-width: 767.98px) {
    .table-responsive-md {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-responsive-md > .table-bordered {
        border: 0;
    }
}

@media (max-width: 991.98px) {
    .table-responsive-lg {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-responsive-lg > .table-bordered {
        border: 0;
    }
}

@media (max-width: 1199.98px) {
    .table-responsive-xl {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-responsive-xl > .table-bordered {
        border: 0;
    }
}

.table-responsive {
    display: block;
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.table-responsive > .table-bordered {
    border: 0;
}
</style>
