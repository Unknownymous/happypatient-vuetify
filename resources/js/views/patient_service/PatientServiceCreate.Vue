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
          <v-card-title class="grey darken-2  text-white">
            Create Patient Services
          </v-card-title>
          <v-card-text class="pa-10">
            <form id="procedureform">
              <v-row>
                <v-col cols="2">
                  <v-radio-group name="type" v-model="patientservicesData.type">
                    <template v-slot:label>
                      <div><strong>Type</strong></div>
                    </template>
                    <v-radio value="individual">
                      <template v-slot:label>
                        <div>Individual</div>
                      </template>
                    </v-radio>
                    <v-radio value="group">
                      <template v-slot:label>
                        <div>Group</div>
                      </template>
                    </v-radio>
                  </v-radio-group>
                </v-col>

                <v-col cols="4">
                  <v-autocomplete
                    name="patient"
                    v-model="patientservicesData.patient"
                    :items="patients"
                    item-value="id"     
                    label="Patient"
                    required
                    :error-messages="patientErrors"
                    @change="$v.service.$touch()"
                    @blur="$v.service.$touch()"
                    v-if="patientservicesData.type == 'individual'"
                  >
                    <template slot="selection" slot-scope="data">
                      {{data.item.id + " - " + data.item.lastname + ", " + data.item.firstname}}&nbsp;<span v-if="data.item.middlename">{{data.item.middlename}}</span>
                    </template>
                    <template slot="item" slot-scope="data">
                      {{data.item.id + " - " + data.item.lastname + ", " + data.item.firstname}}&nbsp;<span v-if="data.item.middlename">{{data.item.middlename}}</span>
                    </template>
                  </v-autocomplete>

                  <v-text-field
                    name="organization"
                    v-model="patientservicesData.organization"
                    :error-messages="organizationErrors"
                    label="Organization"
                    required
                    @input="$v.organization.$touch()"
                    @blur="$v.organization.$touch()"
                    v-if="patientservicesData.type == 'group'"
                  ></v-text-field>

                </v-col>

                <v-col cols="3">
                  <v-text-field
                    name="or_number."
                    v-model="patientservicesData.or_number"
                    label="Official Receipt No."
                  ></v-text-field>
                </v-col>
                <v-col cols="3">
                  <v-menu
                    v-model="input"
                    :close-on-content-click="false"
                    transition="scale-transition"
                    offset-y
                    max-width="290px"
                    min-width="290px"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        name="docdate"
                        v-model="computedDateFormatted"
                        label="Document Date"
                        hint="MM/DD/YYYY format"
                        persistent-hint
                        prepend-icon="mdi-calendar"
                        readonly
                        v-bind="attrs"
                        v-on="on"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="date"
                      no-title
                      @input="input = false"
                    ></v-date-picker>
                  </v-menu>
                </v-col>
              </v-row>
              <v-divider></v-divider>
              <v-row>
                <v-col cols="4">
                  <v-text-field
                    name="bloodpressure"
                    v-model="patientservicesData.bloodpressure"
                    label="Bloodpressure"
                  ></v-text-field>
                </v-col>
                <v-col cols="4">
                  <v-text-field-money
                    name="temperature"
                    v-model="patientservicesData.temperature"
                    label="Temperature (°C)"
                    v-bind:properties="{
                      placeholder: '00.0',
                    }"
                    v-bind:options="{
                      length: 3,
                      precision: 1,
                      empty: null,
                    }"

                  >
                  </v-text-field-money>
                </v-col>
                <v-col cols="4">
                  <v-text-field-money
                    name="weight"
                    v-model="patientservicesData.weight"
                    label="Weight (Kg)"
                    v-bind:properties="{
                      placeholder: '00.0',
                    }"
                    v-bind:options="{
                      length: 4,
                      precision: 2,
                      empty: null,
                    }"

                  >
                  </v-text-field-money>
                </v-col>
              </v-row>
              <v-divider></v-divider>
              <v-row>
                <v-col>
                  <v-card>
                    <v-card-title>
                      Service Procedures
                    </v-card-title>
                    <v-simple-table id="procedureTable">
                      <template v-slot:default>
                        <thead>
                          <tr>
                            <th class="text-left">
                              Procedure
                            </th>
                            <th class="text-left" width="150px">
                              Price (PHP)
                            </th>
                            <th width="100px">
                              
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr
                            v-for="item in service_procedures"
                            :key="item.id"
                          >
                            <td>
                              <v-text-field
                                v-model="procedure"
                                class="mt-5"
                                id="procedure"
                                dense
                                required
                                v-if="service_procedures.indexOf(item) == (service_procedures.length - 1) && !item.procedure"
                              ></v-text-field>   

                              {{ item.procedure }}

                              {{ getIndex(service_procedures.indexOf(item)) }}

                            </td>
                            <td>
                              <v-text-field-money
                                class="mt-2"
                                v-model="price"
                                v-bind:properties="{
                                  prefix: '₱',
                                  placeholder: '0.00',
                                }"
                                v-bind:options="{
                                  length: 11,
                                  precision: 2,
                                  empty: null,
                                }"
                                dense
                                required
                                v-if="procedures.indexOf(item) == (procedures.length - 1) && !item.price"
                              >
                              </v-text-field-money>

                              <strong v-if="item.price">
                                ₱
                              </strong> 

                              {{ item.price }}

                            </td>
                            <td>
                              <v-btn
                                  class="mx-2"
                                  fab
                                  dark
                                  small
                                  color="red"
                                  @click="removeRow(item)"
                                >
                                  <v-icon dark>
                                    mdi-minus
                                  </v-icon>
                              </v-btn>
                            </td>
                          </tr>
                        </tbody>
                        <tfoot>
                          <tr>
                            <td colspan="1"></td>
                            <td>
                              <td>
                                <v-btn
                                  class="mx-2 mb-5 mt-5"
                                  fab
                                  dark
                                  small
                                  color="primary"
                                  @click="addRow"
                                >
                                  <v-icon dark>
                                    mdi-plus
                                  </v-icon>
                                </v-btn>
                            </td>
                          </tr>
                        </tfoot>
                      </template>
                    </v-simple-table>
                  </v-card>
                  <span class="v-messages error--text" v-if="procedureHasError || priceHasError">Procedure and Price are required</span>
                </v-col>
              </v-row>
              <v-btn class="mr-4 mt-5" color="primary" @click="createPatientService" :disabled="disabled"> add </v-btn>
              <v-btn class="mt-5" color="#E0E0E0" :to="{name: 'procedure.index'}"> cancel </v-btn>
            </form>
          </v-card-text>
        </v-card>
      </v-main>
    </div>
  </div>
</template>

<script>

  const access_token = localStorage.getItem('access_token');

  import Axios from 'axios';
  import { validationMixin } from "vuelidate";
  import { required, maxLength, email, minLength, sameAs } from "vuelidate/lib/validators";

  export default {
    mixins: [validationMixin],

    validations: {
      patientservicesData: {
        patient: { required },   
        organization: { required },
      },
      service: { required },
      procedure: { required },    
      price: { required },
      discount: { required },
      discount_amt: { required },
    },

    data: vm => ({
      date: new Date().toISOString().substr(0, 10),
      dateFormatted: vm.formatDate(new Date().toISOString().substr(0, 10)),
      input: false,
      LastIndexIsRemoved: false,
      procedureHasError: false,
      priceHasError: false,
      disabled: false,
      patients: [],
      service: "",
      services: [],
      procedure: "",
      procedures: [],
      service_procedures: [],
      price: "",
      prices: [],
      discount: "",
      discounts: [],
      discount_amt: "",
      discount_amts: [],
      index: "",
      ctr: 0,
      items: [
          {
            text: "Home",
            disabled: false,
            link: "/dashboard",
          },
          {
            text: "Create Patient Services",
            disabled: true,
          },
        ],
      patientservicesData: {
        type: "individual",
        patient: "",
        organization: "",
        or_number: "",
        docdate: "",
        bloodpressure: "",
        temperature: "",
        weight: "",
      }
    }),

    computed: {

      serviceErrors() {
        const errors = [];
        if (!this.$v.service.$dirty) return errors;
        !this.$v.service.required && errors.push("Service is required.");
        return errors;
      },

      patientErrors() {
        const errors = [];
        if (!this.$v.patientservicesData.patient.$dirty) return errors;
        !this.$v.patientservicesData.patient.required && errors.push("Patient is required.");
        return errors;
      },

      organizationErrors() {
        const errors = [];
        if (!this.$v.patientservicesData.organization.$dirty) return errors;
        !this.$v.patientservicesData.organization.required && errors.push("Organization is required.");
        return errors;
      },

      computedDateFormatted () {
        this.patientservicesData.docdate = this.formatDate(this.date);
        return this.patientservicesData.docdate
      },
      
    },
    watch: {
      date (val) {
        this.dateFormatted = this.formatDate(this.date)
      },
    },
    methods: {
      createPatientService() {
        
        this.$v.$touch();

        if(!this.LastIndexIsRemoved)
        {
          this.procedureValidate();
          this.priceValidate();
        }
        else
        {
          this.$v.$reset();
        }

        if(this.service_procedures.length == 0)
        {
          this.procedureHasError = true;
          this.priceHasError = true;
        }
        
      
        if(!this.$v.$error && !this.procedureHasError && !this.priceHasError)
        {
          const procedure_data = { procedure: this.procedure, price: this.price };

          //Assign data from last row textfield value of procedure and price; if last index was not removed
          if(!this.LastIndexIsRemoved)
          {
            Object.assign(this.service_procedures[this.index], procedure_data);
          }

          this.disabled = true;

          let myForm = document.getElementById('procedureform');
          let formData = new FormData(myForm);
          const data = this.patientservicesData;

          //Assign procedures data into data variable
          data['service_procedures'] = this.service_procedures;

          Axios.post('/api/patientservice/store', data, {
              headers: {
                'Authorization': 'Bearer '+access_token,
              }
            }).then((response) => {

            console.log(response.data);
            
            if(response.data.success)
            {
              this.clear();
              this.showAlert(); 
              this.getService();
              
            }

            this.disabled = false;

          }, (errors) => {
            console.log(errors);
          });

        }
        
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

      getPatient() {

        Axios.get("/api/patient/index", {
              headers: {
                'Authorization': 'Bearer '+access_token,
              }
            }).then((response) => {
          this.patients = response.data.patients;
        });
      },

      getService() {

        Axios.get("/api/service/index", {
              headers: {
                'Authorization': 'Bearer '+access_token,
              }
            }).then((response) => {
          this.services = response.data.services;
        });
      },

      getProcedure() {

        Axios.get("/api/procedure/index", {
              headers: {
                'Authorization': 'Bearer '+access_token,
              }
            }).then((response) => {
          this.procedures = response.data.services;
        });
      },

      getIndex(index) {
        this.index = index;
      },

      addRow() {

        if(this.service_procedures.length == 0)
        {

          this.service_procedures.push({ procedure: this.procedure, price: this.price });
          this.procedureHasError = false;
          this.priceHasError = false;

        }
        else
        { 
          
          //if last index was removed then don't validate fields
          if(!this.LastIndexIsRemoved)
          {
            this.procedureValidate();
            this.priceValidate();
          }
          
          if(this.procedureHasError == false && this.priceHasError == false)
          {
            
            const data = { procedure: this.procedure, price: this.price };

            //if last index was removed and procedures has only 1 row data, don't assign data on procedures
            if(!this.LastIndexIsRemoved)
            {
              Object.assign(this.service_procedures[this.index], data);
            }
            else if(this.service_procedures.length == 1)
            {
              Object.assign(this.service_procedures[this.index], data);
            }

            this.procedure = "";
            this.price = "";
            this.LastIndexIsRemoved = false;

            this.procedures.push({ procedure: this.procedure, price: this.price });

          }
          
        }
      },

      removeRow(item) {

        const index = this.service_procedures.indexOf(item);
        const last_index = this.service_procedures.length - 1;

        //Delete rows on the object service_procedures
        this.service_procedures.splice(index, 1);

        if(index == last_index)
        { 
          this.procedureHasError = false;
          this.priceHasError = false;

          this.procedure = "";
          this.price = "";
          
          this.LastIndexIsRemoved = true;
        }

      },

      procedureValidate() {
        
        // const procedure = document.getElementById("procedure").value;

        if(this.procedure)
        {
          this.procedureHasError = false;
        }
        else
        {
          this.procedureHasError = true;
        }
      
      },

      priceValidate() {
        
        // const price = document.getElementById("input-73").value;

        if(this.price)
        {
          this.priceHasError = false;
        }
        else
        {
          this.priceHasError = true;
        }
      }, 

      clear() {
        this.$v.$reset();
        this.LastIndexIsRemoved = false;
        this.procedureHasError = false;
        this.priceHasError = false;
        this.disabled = false;
        this.procedure = "";
        this.price = "";
        this.service = "";
        this.services = [];
        this.procedures = [];
        this.service_procedures = [];
        this.index = "";
      },
      formatDate (date) {
          if (!date) return null

          const [year, month, day] = date.split('-')
          return `${month}/${day}/${year}`
        },
        parseDate (date) {
          if (!date) return null

          const [month, day, year] = date.split('/')
          return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`
        },
    },
    
    mounted () {
      this.getService();
      this.getPatient();
    }
  };
</script>