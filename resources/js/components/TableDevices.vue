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
.b-table {
    width: 100%;
    margin-bottom: 1rem;
    color: #44476A;
    background-color: transparent;
}

.b-table th,
.b-table td {
    padding: 1rem;
    vertical-align: top;
    border-top: 0.0625rem solid #D1D9E6;
}

.b-table thead th {
    vertical-align: bottom;
    border-bottom: 0.125rem solid #D1D9E6;
}

.b-table tbody + tbody {
    border-top: 0.125rem solid #D1D9E6;
}

.b-table-sm th,
.b-table-sm td {
    padding: 0.3rem;
}

.b-table-bordered {
    border: 0.0625rem solid #D1D9E6;
}

.b-table-bordered th,
.b-table-bordered td {
    border: 0.0625rem solid #D1D9E6;
}

.b-table-bordered thead th,
.b-table-bordered thead td {
    border-bottom-width: 0.125rem;
}

.b-table-borderless th,
.b-table-borderless td,
.b-table-borderless thead th,
.b-table-borderless tbody + tbody {
    border: 0;
}

.b-table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(38, 40, 51, 0.05);
}

.b-table-hover tbody tr:hover {
    color: #44476A;
    background-color: rgba(38, 40, 51, 0.03);
}

.b-table-primary,
.b-table-primary > th,
.b-table-primary > td {
    background-color: #eaedf2;
}

.b-table-primary th,
.b-table-primary td,
.b-table-primary thead th,
.b-table-primary tbody + tbody {
    border-color: #e9ebf0;
}

.b-table-hover .b-table-primary:hover {
    background-color: #dae0e8;
}

.b-table-hover .b-table-primary:hover > td,
.b-table-hover .b-table-primary:hover > th {
    background-color: #dae0e8;
}

.b-table-secondary,
.b-table-secondary > th,
.b-table-secondary > td {
    background-color: #b7c2e7;
}

.b-table-secondary th,
.b-table-secondary td,
.b-table-secondary thead th,
.b-table-secondary tbody + tbody {
    border-color: #899bdd;
}

.b-table-hover .b-table-secondary:hover {
    background-color: #a4b2e1;
}

.b-table-hover .b-table-secondary:hover > td,
.b-table-hover .b-table-secondary:hover > th {
    background-color: #a4b2e1;
}

.b-table-success,
.b-table-success > th,
.b-table-success > td {
    background-color: #b1c9c4;
}

.b-table-success th,
.b-table-success td,
.b-table-success thead th,
.b-table-success tbody + tbody {
    border-color: #7ea79c;
}

.b-table-hover .b-table-success:hover {
    background-color: #a2bfb9;
}

.b-table-hover .b-table-success:hover > td,
.b-table-hover .b-table-success:hover > th {
    background-color: #a2bfb9;
}

.b-table-info,
.b-table-info > th,
.b-table-info > td {
    background-color: #aac5e1;
}

.b-table-info th,
.b-table-info td,
.b-table-info thead th,
.b-table-info tbody + tbody {
    border-color: #71a0d2;
}

.b-table-hover .b-table-info:hover {
    background-color: #97b8da;
}

.b-table-hover .b-table-info:hover > td,
.b-table-hover .b-table-info:hover > th {
    background-color: #97b8da;
}

.b-table-warning,
.b-table-warning > th,
.b-table-warning > td {
    background-color: #eddfaf;
}

.b-table-warning th,
.b-table-warning td,
.b-table-warning thead th,
.b-table-warning tbody + tbody {
    border-color: #eed175;
}

.b-table-hover .b-table-warning:hover {
    background-color: #e8d79a;
}

.b-table-hover .b-table-warning:hover > td,
.b-table-hover .b-table-warning:hover > th {
    background-color: #e8d79a;
}

.b-table-danger,
.b-table-danger > th,
.b-table-danger > td {
    background-color: #d9b5bb;
}

.b-table-danger th,
.b-table-danger td,
.b-table-danger thead th,
.b-table-danger tbody + tbody {
    border-color: #c9838c;
}

.b-table-hover .b-table-danger:hover {
    background-color: #d0a4ac;
}

.b-table-hover .b-table-danger:hover > td,
.b-table-hover .b-table-danger:hover > th {
    background-color: #d0a4ac;
}

.b-table-light,
.b-table-light > th,
.b-table-light > td {
    background-color: #e4eaef;
}

.b-table-light th,
.b-table-light td,
.b-table-light thead th,
.b-table-light tbody + tbody {
    border-color: #dee4ec;
}

.b-table-hover .b-table-light:hover {
    background-color: #d4dee6;
}

.b-table-hover .b-table-light:hover > td,
.b-table-hover .b-table-light:hover > th {
    background-color: #d4dee6;
}

.b-table-dark,
.b-table-dark > th,
.b-table-dark > td {
    background-color: #b8bbc4;
}

.b-table-dark th,
.b-table-dark td,
.b-table-dark thead th,
.b-table-dark tbody + tbody {
    border-color: #8b8e9c;
}

.b-table-hover .b-table-dark:hover {
    background-color: #aaaeb8;
}

.b-table-hover .b-table-dark:hover > td,
.b-table-hover .b-table-dark:hover > th {
    background-color: #aaaeb8;
}

.b-table-default,
.b-table-default > th,
.b-table-default > td {
    background-color: #b5b8bd;
}

.b-table-default th,
.b-table-default td,
.b-table-default thead th,
.b-table-default tbody + tbody {
    border-color: #85888f;
}

.b-table-hover .b-table-default:hover {
    background-color: #a8abb1;
}

.b-table-hover .b-table-default:hover > td,
.b-table-hover .b-table-default:hover > th {
    background-color: #a8abb1;
}

.b-table-white,
.b-table-white > th,
.b-table-white > td {
    background-color: #ecf0f3;
}

.b-table-white th,
.b-table-white td,
.b-table-white thead th,
.b-table-white tbody + tbody {
    border-color: #ecf0f3;
}

.b-table-hover .b-table-white:hover {
    background-color: #dce4e9;
}

.b-table-hover .b-table-white:hover > td,
.b-table-hover .b-table-white:hover > th {
    background-color: #dce4e9;
}

.b-table-gray,
.b-table-gray > th,
.b-table-gray > td {
    background-color: #bdc1cd;
}

.b-table-gray th,
.b-table-gray td,
.b-table-gray thead th,
.b-table-gray tbody + tbody {
    border-color: #9598ac;
}

.b-table-hover .b-table-gray:hover {
    background-color: #aeb3c2;
}

.b-table-hover .b-table-gray:hover > td,
.b-table-hover .b-table-gray:hover > th {
    background-color: #aeb3c2;
}

.b-table-neutral,
.b-table-neutral > th,
.b-table-neutral > td {
    background-color: #ecf0f3;
}

.b-table-neutral th,
.b-table-neutral td,
.b-table-neutral thead th,
.b-table-neutral tbody + tbody {
    border-color: #ecf0f3;
}

.b-table-hover .b-table-neutral:hover {
    background-color: #dce4e9;
}

.b-table-hover .b-table-neutral:hover > td,
.b-table-hover .b-table-neutral:hover > th {
    background-color: #dce4e9;
}

.b-table-soft,
.b-table-soft > th,
.b-table-soft > td {
    background-color: #eaedf2;
}

.b-table-soft th,
.b-table-soft td,
.b-table-soft thead th,
.b-table-soft tbody + tbody {
    border-color: #e9ebf0;
}

.b-table-hover .b-table-soft:hover {
    background-color: #dae0e8;
}

.b-table-hover .b-table-soft:hover > td,
.b-table-hover .b-table-soft:hover > th {
    background-color: #dae0e8;
}

.b-table-black,
.b-table-black > th,
.b-table-black > td {
    background-color: #b5b8bd;
}

.b-table-black th,
.b-table-black td,
.b-table-black thead th,
.b-table-black tbody + tbody {
    border-color: #85888f;
}

.b-table-hover .b-table-black:hover {
    background-color: #a8abb1;
}

.b-table-hover .b-table-black:hover > td,
.b-table-hover .b-table-black:hover > th {
    background-color: #a8abb1;
}

.b-table-purple,
.b-table-purple > th,
.b-table-purple > td {
    background-color: #c9bfe5;
}

.b-table-purple th,
.b-table-purple td,
.b-table-purple thead th,
.b-table-purple tbody + tbody {
    border-color: #ab96d9;
}

.b-table-hover .b-table-purple:hover {
    background-color: #baadde;
}

.b-table-hover .b-table-purple:hover > td,
.b-table-hover .b-table-purple:hover > th {
    background-color: #baadde;
}

.b-table-gray-100,
.b-table-gray-100 > th,
.b-table-gray-100 > td {
    background-color: #eef2f5;
}

.b-table-gray-100 th,
.b-table-gray-100 td,
.b-table-gray-100 thead th,
.b-table-gray-100 tbody + tbody {
    border-color: #f0f4f7;
}

.b-table-hover .b-table-gray-100:hover {
    background-color: #dee6ec;
}

.b-table-hover .b-table-gray-100:hover > td,
.b-table-hover .b-table-gray-100:hover > th {
    background-color: #dee6ec;
}

.b-table-gray-200,
.b-table-gray-200 > th,
.b-table-gray-200 > td {
    background-color: #f0f3f6;
}

.b-table-gray-200 th,
.b-table-gray-200 td,
.b-table-gray-200 thead th,
.b-table-gray-200 tbody + tbody {
    border-color: #f3f6f9;
}

.b-table-hover .b-table-gray-200:hover {
    background-color: #e0e6ec;
}

.b-table-hover .b-table-gray-200:hover > td,
.b-table-hover .b-table-gray-200:hover > th {
    background-color: #e0e6ec;
}

.b-table-gray-300,
.b-table-gray-300 > th,
.b-table-gray-300 > td {
    background-color: #eaedf2;
}

.b-table-gray-300 th,
.b-table-gray-300 td,
.b-table-gray-300 thead th,
.b-table-gray-300 tbody + tbody {
    border-color: #e9ebf0;
}

.b-table-hover .b-table-gray-300:hover {
    background-color: #dae0e8;
}

.b-table-hover .b-table-gray-300:hover > td,
.b-table-hover .b-table-gray-300:hover > th {
    background-color: #dae0e8;
}

.b-table-gray-400,
.b-table-gray-400 > th,
.b-table-gray-400 > td {
    background-color: #e4eaef;
}

.b-table-gray-400 th,
.b-table-gray-400 td,
.b-table-gray-400 thead th,
.b-table-gray-400 tbody + tbody {
    border-color: #dee4ec;
}

.b-table-hover .b-table-gray-400:hover {
    background-color: #d4dee6;
}

.b-table-hover .b-table-gray-400:hover > td,
.b-table-hover .b-table-gray-400:hover > th {
    background-color: #d4dee6;
}

.b-table-gray-500,
.b-table-gray-500 > th,
.b-table-gray-500 > td {
    background-color: #dbe1e9;
}

.b-table-gray-500 th,
.b-table-gray-500 td,
.b-table-gray-500 thead th,
.b-table-gray-500 tbody + tbody {
    border-color: #cdd5e0;
}

.b-table-hover .b-table-gray-500:hover {
    background-color: #cbd4df;
}

.b-table-hover .b-table-gray-500:hover > td,
.b-table-hover .b-table-gray-500:hover > th {
    background-color: #cbd4df;
}

.b-table-gray-600,
.b-table-gray-600 > th,
.b-table-gray-600 > td {
    background-color: #d3dbe4;
}

.b-table-gray-600 th,
.b-table-gray-600 td,
.b-table-gray-600 thead th,
.b-table-gray-600 tbody + tbody {
    border-color: #bec9d7;
}

.b-table-hover .b-table-gray-600:hover {
    background-color: #c3ceda;
}

.b-table-hover .b-table-gray-600:hover > td,
.b-table-hover .b-table-gray-600:hover > th {
    background-color: #c3ceda;
}

.b-table-gray-700,
.b-table-gray-700 > th,
.b-table-gray-700 > td {
    background-color: #c6cfdb;
}

.b-table-gray-700 th,
.b-table-gray-700 td,
.b-table-gray-700 thead th,
.b-table-gray-700 tbody + tbody {
    border-color: #a6b2c7;
}

.b-table-hover .b-table-gray-700:hover {
    background-color: #b6c2d1;
}

.b-table-hover .b-table-gray-700:hover > td,
.b-table-hover .b-table-gray-700:hover > th {
    background-color: #b6c2d1;
}

.b-table-gray-800,
.b-table-gray-800 > th,
.b-table-gray-800 > td {
    background-color: #c1c4d3;
}

.b-table-gray-800 th,
.b-table-gray-800 td,
.b-table-gray-800 thead th,
.b-table-gray-800 tbody + tbody {
    border-color: #9c9fb7;
}

.b-table-hover .b-table-gray-800:hover {
    background-color: #b2b6c8;
}

.b-table-hover .b-table-gray-800:hover > td,
.b-table-hover .b-table-gray-800:hover > th {
    background-color: #b2b6c8;
}

.b-table-facebook,
.b-table-facebook > th,
.b-table-facebook > td {
    background-color: #bac6da;
}

.b-table-facebook th,
.b-table-facebook td,
.b-table-facebook thead th,
.b-table-facebook tbody + tbody {
    border-color: #90a1c4;
}

.b-table-hover .b-table-facebook:hover {
    background-color: #a9b8d1;
}

.b-table-hover .b-table-facebook:hover > td,
.b-table-hover .b-table-facebook:hover > th {
    background-color: #a9b8d1;
}

.b-table-dribbble,
.b-table-dribbble > th,
.b-table-dribbble > td {
    background-color: #ebc2d5;
}

.b-table-dribbble th,
.b-table-dribbble td,
.b-table-dribbble thead th,
.b-table-dribbble tbody + tbody {
    border-color: #eb9bbc;
}

.b-table-hover .b-table-dribbble:hover {
    background-color: #e5afc8;
}

.b-table-hover .b-table-dribbble:hover > td,
.b-table-hover .b-table-dribbble:hover > th {
    background-color: #e5afc8;
}

.b-table-github,
.b-table-github > th,
.b-table-github > td {
    background-color: #b3b6b8;
}

.b-table-github th,
.b-table-github td,
.b-table-github thead th,
.b-table-github tbody + tbody {
    border-color: #838586;
}

.b-table-hover .b-table-github:hover {
    background-color: #a6a9ac;
}

.b-table-hover .b-table-github:hover > td,
.b-table-hover .b-table-github:hover > th {
    background-color: #a6a9ac;
}

.b-table-behance,
.b-table-behance > th,
.b-table-behance > td {
    background-color: #aac5f6;
}

.b-table-behance th,
.b-table-behance td,
.b-table-behance thead th,
.b-table-behance tbody + tbody {
    border-color: #71a0f9;
}

.b-table-hover .b-table-behance:hover {
    background-color: #93b5f4;
}

.b-table-hover .b-table-behance:hover > td,
.b-table-hover .b-table-behance:hover > th {
    background-color: #93b5f4;
}

.b-table-twitter,
.b-table-twitter > th,
.b-table-twitter > td {
    background-color: #b2daf3;
}

.b-table-twitter th,
.b-table-twitter td,
.b-table-twitter thead th,
.b-table-twitter tbody + tbody {
    border-color: #80c7f2;
}

.b-table-hover .b-table-twitter:hover {
    background-color: #9ccff0;
}

.b-table-hover .b-table-twitter:hover > td,
.b-table-hover .b-table-twitter:hover > th {
    background-color: #9ccff0;
}

.b-table-slack,
.b-table-slack > th,
.b-table-slack > td {
    background-color: #baded4;
}

.b-table-slack th,
.b-table-slack td,
.b-table-slack thead th,
.b-table-slack tbody + tbody {
    border-color: #8fceba;
}

.b-table-hover .b-table-slack:hover {
    background-color: #a9d6c9;
}

.b-table-hover .b-table-slack:hover > td,
.b-table-hover .b-table-slack:hover > th {
    background-color: #a9d6c9;
}

.b-table-active,
.b-table-active > th,
.b-table-active > td {
    background-color: rgba(38, 40, 51, 0.03);
}

.b-table-hover .b-table-active:hover {
    background-color: rgba(27, 29, 36, 0.03);
}

.b-table-hover .b-table-active:hover > td,
.b-table-hover .b-table-active:hover > th {
    background-color: rgba(27, 29, 36, 0.03);
}

.b-table .thead-dark th {
    color: #e6e7ee;
    background-color: #44476A;
    border-color: #535781;
}

.b-table .thead-light th {
    color: #66799e;
    background-color: #fafbfe;
    border-color: #D1D9E6;
}

.b-table-dark {
    color: #e6e7ee;
    background-color: #44476A;
}

.b-table-dark th,
.b-table-dark td,
.b-table-dark thead th {
    border-color: #535781;
}

.b-table-dark.b-table-bordered {
    border: 0;
}

.b-table-dark.b-table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(236, 240, 243, 0.05);
}

.b-table-dark.b-table-hover tbody tr:hover {
    color: #e6e7ee;
    background-color: rgba(236, 240, 243, 0.075);
}

@media (max-width: 575.98px) {
    .b-table-responsive-sm {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .b-table-responsive-sm > .b-table-bordered {
        border: 0;
    }
}

@media (max-width: 767.98px) {
    .b-table-responsive-md {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .b-table-responsive-md > .b-table-bordered {
        border: 0;
    }
}

@media (max-width: 991.98px) {
    .b-table-responsive-lg {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .b-table-responsive-lg > .b-table-bordered {
        border: 0;
    }
}

@media (max-width: 1199.98px) {
    .b-table-responsive-xl {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .b-table-responsive-xl > .b-table-bordered {
        border: 0;
    }
}

.b-table-responsive {
    display: block;
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.b-table-responsive > .b-table-bordered {
    border: 0;
}
</style>
