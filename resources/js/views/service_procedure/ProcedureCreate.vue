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
                <v-col cols="6">
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
                <v-col cols="6">
                  <v-card>
                    <v-card-title>
                      Service Procedures
                    </v-card-title>
                    <v-simple-table>
                      <template v-slot:default>
                        <thead>
                          <tr>
                            <th class="text-left">
                              Procedure
                            </th>
                            <th class="text-left">
                              Price (PHP)
                            </th>
                            <th>
                              
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
                                class="mt-5"
                                :name="'procedure'+item.id"
                                :key="item.id"
                                dense
                                required
                                @keyup="procedureValidate(item.id)"
                                @blur="procedureValidate(item.id)"
                              ></v-text-field>
                            </td>
                            <td>
                              <v-text-field
                                class="mt-5"
                                :name="'price'+item.id"
                                placeholder="0.00"
                                prefix="₱"
                                dense
                                required
                                @="priceValidate(item.id)"
                                @blur="priceValidate(item.id)"
                              ></v-text-field>
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

      if(!this.$v.$error)
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
      
      this.ctr = this.ctr + 1;
      this.procedures.push({id: this.ctr});

    },

    removeRow() {

    },

    procedureValidate(id) {
      
      const procedure = document.querySelector("input[name=procedure"+id+"]").value;
      this.procedure = procedure;

      if(procedure)
      {
        this.procedureHasError = false;
      }
      else
      {
        this.procedureHasError = true;
      }
    
    },

    priceValidate(id) {
      
      const price = document.querySelector("input[name=price"+id+"]").value;
      this.price = price;

      if(price)
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