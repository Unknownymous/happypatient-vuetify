<template>
  <v-app>
    <!-- Navbar -->
    <v-app-bar dense dark app>
      <v-btn icon @click.stop="mini = !mini">
        <v-app-bar-nav-icon></v-app-bar-nav-icon>
      </v-btn>

      <v-spacer></v-spacer>

      <v-btn @click="logout">
        <v-icon>mdi-logout </v-icon>
        Logout
      </v-btn>
    </v-app-bar>

    <!-- Sidebar -->
    <v-navigation-drawer v-model="drawer" :mini-variant.sync="mini" dark app>
      <v-list>
        <v-list-item class="px-2">
          <v-list-item-avatar class="rounded-0" height="60">
            <v-img
              src="/img/siteicon4.png"
            ></v-img>
          </v-list-item-avatar>
          <v-list-item-content>
            <v-list-item-title>Happy Patient</v-list-item-title>
            <v-list-item-subtitle>{{user}}</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </v-list>
      
      <v-divider></v-divider>

      <v-list>
        <v-list-item link :to="{ name: 'dashboard' }">
          <v-list-item-icon>
            <v-icon>mdi-view-dashboard</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Dashboard</v-list-item-title>
        </v-list-item>
        <v-list-item link :to="{ name: 'transactions' }">
          <v-list-item-icon>
            <v-icon>mdi-currency-usd</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Transactions</v-list-item-title>
        </v-list-item>
        <v-list-group
          v-for="item in items"
          :key="item.title"
          v-model="item.active"
          :prepend-icon="item.icon"
          no-action
        >
          <template v-slot:activator>
            <v-list-item-content>
              <v-list-item-title v-text="item.title"></v-list-item-title>
            </v-list-item-content>
          </template>

          <v-list-item
            v-for="child in item.items"
            :key="child.title"
            :to="child.link"
            link
          >
            <v-list-item-content>
              <v-list-item-title v-text="child.title"></v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list-group>
        <v-list-item link :to="{ name: 'activity_logs' }">
          <v-list-item-icon>
            <v-icon>mdi-history</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Activity Logs</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <!-- Content -->
    <router-view />
  </v-app>
</template>

<script>

import Axios from "axios";
export default {
  data() {
    return {
      drawer: true,
      mini: false,
      items: [
        {
          icon: "mdi-account",
          items: [
            { title: "Create New", link: "/patient/create" },
            { title: "Patients Record", link: "/patient/index" },
          ],
          title: "Patient",
        },
        {
          icon: "mdi-stethoscope",
          items: [
            { title: "Create Services", link: "/patientservice/create" },
            { title: "Services List", link: "/patientservice/index" },
          ],
          title: "Services",
        },
        {
          icon: "mdi-account-arrow-right-outline",
          items: [
            { title: "Create User", link: "/user/create" },
            { title: "Users Record", link: "/user/index" },
          ],
          title: "Users",
        },
        {
          icon: "fa-cog",
          items: [
            { title: "Services", link: "/service/index" },
            { title: "Service Procedures", link: "/procedure/index" },
            { title: "Permissions" },
            { title: "Roles" },
          ],
          title: "Settings",
        },
      ],
      right: null,
      selectedItem: 1,
      user: null,
      loading: null,
      initiated: false,
      user: "",
    };
  },
  methods: {
    logout() {

      let access_token = localStorage.getItem('access_token');
      
      Axios.get("/api/auth/logout", {
        headers: {
          Authorization: "Bearer " + access_token,
        },
      }).then(
        (response) => {
          if(response.data.success)
          {
            localStorage.removeItem("access_token");
            localStorage.removeItem("user");
            this.$router.push("/login").catch(() => {});
          }  
        },
        (error) => {
          console.log(error);
        }
      );
    },
  },

  mounted() {
    this.user = localStorage.getItem('user');
  }
};
</script>