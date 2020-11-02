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
            Create Service Procedure
          </v-card-title>
          <v-card-text class="pa-10">
            <form id="procedureform">
              <v-row>
                <v-col cols="4">
                  <v-autocomplete
                    name="service"
                    v-model="service"
                    :items="services"
                    item-value="id"
                    item-text="service"
                    label="Service"
                    required
                    :error-messages="serviceErrors"
                    @change="$v.service.$touch()"
                    @blur="$v.service.$touch()"
                  ></v-autocomplete>
                </v-col>
              </v-row>
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
                            v-for="item in procedures"
                            :key="item.id"
                          >
                            <td>
                              <v-text-field
                                v-model="procedure"
                                class="mt-5"
                                id="procedure"
                                dense
                                required
                              ></v-text-field>
                              {{ item.procedure }}
                            </td>
                            <td>

                              <!-- <v-text-field
                                class="mt-5"
                                :name="'price'+item.id"
                                placeholder="0.00"
                                prefix="₱"
                                dense
                                required
                              ></v-text-field> -->
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
                              >
                              </v-text-field-money>
                            </td>
                            <td>
                              <v-btn
                                  class="mx-2"
                                  fab
                                  dark
                                  small
                                  color="red"
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
                  <span class="v-messages error--text" v-if="procedureHasError || priceHasError">Procedure and Price required</span>
                </v-col>
              </v-row>
              <v-btn class="mr-4 mt-5" color="primary" @click="createProcedure" :disabled="disabled"> add </v-btn>
              <v-btn class="mt-5" color="#E0E0E0" :to="{name: 'procedure.index'}"> cancel </v-btn>
            </form>
          </v-card-text>
        </v-card>
      </v-main>
    </div>
  </div>
</template>

<script>
import Axios from 'axios';
import { validationMixin } from "vuelidate";
import { required, maxLength, email, minLength, sameAs } from "vuelidate/lib/validators";

export default {
  mixins: [validationMixin],

  validations: {
    service: { required },   
    procedure: { required },    
    price: { required },
  },

  data: () => ({
    procedureHasError: false,
    priceHasError: false,
    disabled: false,
    procedure: "",
    price: "",
    service: "",
    services: [],
    procedures: [],
    indexes: [],
    ctr: 0,
    items: [
        {
          text: "Home",
          disabled: false,
          link: "/dashboard",
        },
        {
          text: "Service Procedures Record",
          disabled: false,
          link: "/procedure/index"
        },
        {
          text: "Create Service Procedures",
          disabled: true,
        },
      ],
  }),

  computed: {

    serviceErrors() {
      const errors = [];
      if (!this.$v.service.$dirty) return errors;
      !this.$v.service.required && errors.push("Service is required.");
      return errors;
    },
     
  },
  methods: {
    createProcedure() {
      
      this.$v.$touch();
      
      if(this.ctr == 0)
      {
        this.procedureHasError = true;
        this.priceHasError = true;
      }

      if(!this.$v.$error && !this.procedureHasError && !this.priceHasError )
      {
        this.disabled = true;

        let myForm = document.getElementById('procedureform');
        let formData = new FormData(myForm);
        const data = {};

        for(let [key, val] of formData.entries())
        {
          Object.assign(data ,{[key]: val});
        }
        Axios.post('/procedure/store', data).then((response) => {
          console.log(response.data);

          if(response.data.success)
          {
            this.clear();
            this.showAlert(); 
          }

          this.disabled = false;

        }, (error) => {
          console.log(error);
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

    getService() {
      Axios.get("/service/index").then((response) => {
        console.log(response.data.services);
        this.services = response.data.services;
      });
    },

    addRow() {
      

      // Validate if last row has valid values
      if(this.ctr > 0)
      { 
        const id = this.ctr;
        this.procedureValidate();
        this.priceValidate();
        
      }
      
      if(this.procedureHasError == false && this.priceHasError == false)
      {
         
        if(this.ctr > 0)
        {
          this.procedures.push({id: id, procedure: this.procedure, price: this.price});
          console.log(this.procedures);
        
        }
        
        this.ctr = this.ctr + 1;
        const id = this.ctr;
        this.procedures.push({id: id, procedure: this.procedure, price: this.price});

        this.procedure = "";
        this.price = "";
      }

      // if(this.ctr == 0)
      // {
      //   this.ctr = this.ctr + 1;
      //   const id = this.ctr;
      //   this.indexes.push({id: id});
        
      //   this.procedureHasError = false;
      //   this.priceHasError = false;

      // }

    },

    removeRow() {

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
  },
  mounted () {
    this.getService();
  }
};
</script>