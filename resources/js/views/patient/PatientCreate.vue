<template>
  <div class="flex column">
    <div id="_wrapper" class="pa-5">
      <v-main>
        <v-card>
          <v-card-title>
            Create Patient
          </v-card-title>
          <v-card-text>
            <form>
              <v-row>
                <v-col>
                  <v-text-field
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
                    v-model="middlename"
                    label="Middlename"
                  ></v-text-field>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="2">
                  <v-radio-group v-model="gender">
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
                <v-col>
                  <v-radio-group v-model="civilstatus">
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
              </v-row>
              <v-text-field
                v-model="email"
                :error-messages="emailErrors"
                label="E-mail"
                @input="$v.email.$touch()"
                @blur="$v.email.$touch()"
              ></v-text-field>
              <v-select
                v-model="select"
                :items="items"
                :error-messages="selectErrors"
                label="Item"
                required
                @change="$v.select.$touch()"
                @blur="$v.select.$touch()"
              ></v-select>
              <v-btn class="mr-4" color="primary" @click="submit"> submit </v-btn>
              <v-btn color="#E0E0E0" @click="clear"> clear </v-btn>
            </form>
          </v-card-text>
        </v-card>
      </v-main>
    </div>
  </div>
</template>

<script>
import { validationMixin } from "vuelidate";
import { required, maxLength, email } from "vuelidate/lib/validators";

export default {
  mixins: [validationMixin],

  validations: {
    lastname: { required },
    firstname: { required },
    email: { email },
    select: { required },
    checkbox: {
      checked(val) {
        return val;
      },
    },
  },

  data: () => ({
    lastname: "",
    firstname: "",
    middlename: "",
    gender: "male",
    civilstatus: "single",
    email: "",
    select: null,
    items: ["Item 1", "Item 2", "Item 3", "Item 4"],
    checkbox: false,
  }),

  computed: {
    checkboxErrors() {
      const errors = [];
      if (!this.$v.checkbox.$dirty) return errors;
      !this.$v.checkbox.checked && errors.push("You must agree to continue!");
      return errors;
    },
    selectErrors() {
      const errors = [];
      if (!this.$v.select.$dirty) return errors;
      !this.$v.select.required && errors.push("Item is required");
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
    emailErrors() {
      const errors = [];
      if (!this.$v.email.$dirty) return errors;
      !this.$v.email.email && errors.push("Must be valid e-mail");
      // !this.$v.email.required && errors.push("E-mail is required");
      return errors;
    },
  },

  methods: {
    submit() {
      this.$v.$touch();
    },
    clear() {
      this.$v.$reset();
      this.name = "";
      this.email = "";
      this.select = null;
      this.checkbox = false;
    },
  },
};
</script>