<template>
    <div class="grid-item item3">
        <div class="card">
            <div class="card-body">
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
                       label: 'Id de actualización',
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
               perPage: 10
           }
       },
       mounted()
       {
           this.initializeTable(this.items);
           this.update();
       },
       methods: {
           initializeTable(entry_data)
           {
               for (let index = 0; index < entry_data.length; index++)
               {
                   if(entry_data[index]['value'] > 50)
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
           update()
           {
               Echo.channel('table').listen('TableUpdater', (e) =>
               {
                   for (let index = 0; index < e.message.length; index++)
                   {
                       // Change the sensors limit.
                       if(e.message[index]['value'] > 50)
                       {
                           e.message[index]['_rowVariant'] = 'danger';
                       }
                   }
                   this.items = e.message;
                   this.updateTable();
               });
           },
           onFiltered(filteredItems)
           {
               this.totalRows = filteredItems.length;
               this.currentPage = 1;
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
