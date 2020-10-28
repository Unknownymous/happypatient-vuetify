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
              dark
              class="mb-2"
              :to="{ name: 'patient.create' }"
            >
              <v-icon>mdi-plus </v-icon>
              Create New
            </v-btn>
          </v-card-title>
          <v-data-table
            :headers="headers"
            :items="patients"
            :search="search"
          ></v-data-table>
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
            text: "Lastname",
            align: "start",
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

      editItem (item) {
        this.editedIndex = this.desserts.indexOf(item)
        this.editedItem = Object.assign({}, item)
      },

      deleteItem (item) {
        this.editedIndex = this.desserts.indexOf(item)
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

    },
    mounted() {
      this.getPatients();
    },
  };
</script>