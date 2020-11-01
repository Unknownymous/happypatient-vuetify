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
              <v-btn class="mr-4" color="primary" @click="createProcedure" :disabled="disabled"> add </v-btn>
              <v-btn color="#E0E0E0" :to="{name: 'procedure.index'}"> cancel </v-btn>
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
  },

  data: () => ({
    disabled: false,
    service: "",
    services: [],
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

        let myForm = document.getElementById('userform');
        let formData = new FormData(myForm);
        const data = {};

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
  },
  mounted () {
    this.getService();
  }
};
</script>