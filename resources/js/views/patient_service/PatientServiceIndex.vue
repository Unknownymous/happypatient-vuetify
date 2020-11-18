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
            Patient Services Record
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
              :to="{ name: 'patientservice.create' }"
            >
              <v-icon>mdi-plus</v-icon>
            </v-btn>
          </v-card-title>
          <v-data-table
            :headers="headers"
            :items="patient_services"
            :search="search"
          > 

            <template v-slot:item.actions="{ item }">
              <v-btn
                color="primary"
                dark
                x-small
                class="ml-2"
                @click="editPatientService(item)"
              >
                view
              </v-btn>
              
            </template>
          </v-data-table>
          
        </v-card>
      </v-main>
    </div>
  </div>
</template>
<script>

  let access_token;

  import Axios from "axios";
  import moment from 'moment';
  export default {
    data() {
      return {
        search: "",
        headers: [
          { text: "Document Date", value: "docdate" },
          { text: "OR Number", value: "or_number" },
          { text: "Patient/Organization", value: "name" },
          { text: "Cancelled", value: "cancelled" },
          { text: "Actions", value: "actions", sortable: false },
        ],
        patient_services: [],
        items: [
          { 
            text: 'Home', 
            disabled: false, 
            link: '/dashboard',
          },
          { 
            text: 'Patient Services Record', 
            disabled: true, 
          }
        ]
      }
    },

    methods: {
      getPatientServices(){
        Axios.get('/api/patientservice/index', {
            headers: {
              'Authorization': 'Bearer '+access_token,
            }
          }).then( (response) => {
          console.log(response.data);
          this.patient_services = response.data.patient_services;
        });
      },

      getBirthdate(birthdate) {
        return moment(String(birthdate)).format('MM/DD/YYYY');
      },

      editPatientService (item) {
        this.$router.push({ name: 'patientservice.edit', params: { psid: item.id} });
      },

      deletePatient (patientid) {

        const data = { patientid: patientid };

        Axios.post('/patient/delete', data).then( (response) => {
          console.log(response.data);
        }, (error) => {
          console.log(error)
        });

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

      showConfirmAlert(item) {
        this.$swal({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Delete record!'
        }).then((result) => { // <--

            if (result.value) { // <-- if confirmed
                
                
                const patientid = item.id;
                const index = this.patients.indexOf(item);

                //Call delete Patient function
                this.deletePatient(patientid);

                //Remove item from array patients
                this.patients.splice(index, 1);

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
      access_token = localStorage.getItem('access_token');
      this.getPatientServices();
    },
  };
</script>