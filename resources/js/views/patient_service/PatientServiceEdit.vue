<template>
  <div class="flex column">
    <div id="_wrapper" class="pa-5">
      <v-main>
        <v-breadcrumbs :items="items">
          <template v-slot:item="{ item }">
            <v-breadcrumbs-item :to="item.link" :disabled="item.disabled">
              {{ item.text.toUpperCase() }}
            </v-breadcrumbs-item>
          </template>
        </v-breadcrumbs>
        <v-card>
          <v-card-title class="grey darken-2 text-white">
            Edit Patient Services
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
                      {{
                        data.item.id +
                        " - " +
                        data.item.lastname +
                        ", " +
                        data.item.firstname
                      }}&nbsp;<span v-if="data.item.middlename">{{
                        data.item.middlename
                      }}</span>
                    </template>
                    <template slot="item" slot-scope="data">
                      {{
                        data.item.id +
                        " - " +
                        data.item.lastname +
                        ", " +
                        data.item.firstname
                      }}&nbsp;<span v-if="data.item.middlename">{{
                        data.item.middlename
                      }}</span>
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
                    <v-card-title> Service Procedures </v-card-title>
                    <v-simple-table id="procedureTable">
                      <template v-slot:default>
                        <thead>
                          <tr>
                            <th class="text-left">Services</th>
                            <th class="text-left">Procedures</th>
                            <th class="text-left" width="150px">Price (PHP)</th>
                            <th class="text-left" width="150px">
                              Discount (%)
                            </th>
                            <th class="text-left" width="150px">
                              Discount (PHP)
                            </th>
                            <th class="text-left" width="150px">
                              Total Amount (PHP)
                            </th>
                            <th width="200px"></th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr
                            v-for="item in patient_service_items"
                            :key="item.id"
                          >
                            <td>
                              {{ item.service }}
                            </td>
                            <td>
                              {{ item.procedure }}
                            </td>
                            <td>
                              <v-text-field-money
                                class="mt-2"
                                v-model="editedItem.price"
                                v-bind:properties="{
                                  name: 'price',
                                  prefix: '₱',
                                  placeholder: '0.00',
                                  disabled: priceDisabled,
                                  keyup: computeTotalAmount(),
                                }"
                                v-bind:options="{
                                  length: 11,
                                  precision: 2,
                                  empty: null,
                                }"
                                v-if="patient_service_items.indexOf(item) == editedIndex"
                              >
                              </v-text-field-money>

                              <span v-if="patient_service_items.indexOf(item) != editedIndex">
                                <strong v-if="item.price"> ₱ </strong>

                                {{ item.price }}
                              </span>
                            </td>
                            <td>
                              <v-text-field-money
                                class="mt-2"
                                v-model="editedItem.discount"
                                v-bind:properties="{
                                  name: 'discount',
                                  suffix: '%',
                                  placeholder: '0.00',
                                  disabled: discountDisabled,
                                  keyup: computeTotalAmount(),
                                }"
                                v-bind:options="{
                                  length: 4,
                                  precision: 2,
                                  empty: null,
                                }"
                                :disabled="discountDisabled"
                                v-if="patient_service_items.indexOf(item) == editedIndex"
                              >
                              </v-text-field-money>
                              <span v-if="patient_service_items.indexOf(item) != editedIndex">
                                {{ item.discount }}

                                <strong v-if="item.discount"> % </strong>
                              </span>
                            </td>
                            <td>
                              <v-text-field-money
                                class="mt-2"
                                v-model="editedItem.discount_amt"
                                v-bind:properties="{
                                  name: 'discount_amt',
                                  prefix: '₱',
                                  placeholder: '0.00',
                                  disabled: discount_amtDisabled,
                                  keyup: computeTotalAmount(),
                                }"
                                v-bind:options="{
                                  length: 11,
                                  precision: 2,
                                  empty: null,
                                }"
                                v-if="patient_service_items.indexOf(item) == editedIndex"
                              >
                              </v-text-field-money>

                              <span v-if="patient_service_items.indexOf(item) != editedIndex">
                                <strong v-if="item.discount_amt"> ₱ </strong>

                                {{ item.discount_amt }}
                              </span>
                            </td>
                            <td>
                              <v-text-field-money
                                class="mt-2"
                                v-model="editedItem.total_amount"
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
                                v-if="patient_service_items.indexOf(item) == editedIndex"
                              >
                              </v-text-field-money>

                              <span v-if="patient_service_items.indexOf(item) != editedIndex">
                                <strong v-if="item.total_amount"> ₱ </strong>

                                {{ item.total_amount }}
                              </span>
                            </td>
                            <td>
                              <v-icon
                                small
                                class="mr-2"
                                color="green"
                                @click="editAmount(item)"
                                v-if="patient_service_items.indexOf(item) != editedIndex"
                              >
                                mdi-pencil
                              </v-icon>

                              <v-btn color="primary" 
                                small  
                                @click="updateAmount()" v-if="patient_service_items.indexOf(item) == editedIndex"
                                :disabled="btnSaveDisabled"
                              >
                                save

                              </v-btn>
                              <v-btn 
                                color="#E0E0E0" 
                                small  
                                @click="reset()" 
                                v-if="patient_service_items.indexOf(item) == editedIndex"
                              >
                                cancel
                              </v-btn>
                            </td>
                          </tr>
                        </tbody>
                        <tfoot>
                          <tr>
                            <td colspan="5">
                              <strong
                                ><span class="pull-right"
                                  >Grand Total :</span
                                ></strong
                              >
                            </td>
                            <td>
                              <strong
                                ><span class="service-grand-total"
                                  >₱
                                  {{
                                    (patientservicesData.grand_total = computedGrandTotal)
                                  }}</span
                                ></strong
                              >
                            </td>
                          </tr>
                        </tfoot>
                      </template>
                    </v-simple-table>
                  </v-card>
                </v-col>
              </v-row>
              <v-btn
                class="mr-4 mt-5"
                color="primary"
                @click="updatePatientService"
                :disabled="disabled"
              >
                update
              </v-btn>
              <v-btn
                class="mt-5"
                color="#E0E0E0"
                :to="{ name: 'patientservice.index' }"
              >
                cancel
              </v-btn>
            </form>
          </v-card-text>
        </v-card>
      </v-main>
    </div>
  </div>
</template>

<script>
let access_token;

import Axios from "axios";
import { validationMixin } from "vuelidate";
import {
  required,
  maxLength,
  email,
  minLength,
  sameAs,
} from "vuelidate/lib/validators";

export default {
  mixins: [validationMixin],

  validations: {
    patientservicesData: {
      patient: { required },
      organization: { required },
    },
  },

  data: (vm) => ({
    date: new Date().toISOString().substr(0, 10),
    dateFormatted: vm.formatDate(new Date().toISOString().substr(0, 10)),
    input: false,
    priceHasError: false,
    disabled: false,
    btnSaveDisabled: false,
    priceDisabled: false,
    discountDisabled: false,
    discount_amtDisabled: false,
    total_amtDisabled: false,
    patients: [],
    patient_service_items: [],
    items: [
      {
        text: "Home",
        disabled: false,
        link: "/dashboard",
      },
      {
        text: "Patient Services Record",
        disabled: false,
        link: "/patientservice/index",
      },
      {
        text: "Edit Patient Services",
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
      grand_total: "0.00",
    },
    editedIndex: -1,
    editedItem: {
      service: "",
      procedure: "",
      price: "",
      discount: "",
      discount_amt: "",
      total_amount: "",
    },
    defaultItem: {
      service: "",
      procedure: "",
      price: "",
      discount: "",
      discount_amt: "",
      total_amount: "",
    },
  }),

  computed: {
    patientErrors() {
      const errors = [];
      if (!this.$v.patientservicesData.patient.$dirty) return errors;
      !this.$v.patientservicesData.patient.required &&
        errors.push("Patient is required.");
      return errors;
    },

    organizationErrors() {
      const errors = [];
      if (!this.$v.patientservicesData.organization.$dirty) return errors;
      !this.$v.patientservicesData.organization.required &&
        errors.push("Organization is required.");
      return errors;
    },

    computedDateFormatted() {
      this.patientservicesData.docdate = this.formatDate(this.date);
      return this.patientservicesData.docdate;
    },

    computedGrandTotal() {
      let grand_total = 0;

      return this.patient_service_items.reduce(function (sum, item) {
        if (item.total_amount) {
          grand_total = parseFloat(grand_total) + parseFloat(item.total_amount);
        }

        return parseFloat(grand_total).toFixed(2);
      }, "0.00");
    },
  },
  watch: {
    date(val) {
      this.dateFormatted = this.formatDate(this.date);
    },
  },
  methods: {
    updatePatientService() {

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

      if(!nameHasError)
      { 

        const psid = this.$route.params.psid

        Axios.post('/api/patientservice/update/'+psid, this.patientservicesData, {
          headers: {
            Authorization: "Bearer " + access_token,
          },
        }).then((response) => {
          console.log(response.data)
          if(response.data.success)
          {
            this.showAlert();
          }

        });
      }

    },

    editAmount(item) {
      this.editedIndex = this.patient_service_items.indexOf(item);
      this.editedItem = Object.assign({}, item);
    },

    updateAmount() {

      if(!this.priceHasError)
      {
        Axios.post('/api/patientservice/update_price', this.editedItem, {
          headers: {
            Authorization: "Bearer " + access_token,
          },
        }).then((response) => {
          console.log(response.data);

          if(response.data.success)
          {
            this.showAlert2();
            Object.assign(this.patient_service_items[this.editedIndex], this.editedItem);
            this.reset();
          }

        });

      }
      
    },

    reset() {
      this.editedIndex = -1;
      this.editedItem = Object.assign({}, this.defaultItem);
      this.btnSaveDisabled = false;
    },

    getPatientServices() {
      Axios.get("/api" + this.$route.path, {
        headers: {
          Authorization: "Bearer " + access_token,
        },
      }).then((response) => {
        console.log(response.data);
        this.patientservicesData.type = response.data.patientservice.type;
        this.patientservicesData.patient = response.data.patientservice.patientid;
        this.patientservicesData.organization = response.data.patientservice.organization;
        this.patientservicesData.or_number =  response.data.patientservice.or_number;
        this.date = response.data.patientservice.docdate;
        this.patientservicesData.bloodpressure = response.data.patientservice.bloodpressure;
        this.patientservicesData.temperature = response.data.patientservice.temperature;
        this.patientservicesData.weight = response.data.patientservice.weight;
        this.patientservicesData.grand_total = response.data.patientservice.grand_total;
        this.patient_service_items = response.data.patientserviceitems;
      });
    },

    getPatient() {
      Axios.get("/api/patient/index", {
        headers: {
          Authorization: "Bearer " + access_token,
        },
      }).then((response) => {
        this.patients = response.data.patients;
      });
    },

    getService() {
      Axios.get("/api/service/index", {
        headers: {
          Authorization: "Bearer " + access_token,
        },
      }).then((response) => {
        this.services = response.data.services;
      });
    },

    getIndex(index) {
      this.index = index;
    },

    formatDate(date) {
      if (!date) return null;

      const [year, month, day] = date.split("-");
      return `${month}/${day}/${year}`;
    },

    parseDate(date) {
      if (!date) return null;

      const [month, day, year] = date.split("/");
      return `${year}-${month.padStart(2, "0")}-${day.padStart(2, "0")}`;
    },

    computeTotalAmount() {

      this.priceValidate();

      if (!this.editedItem.price) {
        this.editedItem.price = "0.00";
      }

      if (!this.editedItem.discount) {
        this.editedItem.discount = "0.00";
      }

      if (!this.editedItem.discount_amt) {
        this.editedItem.discount_amt = "0.00";
      }

      let discounted_price =
        parseFloat(this.editedItem.price) * (parseFloat(this.editedItem.discount) / 100);

      this.editedItem.total_amount = parseFloat(this.editedItem.price) - parseFloat(discounted_price) - parseFloat(this.editedItem.discount_amt);
    },

    priceValidate() {
        
      // const price = document.getElementById("input-73").value;

      if(parseFloat(this.editedItem.price) > 0)
      {
        this.btnSaveDisabled = false;
        this.priceHasError = false;
      }
      else
      { 
        this.btnSaveDisabled = true;
        this.priceHasError = true;
      }
    }, 

    showAlert() {
      this.$swal({
        position: "center",
        icon: "success",
        title: "Record has successfully updated",
        showConfirmButton: false,
        timer: 2500,
      });
    },

    showAlert2() {
      this.$swal({
        position: "center",
        icon: "success",
        title: "Amounts has successfully updated",
        showConfirmButton: false,
        timer: 2500,
      });
    },

  },

  mounted() {
    access_token = localStorage.getItem("access_token");
    this.getPatient();
    this.getPatientServices();
  },
};
</script>