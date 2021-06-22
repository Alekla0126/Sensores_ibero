<template>
    <div class="grid-item item3">
        <div class="card">
            <div class="card-body">
                <b-table
                    responsive
                    ref="table"
                    hover
                    sticky-header
                    :items="items"
                    :fields="fields"
                    @row-clicked="onRowClick"
                >
                </b-table>
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
                       label: 'Número del dispositivo',
                       key: 'id',
                       sortable: true
                   },
                   {
                       label: 'Número del salón',
                       key: 'device_id',
                       sortable: true
                   },
                   {
                       label: 'Temperatura',
                       key: 'value',
                       sortable: true,
                       // variant: 'danger'
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
                   {
                       label: 'Creado en ',
                       key: 'created_at',
                       sortable: true,
                   }
               ],
               items: this.devices
           }
       },
       mounted()
       {
           this.update();
       },
       methods: {
           updateTable()
           {
               this.$refs.table.refresh();
           },
           onRowClick(record, index)
           {
               window.location.href = '/' + 'graph' + '/' + record['id'];
           },
           update()
           {
               Echo.channel('table').listen('TableUpdater', (e) =>
               {
                   this.items = e.message;
                   for (let index = 0; index < e.message.length; index++)
                   {
                       if(e.message[index]['value'] > 40)
                       {
                           e.message[index]['_rowVariant'] = 'danger';
                       }
                   }
                   this.updateTable();
               });
           }
       }
   }
</script>
