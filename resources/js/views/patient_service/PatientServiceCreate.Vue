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
                    @change="$v.patientservicesData.patient.$touch()"
                    @blur="$v.patientservicesData.patient.$touch()"
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
                    @input="$v.patientservicesData.organization.$touch()"
                    @blur="$v.patientservicesData.organization.$touch()"
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
                              Services
                            </th>
                            <th class="text-left">
                              Procedures
                            </th>
                            <th class="text-left" width="150px">
                              Price (PHP)
                            </th>
                            <th class="text-left" width="150px">
                              Discount (%)
                            </th>
                            <th class="text-left" width="150px">
                              Discount (PHP)
                            </th>
                            <th class="text-left" width="150px">
                              Total Amount (PHP)
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
                              <v-autocomplete
                                name="service"
                                v-model="service_id"
                                :items="services"
                                item-text="service"
                                item-value="id"     
                                label="Service"
                                v-on:change="serviceOnChange()"
                                v-if="service_procedures.indexOf(item) == (service_procedures.length - 1) && !item.service"
                              >
                              </v-autocomplete>   

                              {{ item.service }}

                              {{ getIndex(service_procedures.indexOf(item)) }}

                            </td>
                            <td>
                              <v-autocomplete
                                name="procedure"
                                v-model="procedure_id"
                                :items="procedure_per_service"
                                item-text="procedure"
                                item-value="id"     
                                label="Procedure"
                                :disabled="procedureDisabled"
                                v-on:change="procedureOnChange()"
                                v-if="service_procedures.indexOf(item) == (service_procedures.length - 1) && !item.procedure"
                              >
                              </v-autocomplete>   
  
                              {{ item.procedure }}

                              {{ getIndex(service_procedures.indexOf(item)) }}

                            </td>
                            <td>
                              <v-text-field-money
                                class="mt-2"
                                v-model="price"
                                v-bind:properties="{
                                  name: 'price',
                                  prefix: '₱',
                                  placeholder: '0.00',
                                  disabled: priceDisabled,
                                  keyup:computeTotalAmount()
                                }"
                                v-bind:options="{
                                  length: 11,
                                  precision: 2,
                                  empty: null,
                                }"
                                :disabled="priceDisabled"
                                v-if="service_procedures.indexOf(item) == (service_procedures.length - 1) && !item.price"
                              >
                              </v-text-field-money>

                              <strong v-if="item.price">
                                ₱
                              </strong> 

                              {{ item.price }}

                            </td>
                            <td>
                              <v-text-field-money
                                class="mt-2"
                                v-model="discount"
                                v-bind:properties="{
                                  name: 'discount',
                                  suffix: '%',
                                  placeholder: '0.00',
                                  disabled: discountDisabled,
                                  keyup:computeTotalAmount()
                                }"
                                v-bind:options="{
                                  length: 4,
                                  precision: 2,
                                  empty: null,
                                }"
                                :disabled="discountDisabled"
                                v-if="service_procedures.indexOf(item) == (service_procedures.length - 1) && !item.discount"
                              >
                              </v-text-field-money> 

                              {{ item.discount }}

                              <strong v-if="item.discount">
                                %
                              </strong>

                            </td>
                            <td>
                              <v-text-field-money
                                class="mt-2"
                                v-model="discount_amt"
                                v-bind:properties="{
                                  name: 'discount_amt',
                                  prefix: '₱',
                                  placeholder: '0.00',
                                  disabled: discount_amtDisabled,
                                  keyup:computeTotalAmount()
                                }"
                                v-bind:options="{
                                  length: 11,
                                  precision: 2,
                                  empty: null,
                                }"
                                v-if="service_procedures.indexOf(item) == (service_procedures.length - 1) && !item.discount_amt"
                              >
                              </v-text-field-money>

                              <strong v-if="item.discount_amt">
                                ₱
                              </strong> 

                              {{ item.discount_amt }}

                            </td>
                            <td>
                              <v-text-field-money
                                class="mt-2"
                                v-model="total_amount"
                                v-bind:properties="{
                                  name: 'total_amount',
                                  prefix: '₱',
                                  placeholder: '0.00',
                                  readonly: true,
                                  disabled: total_amtDisabled,
                                  
                                }"
                                v-bind:options="{
                                  length: 11,
                                  precision: 2,
                                  empty: null,
                                  
                                }"
                                v-if="service_procedures.indexOf(item) == (service_procedures.length - 1) && !item.total_amount"
                              >
                              </v-text-field-money>

                              <strong v-if="item.total_amount">
                                ₱
                              </strong> 

                              {{ item.total_amount }}

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
                            <td colspan="5">
                              <strong><span class="pull-right">Grand Total :</span></strong>
                            </td>
                            <td><strong><span class="service-grand-total">₱ {{ patientservicesData.grand_total = computedGrandTotal }}</span></strong></td>
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
                  <span class="v-messages error--text" v-if="serviceHasError || procedureHasError || priceHasError">Service, Procedure and Price are required</span>
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
    },

    data: vm => ({
      date: new Date().toISOString().substr(0, 10),
      dateFormatted: vm.formatDate(new Date().toISOString().substr(0, 10)),
      input: false,
      LastIndexIsRemoved: false,
      serviceHasError: false,
      procedureHasError: false,
      priceHasError: false,
      disabled: false,
      procedureDisabled: true,
      priceDisabled: true,
      discountDisabled: true,
      discount_amtDisabled: true,
      total_amtDisabled: true,
      patients: [],
      service_id: "",
      service: "",
      services: [],
      procedure_id: "",
      procedure: "",
      procedures: [],
      procedure_per_service: [],
      service_procedures: [],
      price: "",
      discount: "",
      discount_amt: "",
      total_amount: "",
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
        grand_total: '0.00',
      }
    }),

    computed: {

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

      computedGrandTotal() {
        
        let total_amount = this.total_amount;

        if(!this.total_amount)
        { 
          total_amount = 0;
        }

        let grand_total = parseFloat(total_amount);
        
        return this.service_procedures.reduce(function (sum, item) {
          
          if(item.total_amount)
          {
            grand_total = parseFloat(grand_total) + parseFloat(item.total_amount);
          }

          return parseFloat(grand_total).toFixed(2);

        }, '0.00')
      }
      
    },
    watch: {
      date (val) {
        this.dateFormatted = this.formatDate(this.date)
      },
    },
    methods: {
      createPatientService() {
        
        this.$v.patientservicesData.$touch();
        
        //if type is individual then reset validation for organization else reset validation for patient
        if(this.patientservicesData.type == 'individual')
        {
          this.$v.patientservicesData.organization.$reset();
        }
        else
        {
          this.$v.patientservicesData.patient.$reset();
        }

        let nameHasError = false;

        if(this.$v.patientservicesData.patient.$error || this.$v.patientservicesData.organization.$error) 
        {
           nameHasError = true;
        }

        if(!this.LastIndexIsRemoved)
        {
          this.serviceValidate();
          this.procedureValidate();
          this.priceValidate();
        }

        if(this.service_procedures.length == 0)
        { 
          this.serviceHasError = true;
          this.procedureHasError = true;
          this.priceHasError = true;
        }
        
        if(!nameHasError && !this.serviceHasError && !this.procedureHasError && !this.priceHasError)
        {
          const procedure_data = { 
              service_id: this.service_id, 
              service: this.service, 
              procedure_id: this.procedure_id, 
              procedure: this.procedure, 
              price: this.price, 
              discount: this.discount,
              discount_amt: this.discount_amt,
              total_amount: parseFloat(this.total_amount).toFixed(2),
          };

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

          this.procedures = response.data.procedures;
        });
      },

      getIndex(index) {
        this.index = index;
      },

      addRow() {

        if(this.service_procedures.length == 0)
        {

          this.service_procedures.push({ 
            service_id: this.service_id, 
            service: this.service, 
            procedure_id: this.procedure_id, 
            procedure: this.procedure, 
            price: this.price, 
            discount: this.discount,
            discount_amt: this.discount_amt,
            total_amount: this.total_amount,
          });

          this.serviceHasError = false;
          this.procedureHasError = false;
          this.priceHasError = false;

        }
        else
        { 

          if(!this.discount)
          {
            this.discount = '0.00';
          }

          if(!this.discount_amt)
          {
            this.discount_amt = '0.00';
          }
          //if last index was removed then don't validate fields
          if(!this.LastIndexIsRemoved)
          { 
            this.serviceValidate();
            this.procedureValidate();
            this.priceValidate();
          }
          
          if(!this.serviceHasError && !this.procedureHasError && !this.priceHasError)
          {
            
            const data = { 
              service_id: this.service_id, 
              service: this.service, 
              procedure_id: this.procedure_id, 
              procedure: this.procedure, 
              price: this.price, 
              discount: this.discount,
              discount_amt: this.discount_amt,
              total_amount: parseFloat(this.total_amount).toFixed(2),
            };

            //if last index was removed and procedures has only 1 row data, don't assign data on procedures
            if(!this.LastIndexIsRemoved)
            {
              Object.assign(this.service_procedures[this.index], data);
            }

            this.reset();  

            this.service_procedures.push({ 
              service_id: this.service_id, 
              service: this.service, 
              procedure_id: this.procedure_id, 
              procedure: this.procedure, 
              price: this.price, 
              discount: this.discount,
              discount_amt: this.discount_amt,
              total_amount: this.total_amount,
            });

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
          this.serviceHasError = false;
          this.procedureHasError = false;
          this.priceHasError = false;

          this.reset();
          
          // if table has records
          if(this.service_procedures.length > 0)
          {
            this.LastIndexIsRemoved = true;
          }
        }

      },

      serviceOnChange() {
        
        this.procedureDisabled = false;
        this.priceDisabled = true;
        this.discountDisabled = true;
        this.discount_amtDisabled = true;
        this.total_amtDisabled = true;
        this.price = "";
        this.procedure_id = "";
        this.procedure = "";
        this.discount = "";
        this.discount_amt = "";
        this.procedure_per_service = [];
        const data = {};

        //services
        for(let [key, val] of this.services.entries())
        { 
          if(this.service_id == val.id)
          {
            this.service = val.service;
          }
        } 

        //procedures
        for(let [key, val] of this.procedures.entries())
        { 
          if(this.service_id == val.serviceid)
          {
            this.procedure_per_service.push({id: val.id, procedure: val.procedure, price: val.price});
          }
        }      
 
      },

      procedureOnChange() {

        this.priceDisabled = false;
        this.discountDisabled = false;
        this.discount_amtDisabled = false;
        this.total_amtDisabled = false;
        this.discount = "";
        this.discount_amt = "";

        for(let [key, val] of this.procedures.entries())
        { 
          if(this.procedure_id == val.id)
          { 
            this.procedure = val.procedure;
            this.price = val.price;
            this.total_amount = val.price;
          }
        }
      
      },

      serviceValidate() {
        
        // const procedure = document.getElementById("procedure").value;

        if(this.procedure_id)
        {
          this.serviceHasError = false;
        }
        else
        {
          this.serviceHasError = true;
        }
      
      },

      procedureValidate() {
        
        // const procedure = document.getElementById("procedure").value;

        if(this.procedure_id)
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

        if(parseFloat(this.price) > 0)
        {
          this.priceHasError = false;
        }
        else
        {
          this.priceHasError = true;
        }
      }, 

      computeTotalAmount() {

        if(!this.price)
        {
          this.price = '0.00';
        }

        if(!this.discount)
        {
          this.discount = '0.00';
        }

        if(!this.discount_amt)
        {
          this.discount_amt = '0.00';
        }

        let discounted_price = parseFloat(this.price) * (parseFloat(this.discount) / 100);
    
        this.total_amount = parseFloat(this.price) - parseFloat(discounted_price) - parseFloat(this.discount_amt);

      },

      clear() {
        this.$v.$reset();
        this.LastIndexIsRemoved = false;
        this.disabled = false;
        this.patientservicesData.type = "individual";
        this.patientservicesData.patient = "";
        this.patientservicesData.organization = "";
        this.patientservicesData.or_number = "";
        this.patientservicesData.bloodpressure = "";
        this.patientservicesData.temperature = "";
        this.patientservicesData.weight = "";
        this.patientservicesData.grand_total = "0.00";
        this.service_procedures = [];
        this.index = "";
        this.reset();
      },

      reset() {
        this.serviceHasError = false;
        this.procedureHasError = false;
        this.priceHasError = false;
        this.procedureDisabled = true;
        this.priceDisabled = true;
        this.discountDisabled = true;
        this.discount_amtDisabled = true;
        this.total_amtDisabled = true;
        this.procedure_per_service = [];
        this.procedure_id = "";
        this.procedure = "";
        this.service_id = "";
        this.service = "";
        this.price = "";
        this.discount = "";
        this.discount_amt = "";
        this.total_amount = "";
        this.LastIndexIsRemoved = false;
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
      
      this.getPatient();
      this.getService();
      this.getProcedure();

    }
  };
</script>