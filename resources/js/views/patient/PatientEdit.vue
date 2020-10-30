<template>
  <div class="flex column">
    <div id="_wrapper" class="pa-5">
      <v-main>
        <v-card>
          <v-card-title class="grey darken-2  text-white">
            Update Patient
          </v-card-title>
          <v-card-text class="pa-10">
            <form id="patientform">
              <v-row>
                <v-col>
                  <v-text-field
                    name="lastname"
                    v-model="lastname"
                    :error-messages="lastnameErrors"
                    label="Lastname"
                    required
                    @input="$v.lastname.$touch()"
                    @blur="$v.lastname.$touch()"
                  ></v-text-field>
                </v-col>
                <v-col>
                  <v-text-field
                    name="firstname"
                    v-model="firstname"
                    :error-messages="firstnameErrors"
                    label="Firstname"
                    required
                    @input="$v.firstname.$touch()"
                    @blur="$v.firstname.$touch()"
                  ></v-text-field>
                </v-col>
                <v-col>
                  <v-text-field
                    name="middlename"
                    v-model="middlename"
                    label="Middlename"
                  ></v-text-field>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="4">
                  <v-radio-group name="gender" v-model="gender">
                    <template v-slot:label>
                      <div><strong>Gender</strong></div>
                    </template>
                    <v-radio value="male">
                      <template v-slot:label>
                        <div>Male</div>
                      </template>
                    </v-radio>
                    <v-radio value="female">
                      <template v-slot:label>
                        <div>Female</div>
                      </template>
                    </v-radio>
                  </v-radio-group>
                </v-col>
                <v-col cols="4">
                  <v-radio-group name="civilstatus" v-model="civilstatus">
                    <template v-slot:label>
                      <div><strong>Civil Status</strong></div>
                    </template>
                    <v-radio value="single">
                      <template v-slot:label>
                        <div>Single</div>
                      </template>
                    </v-radio>
                    <v-radio value="married">
                      <template v-slot:label>
                        <div>Married</div>
                      </template>
                    </v-radio>
                    <v-radio value="widowed">
                      <template v-slot:label>
                        <div>Widowed</div>
                      </template>
                    </v-radio>
                  </v-radio-group>
                </v-col>
                <v-col
                cols="4"
                >
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
                        name="birthdate"
                        v-model="computedDateFormatted"
                        label="Birthdate"
                        hint="MM/DD/YYYY format"
                        persistent-hint
                        prepend-icon="mdi-calendar"
                        required
                        readonly
                        v-bind="attrs"
                        v-on="on"
                        :error-messages="birthdateErrors"
                        @input="$v.birthdate.$touch()"
                        @blur="$v.birthdate.$touch()"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="birthdate"
                      no-title
                      @input="input = false"
                    ></v-date-picker>
                  </v-menu>
                </v-col>
              </v-row>
              <v-row>
                <v-col>
                  <v-text-field
                    name="landline"
                    v-model="landline"
                    label="Landline"
                    prepend-icon="mdi-phone"
                  ></v-text-field>
                </v-col>
                <v-col>
                  <v-text-field
                    name="mobile"
                    v-model="mobile"
                    label="Mobile"
                    prepend-icon="mdi-cellphone"
                  ></v-text-field>
                </v-col>
                <v-col>
                  <v-text-field
                    name="email"
                    v-model="email"
                    prepend-icon="mdi-email"
                    :error-messages="emailErrors"
                    label="E-mail"
                    @input="$v.email.$touch()"
                    @blur="$v.email.$touch()"
                  ></v-text-field>
                </v-col>
              </v-row>
              <v-row>
                <v-col>
                  <v-text-field
                    name="address"
                    v-model="address"
                    label="Address"
                  ></v-text-field>
                </v-col>
              </v-row>
              <v-row class="mb-6">
                <v-col>
                  <v-autocomplete
                    name="province"
                    v-model="province"
                    :items="provinces"
                    item-value="province_id"
                    item-text="name"
                    label="Province"
                    required
                    :error-messages="provinceErrors"
                    @change="$v.province.$touch()"
                    @blur="$v.province.$touch()"
                    v-on:change="getCities($v.province.$model)"
                  >
                  </v-autocomplete>
                  
                </v-col>
                <v-col>
                  <v-autocomplete
                    name="city"
                    v-model="city"
                    :items="cities"
                    item-value="city_id"
                    item-text="name"
                    label="City/Municipality"
                    required
                    :error-messages="cityErrors"
                    @change="$v.city.$touch()"
                    @blur="$v.city.$touch()"
                    v-on:change="getBarangays($v.city.$model)"
                  ></v-autocomplete>
                </v-col>
                <v-col>
                  <v-autocomplete
                    name="barangay"
                    v-model="barangay"
                    :items="barangays"
                    item-value="id"
                    item-text="name"
                    label="Barangay"
                    required
                    :error-messages="barangayErrors"
                    @change="$v.barangay.$touch()"
                    @blur="$v.barangay.$touch()"
                    
                  ></v-autocomplete>
                </v-col>
              </v-row>
              <v-btn class="mr-4" color="primary" @click="createPatient" :disabled="disabled"> submit </v-btn>
              <v-btn color="#E0E0E0" @click="clear"> clear </v-btn>
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
import { required, maxLength, email } from "vuelidate/lib/validators";

export default {
  mixins: [validationMixin],

  validations: {
    lastname: { required },
    firstname: { required },
    birthdate: { required },
    email: { email },
    province: { required },
    city: { required },
    barangay: { required },
    
  },

  data: () => ({
    lastname: "",
    firstname: "",
    middlename: "",
    gender: "male",
    civilstatus: "single",
    birthdate: "",
    input: false,
    landline: "",
    mobile: "",
    email: "",
    address: "",
    province: null,
    city: null,
    barangay: null,
    provinces: [],
    cities: [],  
    barangays: [],
    checkbox: false,
    disabled: false,
  }),

  computed: {
    checkboxErrors() {
      const errors = [];
      if (!this.$v.checkbox.$dirty) return errors;
      !this.$v.checkbox.checked && errors.push("You must agree to continue!");
      return errors;
    },
    lastnameErrors() {
      const errors = [];
      if (!this.$v.lastname.$dirty) return errors;
      // !this.$v.lastname.maxLength &&
        // errors.push("Name must be at most 10 characters long");
      !this.$v.lastname.required && errors.push("Lastname is required.");
      return errors;
    },
    firstnameErrors() {
      const errors = [];
      if (!this.$v.firstname.$dirty) return errors;
      !this.$v.firstname.required && errors.push("Firstname is required.");
      return errors;
    },
    birthdateErrors() {
      const errors = [];
      if (!this.$v.birthdate.$dirty) return errors;
      !this.$v.birthdate.required && errors.push("Birthdate is required");
      // !this.$v.email.required && errors.push("E-mail is required");
      return errors;
    },
    computedDateFormatted () {
      return this.formatDate(this.birthdate)
    },
    emailErrors() {
      const errors = [];
      if (!this.$v.email.$dirty) return errors;
      !this.$v.email.email && errors.push("Must be valid e-mail");
      // !this.$v.email.required && errors.push("E-mail is required");
      return errors;
    },
    provinceErrors() {
      const errors = [];
      if (!this.$v.province.$dirty) return errors;
      !this.$v.province.required && errors.push("Province is required");
      return errors;
    },
    cityErrors() {
      const errors = [];
      if (!this.$v.city.$dirty) return errors;
      !this.$v.city.required && errors.push("City is required");
      return errors;
    },
    barangayErrors() {
      const errors = [];
      if (!this.$v.barangay.$dirty) return errors;
      !this.$v.barangay.required && errors.push("Barangay is required");
      return errors;
    },
  },
  watch: {
    date (val) {
      this.dateFormatted = this.formatDate(this.birthdate)
    },
  },
  methods: {
    createPatient() {
      this.$v.$touch();

      if(!this.$v.$error)
      {
        
        this.disabled = true;

        const patientid = this.$route.params.patientid;

        let myForm = document.getElementById('patientform');
        let formData = new FormData(myForm);
        const data = {};

        for(let [key, val] of formData.entries())
        {
          Object.assign(data ,{[key]: val});
        }

        Axios.post('/patient/update/'+patientid, data).then( (response) => {
          console.log(response.data);

          if(response.data.success)
          {
            this.disabled = false;
            this.showAlert(); 
          }


        }, (error) => {
          console.log(error);
        });
      }
      
    },
    clear() {
      this.$v.$reset();
      this.lastname = "";
      this.firstname=  "";
      this.middlename = "";
      this.gender = "male";
      this.civilstatus = "single";
      this.birthdate = "";
      this.landline = "";
      this.mobile = "";
      this.email = "";
      this.address = "";
      this.province = null;
      this.city = null;
      this.barangay = null;
      this.provinces = [];
      this.cities = []; 
      this.barangays = [];

    },
    formatDate (birthdate) {
      if (!birthdate) return null

      const [year, month, day] = birthdate.split('-')
      return `${month}/${day}/${year}`
    },
    parseDate (birthdate) {
      if (!birthdate) return null

      const [month, day, year] = birthdate.split('/')
      return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`
    },
    getProvinces(){
      Axios.get('/provinces').then((response) => {
        this.provinces = response.data.provinces;
        console.log(this.provinces);
      });
    },
    getCities(province_id) {
      Axios.get('/cities/'+province_id).then((response) => {
        this.cities = response.data.cities;
        this.barangays = [];
        this.city = null;
        this.barangay = null;
        this.$v.city.$reset();
        this.$v.barangay.$reset();
        console.log(this.cities);
      });
    },
    getBarangays(city_id) {
      Axios.get('/barangays/'+city_id).then((response) => {
        this.barangays = response.data.barangays;
        console.log(this.barangays);
      });
    },
    showAlert() {
       this.$swal({
        position: 'center',
        icon: 'success',
        title: 'Record has been updated',
        showConfirmButton: false,
        timer: 2500
      });
    },
    getPatient(){
      Axios.get(this.$route.path).then((response) => {  
        
        this.lastname = response.data.patient.lastname;
        this.firstname =  response.data.patient.firstname;
        this.middlename = response.data.patient.middlename;
        this.gender = response.data.patient.gender;
        this.civilstatus = response.data.patient.civilstatus;
        this.birthdate = response.data.birthdate;
        this.landline = response.data.patient.landline;
        this.mobile = response.data.patient.mobile;
        this.email = response.data.patient.email;
        this.address = response.data.patient.address;
        this.province = response.data.patient.province;
        this.city = response.data.patient.city;
        this.barangay = Number(response.data.patient.barangay);

        const province_id = response.data.patient.province;
        const city_id = response.data.patient.city;

        //Get Provinces
        this.getProvinces();

        //Get Cities
        Axios.get('/cities/'+province_id).then((response) => {
            this.cities = response.data.cities;
          });

        //Get Barangays
        Axios.get('/barangays/'+city_id).then((response) => {
            this.barangays = response.data.barangays;
          });

        
      });
    }
  },
  mounted () {
    this.getPatient();
  }
};
</script>