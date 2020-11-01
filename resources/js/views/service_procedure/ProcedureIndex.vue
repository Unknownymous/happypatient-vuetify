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
          <v-card-title>
            Service Procedures Record
            <v-spacer></v-spacer>
            <v-text-field
              v-model="search"
              append-icon="mdi-magnify"
              label="Search"
              single-line
              hide-details
            ></v-text-field>
            <template>
              <v-toolbar flat>
                <v-spacer></v-spacer>
                <v-dialog v-model="dialog" max-width="500px">
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      color="primary"
                      fab
                      dark
                      class="mb-2"
                      v-bind="attrs"
                      v-on="on"
                      :to="{ name: 'procedure.create' }"
                    >
                      <v-icon>mdi-plus</v-icon>
                    </v-btn>
                  </template>
                  <v-card>
                    <v-card-title>
                      <span class="headline">Update Service Procedure</span>
                    </v-card-title>

                    <v-card-text>
                      <v-container>
                        <v-row>
                          <v-col>
                            <v-autocomplete
                              name="service"
                              v-model="editedItem.service"
                              :items="services"
                              item-value="id"
                              item-text="service"
                              label="Service"
                              required
                              :error-messages="serviceErrors"
                              @change="$v.editedItem.service.$touch()"
                              @blur="$v.editedItem.service.$touch()"
                            ></v-autocomplete>
                          </v-col>
                        </v-row>
                        <v-row>
                          <v-col>
                            <v-text-field
                              name="procedure"
                              v-model="editedItem.procedure"
                              label="Procedure"
                              required
                              :error-messages="procedureErrors"
                              @input="$v.editedItem.procedure.$touch()"
                              @blur="$v.editedItem.procedure.$touch()"
                            ></v-text-field>
                          </v-col>
                        </v-row>
                        <v-row>
                          <v-col>
                            <v-text-field
                              name="price"
                              v-model="editedItem.price"
                              label="Price"
                              required
                              :error-messages="priceErrors"
                              @input="$v.editedItem.price.$touch()"
                              @blur="$v.editedItem.price.$touch()"
                            ></v-text-field>
                          </v-col>
                        </v-row>
                      </v-container>
                    </v-card-text>

                    <v-card-actions>
                      <v-spacer></v-spacer>
                      <v-btn color="#E0E0E0" @click="close" class="mb-4">
                        Cancel
                      </v-btn>
                      <v-btn
                        color="primary"
                        @click="save"
                        class="mb-4 mr-4"
                        :disabled="disabled"
                      >
                        Save
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-dialog>
              </v-toolbar>
            </template>
          </v-card-title>
          <v-data-table :headers="headers" :items="procedures" :search="search">
            <template v-slot:item.actions="{ item }">
              <v-icon
                small
                class="mr-2"
                color="green"
                @click="editProcedure(item)"
              >
                mdi-pencil
              </v-icon>
              <v-icon small color="red" @click="showConfirmAlert(item)">
                mdi-delete
              </v-icon>
            </template>
          </v-data-table>
        </v-card>
      </v-main>
    </div>
  </div>
</template>
<script>
import Axios from "axios";
import { validationMixin } from "vuelidate";
import { required, maxLength, email } from "vuelidate/lib/validators";

export default {
  mixins: [validationMixin],

  validations: {
    editedItem: {
      service: { required },
      procedure: { required },
      price: { required },
    },
  },
  data() {
    return {
      search: "",
      headers: [
        { text: "Service", value: "service" },
        { text: "Procedure", value: "procedure" },
        { text: "Price", value: "price" },
        { text: "Actions", value: "actions", sortable: false },
      ],
      disabled: false,
      dialog: false,
      services: [],
      procedures: [],
      editedIndex: -1,
      editedItem: {
        service: "",
        procedure: "",
        price: "",
      },
      defaultItem: {
        service: "",
        procedure: "",
        price: "",
      },
      items: [
        {
          text: "Home",
          disabled: false,
          link: "/dashboard",
        },
        {
          text: "Service Procedures Record",
          disabled: true,
        },
      ],
    };
  },

  methods: {
    getServiceProcedures() {
      Axios.get("/procedure/index").then((response) => {
        console.log(response.data.procedures);
        this.procedures = response.data.procedures;
      });
    },

    getService() {
      Axios.get("/service/index").then((response) => {
        console.log(response.data.services);
        this.services = response.data.services;
      });
    },

    editService(item) {
      this.editedIndex = this.procedures.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },

    deleteService(procedureid) {
      const data = { procedureid: procedureid };

      Axios.post("/procedure/delete", data).then(
        (response) => {
          console.log(response.data);
        },
        (error) => {
          console.log(error);
        }
      );
    },

    showAlert() {
      this.$swal({
        position: "center",
        icon: "success",
        title: "Record has been saved",
        showConfirmButton: false,
        timer: 2500,
      });
    },

    showConfirmAlert(item) {
      this.$swal({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Delete record!",
      }).then((result) => {
        // <--

        if (result.value) {
          // <-- if confirmed

          const serviceid = item.id;
          const index = this.services.indexOf(item);

          //Call delete Patient function
          this.deleteService(serviceid);

          //Remove item from array services
          this.services.splice(index, 1);

          this.$swal({
            position: "center",
            icon: "success",
            title: "Record has been deleted",
            showConfirmButton: false,
            timer: 2500,
          });
        }
      });
    },
    close() {
      this.dialog = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },
    save() {

      this.$v.$touch();

      if (!this.$v.$error) {
        this.disabled = true;

        Object.assign(this.procedures[this.editedIndex], this.editedItem);

        const data = this.editedItem;
        const procedureid = this.editedItem.id;

        Axios.post("/procedure/update/" + procedureid, data).then(
          (response) => {
            console.log(response.data);

            if (response.data.success) {
              this.clear();
              this.showAlert();
            }

            this.disabled = false;
          },
          (error) => {
            console.log(error);
          }
        );
      }
      this.close();
    },
  },
  computed: {
    serviceErrors() {
      const errors = [];
      if (!this.$v.editedItem.service.$dirty) return errors;
      !this.$v.editedItem.service.required &&
        errors.push("Service is required.");
      return errors;
    },
    procedureErrors() {
      const errors = [];
      if (!this.$v.editedItem.procedure.$dirty) return errors;
      !this.$v.editedItem.procedure.required &&
        errors.push("Procedure is required.");
      return errors;
    },
    priceErrors() {
      const errors = [];
      if (!this.$v.editedItem.price.$dirty) return errors;
      !this.$v.editedItem.price.required &&
        errors.push("Price is required.");
      return errors;
    },
  },
  mounted() {
    this.getServiceProcedures();
  },
};
</script>