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
            Transactions
            <v-spacer></v-spacer>
          </v-card-title>
          <v-row>
            <v-col cols="4" class="ml-10">
              <v-autocomplete
                name="service"
                v-model="service"
                :items="services"
                item-value="id"
                item-text="service"
                label="Filter by Service"
                @change="filterTransactions()"
              ></v-autocomplete>
            </v-col>
            <v-col cols="3" class="ml-10">
              <v-menu
                v-model="input_date_from"
                :close-on-content-click="false"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="290px"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    name="datefrom"
                    v-model="computedDateFromFormatted"
                    label="Filter Date From"
                    hint="MM/DD/YYYY format"
                    persistent-hint
                    prepend-icon="mdi-calendar"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                  v-model="date_from"
                  no-title
                  @input="input_date_from = false"
                  @change="filterTransactions()"
                ></v-date-picker>
              </v-menu>
            </v-col>
            <v-col cols="3" class="ml-10">
              <v-menu
                v-model="input_date_to"
                :close-on-content-click="false"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="290px"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    name="dateto"
                    v-model="computedDateToFormatted"
                    label="Filter Date To"
                    hint="MM/DD/YYYY format"
                    persistent-hint
                    prepend-icon="mdi-calendar"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                  v-model="date_to"
                  no-title
                  @input="input_date_to = false"
                  @change="filterTransactions()"
                ></v-date-picker>
              </v-menu>
            </v-col>
          </v-row>
          <v-row>
            <v-col>
              <v-simple-table id="transactionsTable">
                <template v-slot:default>
                  <thead>
                    <tr>
                      <th class="text-left">Document Date</th>
                      <th class="text-left">Patient/Organization</th>
                      <th class="text-left">Service</th>
                      <th class="text-left">Code Name</th>
                      <th class="text-left">Procedure</th>
                      <th class="text-left">Amount (PHP)</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in transactions">
                      <td>{{ item.docdate }}</td>
                      <td>{{ item.name }}</td>
                      <td>{{ item.service }}</td>
                      <td>{{ item.code }}</td>
                      <td>{{ item.procedure }}</td>
                      <td><strong>₱</strong> {{ item.total_amount }}</td>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td class="text-right" colspan="5">
                        <strong>Grand Total:</strong>
                      </td>
                      <td><strong>₱</strong> {{ computedGrandTotal }}</td>
                    </tr>
                  </tfoot>
                </template>
              </v-simple-table>
            </v-col>
          </v-row>
        </v-card>
      </v-main>
    </div>
  </div>
</template>
<script>

  let access_token;

  import Axios from "axios";

  export default {
    data() {
      return {
        date_from: new Date().toISOString().substr(0, 10),
        date_to: new Date().toISOString().substr(0, 10),
        search: "",
        headers: [
          { text: "Document Date", value: "docdate" },
          { text: "Patient/Organization", value: "name" },
          { text: "Service", value: "service" },
          { text: "Code Name", value: "code" },
          { text: "Procedure", value: "procedure" },
          { text: "Amout (PHP)", value: "total_amount" },
        ],
        transactions: [],
        items: [
          {
            text: "Home",
            disabled: false,
            link: "/dashboard",
          },
          {
            text: "Transactions",
            disabled: true,
          },
        ],
        service: "",
        services: [],
        input_date_from: false,
        input_date_to: false,
        filterDateFrom: "",
        filterDateTo: "",
      };
    },
    computed: {
      computedGrandTotal() {
        let grand_total = 0;

        return this.transactions.reduce(function (sum, item) {
          if (item.total_amount) {
            grand_total = parseFloat(grand_total) + parseFloat(item.total_amount);
          }

          return parseFloat(grand_total).toFixed(2);
        }, "0.00");
      },

      computedDateFromFormatted() {
        this.filterDateFrom = this.formatDateFrom(this.date_from);
        return this.filterDateFrom;
      },

      computedDateToFormatted() {
        this.filterDateTo = this.formatDateTo(this.date_to);
        return this.filterDateTo;
      },
    },

    watch: {
      dateFrom(val) {
        this.dateFormatted = this.formatDate(this.date_from);
      },
      dateTo(val) {
        this.dateFormatted = this.formatDate(this.date_to);
      },
    },

    methods: {

      getTransactions() {
        Axios.get("/api/transactions", {
          headers: {
            Authorization: "Bearer " + access_token,
          },
        }).then((response) => {
          this.transactions = response.data.transactions;
        });
      },

      filterTransactions() {

        const data = {service: this.service, date_from: this.date_from, date_to: this.date_to};

        Axios.post("/api/gettransactions", data, {
          headers: {
            Authorization: "Bearer " + access_token,
          },
        }).then((response) => {
          // console.log(response.data.transactions);
          this.transactions = response.data.transactions;
        });
      },

      getService() {
        Axios.get("/api/service/index", {
          headers: {
            Authorization: "Bearer " + access_token,
          },
        }).then((response) => {
          this.services = response.data.services;
          this.services.push({id: "", service: "All Services"});
        });
      },

      formatDateFrom(date) {

        if (!date) return null;

        const [year, month, day] = date.split("-");
        return `${month}/${day}/${year}`;
      },

      formatDateTo(date) {

        if (!date) return null;

        const [year, month, day] = date.split("-");
        return `${month}/${day}/${year}`;
      },

      parseDate(date) {
        if (!date) return null;

        const [month, day, year] = date.split("/");
        return `${year}-${month.padStart(2, "0")}-${day.padStart(2, "0")}`;
      },
    },

    mounted() {
      access_token = localStorage.getItem("access_token");
      this.filterTransactions();
      this.getService();
    },
  };
</script>