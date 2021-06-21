<template>
    <div class="grid-item item3">
        <div class="card">
            <div class="card-body">
                <b-table responsive ref="table" hover :items="items" :fields="fields" @row-click="onRowClick"></b-table>
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
                   },
                   {
                       label: 'Visualizar',
                       key: 'link',
                       sortable: true,
                   }
               ],
               items: this.devices
           }
       },
       mounted()
       {
           this.addLinks(this.devices);
           this.update();
       },
       methods: {
           updateTable()
           {
               this.$refs.table.refresh();
           },
           addLinks(obj)
           {
               for (let index = 0; index < obj.length; index++)
               {
                   obj[index]['link'] = window.location.hostname + '/' + (index+1).toString();
               }
           },
           onRowClick(event)
           {
               window.location.href = window.location.hostname + '/' + '${event.row.id}';
           },
           update()
           {
               Echo.channel('table').listen('TableUpdater', (e) =>
               {
                   this.addLinks(e.message);
                   this.items = e.message;
                   this.updateTable();
               });
           }

       }
   }
</script>
