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
          
            <!-- <template v-slot:item.rows="{item}">
              {{ index(item) }}
            </template> -->


            <template v-slot:item.birthdate="{ item }">
              {{ getBirthdate(item.birthdate) }}
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
                @click="showConfirmAlert(item)"
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

  const access_token = localStorage.getItem('access_token');

  import Axios from "axios";
  import moment from 'moment';
  export default {
    data() {
      return {
        search: "",
        headers: [
          // {
          //   text: "#",
          //   align: "start",
          //   value: "rows",
          //   sortable: false
          // },
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
        items: [
          { 
            text: 'Home', 
            disabled: false, 
            link: '/dashboard',
          },
          { 
            text: 'Patients Record', 
            disabled: true, 
          }
        ]
      }
    },

    methods: {
      getPatients(){

        Axios.get('/api/patient/index', {
            headers: {
              'Authorization': 'Bearer '+access_token,
            }
          }).then( (response) => {
          console.log(response.data.patients);
          this.patients = response.data.patients;
        });
      },

      getBirthdate(birthdate) {
        return moment(String(birthdate)).format('MM/DD/YYYY');
      },

      editPatient (item) {
        this.$router.push({ name: 'patient.edit', params: { patientid: item.id} });
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
      index(item){
        return this.patients.indexOf(item) + 1;
      }
    },
    mounted() {
      this.getPatients();
    },
  };
</script>