<template>
  <div class="flex column">
    <div id="_wrapper" class="pa-5">
      <v-main>
        <v-card>
          <v-card-title>
            Patients Record
            <v-spacer></v-spacer>
            <v-text-field
              v-model="search"
              append-icon="mdi-magnify"
              label="Search"
              single-line
              hide-details
            ></v-text-field>
            <v-spacer></v-spacer>
            <v-btn
              color="primary"
              fab
              dark
              class="mb-2"
              :to="{ name: 'patient.create' }"
            >
              <v-icon>mdi-plus</v-icon>
            </v-btn>
          </v-card-title>
          <v-data-table
            :headers="headers"
            :items="patients"
            :search="search"
          > 
          
            <template v-slot:item.rows="{item}">
              {{ index(item) }}
            </template>

            <template v-slot:item.actions="{ item }">
              <v-icon
                small
                class="mr-2"
                color="green"
                @click="editPatient(item)"
              >
                mdi-pencil
                
              </v-icon>
              <v-icon
                small
                color="red"
                @click="deletePatient(item)"
              >
                mdi-delete
              </v-icon>
            </template>
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
          {
            text: "#",
            align: "start",
            value: "rows",
            sortable: false
          },
          {
            text: "Lastname",
            value: "lastname",
          },
          { text: "Firstname", value: "firstname" },
          { text: "Middlename", value: "middlename" },
          { text: "Birthdate", value: "birthdate" },
          { text: "Gender", value: "gender" },
          { text: "Civil Status", value: "civilstatus" },
          { text: "Mobile", value: "mobile" },
          { text: "Actions", value: "actions", sortable: false },
        ],
        patients: [],
      }
    },

    methods: {
      getPatients(){
        Axios.get('/patient/index').then((response) => {
          console.log(response.data.patients);
          this.patients = response.data.patients;
        });
      },

      editPatient (item) {
        this.editedIndex = this.patients.indexOf(item)
        this.editedItem = Object.assign({}, item)
        console.log(item);
      },

      deletePatient (item) {
        this.editedIndex = this.patients.indexOf(item)
        this.editedItem = Object.assign({}, item)
      },

      showAlert() {
        this.$swal({
          position: 'center',
          icon: 'success',
          title: 'Record has successfully added',
          showConfirmButton: false,
          timer: 2500
        });
      },

      showConfirmAlert() {
        this.$swal({
          title: "Delete this order status?",
          text: "Are you sure? You won't be able to revert this!",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          confirmButtonText: "Yes, Delete it!"
        }).then((result) => { // <--
            if (result.value) { // <-- if confirmed
                this.$swal({
                  position: 'center',
                  icon: 'success',
                  title: 'Record has been deleted',
                  showConfirmButton: false,
                  timer: 2500
                });
            }
        });
      },
      index(item){
        return this.patients.indexOf(item);
      }
    },
    mounted() {
      this.getPatients();
    },
  };
</script>