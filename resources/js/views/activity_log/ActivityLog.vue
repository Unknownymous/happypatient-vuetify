<template>
  <div class="flex column">
    <div id="_wrapper" class="pa-5">
      <v-main>
        <v-breadcrumbs :items="items">
          <template v-slot:item="{ item }">
            <v-breadcrumbs-item
              :to="item.link"
              :disabled="item.disabled"
            >
              {{ item.text.toUpperCase() }}
            </v-breadcrumbs-item>
          </template>
        </v-breadcrumbs>
        <v-card>
          <v-card-title>
            Activity Logs
            <v-spacer></v-spacer>
            <v-text-field
              v-model="search"
              append-icon="mdi-magnify"
              label="Search"
              single-line
              hide-details
            ></v-text-field>
            <v-spacer></v-spacer>
          </v-card-title>
          <v-data-table
            :headers="headers"
            :items="activity_logs"
            :search="search"
          > 
          </v-data-table>
          
        </v-card>
      </v-main>
    </div>
  </div>
</template>
<script>
  import Axios from "axios";

  export default {
    data() {
      return {
        search: "",
        headers: [
          { text: "Date", value: "created_at" },
          { text: "Activity", value: "description" },
          { text: "User", value: "username" },
        ],
        activity_logs: [],
        items: [
          { 
            text: 'Home', 
            disabled: false, 
            link: '/dashboard',
          },
          { 
            text: 'Activity Logs', 
            disabled: true, 
          }
        ]
      }
    },

    methods: {
      getActivityLogs(){
        Axios.get('/api/activity_logs').then( (response) => {
          console.log(response.data.activity_logs);
          this.activity_logs = response.data.activity_logs;
        });
      },
    },
    mounted() {
      this.getActivityLogs();
    },
  };
</script>