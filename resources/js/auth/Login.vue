<template>
  <v-app v-if="!user">
    <v-main>
      <v-container
        fluid
        fill-height
      >
        <v-layout
          align-center
          justify-center
        >
          <v-flex
            xs12
            sm8
            md4
          >
            <v-card class="elevation-12">
              <v-toolbar
                color="dark"
                dark
                flat
              >
                <v-toolbar-title color >Login</v-toolbar-title>
                <v-spacer></v-spacer>
              </v-toolbar>
              <v-card-text>
                <v-alert
                  dense
                  outlined
                  type="error"
                  v-if="isInvalid"
                >
                  Invalid Credentials
                </v-alert>
                <v-form class="mr-5 ml-5">
                  <v-text-field
                    label="Username"
                    name="username"
                    type="text"
                    v-model="username"
                    prepend-icon="mdi-account"
                    required
                    :error-messages="usernameErrors"
                    @change="$v.username.$touch()"
                    @blur="$v.username.$touch()"
                    @keyup="clear()"
                  ></v-text-field>

                  <v-text-field
                    id="password"
                    label="Password"
                    name="password"
                    type="password"
                    v-model="password"
                    prepend-icon="mdi-key"
                    required
                    :error-messages="passwordErrors"
                    @change="$v.password.$touch()"
                    @blur="$v.password.$touch()"
                    @keyup="clear()"
                  ></v-text-field>
                </v-form>
              </v-card-text>
              <v-card-actions class="mr-5 ml-5">
                <v-btn color="primary" class="mb-5 mr-5" block @click="login">Login</v-btn>
              </v-card-actions>
            </v-card>
          </v-flex>
        </v-layout>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>

  import Axios from 'axios';
  import { validationMixin } from "vuelidate";
  import { required, maxLength, email } from "vuelidate/lib/validators";

  export default {
    name: 'login',
    props: ['app'],
    mixins: [validationMixin],

    validations: {
      username: { required },
      password: { required },
    },
    data () {
      return {
        username: "",
        password: "",
        user: null,
        isInvalid: false,
      }
    },
    computed: {

      usernameErrors() {
        const errors = [];
        if (!this.$v.username.$dirty) return errors;
        // !this.$v.lastname.maxLength &&
          // errors.push("Name must be at most 10 characters long");
        !this.$v.username.required && errors.push("Username is required.");
        return errors;
      },

      passwordErrors() {
        const errors = [];
        if (!this.$v.password.$dirty) return errors;
        !this.$v.password.required && errors.push("Password is required.");
        return errors;
      },

    },
    methods: {

      login() {
         this.$v.$touch();

        if(!this.$v.$error)
        {

          const username = this.username;
          const password = this.password;
          const data = {username: username, password: password};

          Axios.post('/api/auth/login', data).then( (response) => {
            // console.log(response.data);

            if(response.data.access_token)
            { 

              // console.log(localStorage.getItem('access_token'));

              localStorage.setItem('access_token', response.data.access_token);
 
              this.$router.push('/').catch(e => {});
              this.clear();
              this.username = null;
              this.password = null;
            }
            else
            {
              this.isInvalid = true;
            }
            
          }, (error) => {
            console.log();
          });

        }
      },
      
      clear() {
        this.$v.$reset();
        this.isInvalid = false;
      }

    },

    mounted() {

    }
  }
</script>