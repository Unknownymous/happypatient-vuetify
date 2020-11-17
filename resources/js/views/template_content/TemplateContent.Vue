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
            Template Content
          </v-card-title>
          <v-card-text class="pa-10">
            <form id="templateform">
              <v-row>
                <v-col>
                  <vue-editor v-model="content"></vue-editor>
                </v-col>
              </v-row>
              
              <v-btn class="mr-4 mt-5" color="primary" @click="saveTemplate"> save </v-btn>
              <v-btn class="mt-5" color="#E0E0E0" :to="{name: 'procedure.index'}"> cancel </v-btn>
            </form>
          </v-card-text>
        </v-card>
      </v-main>
    </div>
  </div>
</template>

<script>

  let access_token;

  import Axios from 'axios';
  import { validationMixin } from "vuelidate";
  import { required, maxLength, email, minLength, sameAs } from "vuelidate/lib/validators";
  // Advanced Use - Hook into Quill's API for Custom Functionality
  import { VueEditor, Quill } from "vue2-editor";

  export default {
    mixins: [validationMixin],
    components: {
      VueEditor
    },
    validations: {
      service: { required },   
      procedure: { required },    
      price: { required },
    },

    data: () => ({
      
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
            text: "Template Content",
            disabled: true,
          },
        ],
        template: [],
        content: null,
    }),

    computed: {

      templateErrors() {
        const errors = [];
        if (!this.$v.service.$dirty) return errors;
        !this.$v.service.required && errors.push("Service is required.");
        return errors;
      },
      
    },
    methods: {
      saveTemplate() { 
      
          const data = {content: this.content};
          const procedureid = this.template.procedureid;

          Axios.post('/api/procedure/template/update/'+procedureid, data, {
              headers: {
                'Authorization': 'Bearer '+access_token,
              }
            }).then((response) => {
            
            if(response.data.success)
            { 
              this.showAlert();
            }

          }, (errors) => {
            console.log(errors);
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

      getTemplateContent() {

        Axios.get('/api'+this.$route.path, {
              headers: {
                'Authorization': 'Bearer '+access_token,
              }
            }).then((response) => {
          this.template = response.data.template_content;
          this.content = this.template.content;
          console.log(this.template);
        });
      },
    },

    mounted () {
      access_token = localStorage.getItem('access_token');
      this.getTemplateContent();
    }
  };
</script>