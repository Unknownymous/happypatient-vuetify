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
            Update User
          </v-card-title>
          <v-card-text class="pa-10">
            <form id="userform">
              <v-row>
                <v-col cols="4">
                  <v-text-field
                    name="name"
                    v-model="name"
                    :error-messages="nameErrors"
                    label="Full Name"
                    required
                    @input="$v.name.$touch()"
                    @blur="$v.name.$touch()"
                  ></v-text-field>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="4">
                  <v-textarea
                    name="description"
                    v-model="description"
                    label="Description"
                    rows="2"
                  ></v-textarea>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="4">
                  <v-text-field
                    name="license"
                    v-model="license"
                    label="License"
                  ></v-text-field>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="4">
                  <v-text-field
                    name="email"
                    v-model="email"
                    :error-messages="emailErrors"
                    label="E-mail"
                    @input="$v.email.$touch()"
                    @blur="$v.email.$touch()"
                  ></v-text-field>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="4">
                  <v-text-field
                    name="username"
                    v-model="username"
                    :error-messages="usernameErrors"
                    label="Username"
                    disabled
                    required
                    @input="$v.username.$touch()"
                    @blur="$v.username.$touch()"
                  ></v-text-field>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="4">
                  <v-text-field
                    name="password"
                    v-model="password"
                    :error-messages="passwordErrors"
                    label="Password"
                    @input="$v.password.$touch()"
                    @blur="$v.password.$touch()"
                    type="password"
                  ></v-text-field>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="4">
                  <v-text-field
                    name="confirm_password"
                    v-model="confirm_password"
                    :error-messages="confirm_passwordErrors"
                    label="Confirm Password"
                    @input="$v.confirm_password.$touch()"
                    type="password"
                  ></v-text-field>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="4">
                  <v-combobox
                    name="role"
                    v-model="role"
                    :items="roles"
                    :item-text="roles.text"
                    :item-value="roles.value"
                    label="Roles"
                    multiple
                    chips
                  ></v-combobox>
                </v-col>
              </v-row>
              <v-btn class="mr-4" color="primary" @click="updateUser" :disabled="disabled"> update </v-btn>
              <v-btn color="#E0E0E0" :to="{name: 'user.index'}"> cancel </v-btn>
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
    name: { required },
    username: { required },
    password: { minLength: minLength(8) },
    confirm_password: { sameAsPassword: sameAs('password') },
    email: { email },    
  },

  data: () => ({
    name: "",
    description: "",
    license: "",
    email: "",
    username: "",
    password: "",
    confirm_password: "",
    role: "",
    roles: ['Role 1', 'Role 2', 'Role 3', 'Role 4'],
    disabled: false,
    items: [
        { 
          text: 'Home', 
          disabled: false, 
          link: '/dashboard',
        },
        { 
          text: 'Users Record', 
          disabled: false, 
          link: '/user/index' ,
        },
        { 
          text: 'Update User', 
          disabled: true,
        }
      ]
  }),

  computed: {
    nameErrors() {
      const errors = [];
      if (!this.$v.name.$dirty) return errors;
      !this.$v.name.required && errors.push("Full Name is required.");
      return errors;
    },
    
     usernameErrors() {
      const errors = [];
      if (!this.$v.username.$dirty) return errors;
      !this.$v.username.required && errors.push("Username is required.");
      return errors;
    },

    passwordErrors() {
      const errors = [];
      if (!this.$v.password.$dirty) return errors;
      // !this.$v.password.required && errors.push("Password is required.");
      !this.$v.password.minLength && errors.push("Password must be atleast 8 characters.");
      return errors;
    },

    confirm_passwordErrors() {
      const errors = [];
      if (!this.$v.confirm_password.$dirty) return errors;
      // !this.$v.confirm_password.required && errors.push("Password Confirmation is required.");
      !this.$v.confirm_password.sameAsPassword && errors.push("Passwords did not match");
      return errors;
    },
    
    emailErrors() {
      const errors = [];
      if (!this.$v.email.$dirty) return errors;
      !this.$v.email.email && errors.push("Must be valid e-mail");
      return errors;
    },
  },
  methods: {
    updateUser() {
      
      this.$v.$touch();

      if(!this.$v.$error)
      {
        
        this.disabled = true;

        const userid = this.$route.params.userid;

        let myForm = document.getElementById('userform');
        let formData = new FormData(myForm);
        const data = {};

        for(let [key, val] of formData.entries())
        {
          Object.assign(data ,{[key]: val});
        }

        Axios.post('/user/update/'+userid, data).then((response) => {
          console.log(response.data);

          if(response.data.success)
          {
            this.showAlert(); 
          }

          this.disabled = false;

        }, (error) => {
          console.log(error);
        });

      }
      
    },
    clear() {
      this.$v.$reset();
      this.name = "";
      this.description=  "";
      this.license = "";
      this.email = "";
      this.username = "";
      this.password = "";
      this.confirm_password = "";
      this.role = "";
      this.roles = ['Role 1', 'Role 2', 'Role 3', 'Role 4'];
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
    getUser() {
      Axios.get(this.$route.path).then( (response) => {

        this.name = response.data.user.name;
        this.description = response.data.user.description;
        this.license = response.data.user.license;
        this.email = response.data.user.email;
        this.username = response.data.user.username;
        this.role = response.data.user.role;
        
      }, (error) => { 
        console.log(error);
      })
    }
  },
  mounted () {
    this.getUser();
  }
};
</script>