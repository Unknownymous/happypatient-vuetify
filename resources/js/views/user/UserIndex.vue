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
          <v-card-title>
            Users Record
            <v-spacer></v-spacer>
            <v-text-field
              v-model="search"
              append-icon="mdi-magnify"
              label="Search"
              single-line
              hide-details
            ></v-text-field>
            <v-spacer></v-spacer>
            <v-btn
              color="primary"
              fab
              dark
              class="mb-2"
              :to="{ name: 'user.create' }"
            >
              <v-icon>mdi-plus</v-icon>
            </v-btn>
          </v-card-title>
          <v-data-table
            :headers="headers"
            :items="users"
            :search="search"
          > 
          
            <!-- <template v-slot:item.rows="{item}">
              {{ index(item) }}
            </template> -->

            <template v-slot:item.actions="{ item }">
              <v-icon
                small
                class="mr-2"
                color="green"
                @click="editUser(item)"
              >
                mdi-pencil
                
              </v-icon>
              <v-icon
                small
                color="red"
                @click="showConfirmAlert(item)"
              >
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

  export default {
    data() {
      return {
        search: "",
        headers: [

          { text: "Name", value: "name" },
          { text: "Description", value: "description" },
          { text: "License", value: "license" },
          { text: "Username", value: "username" },
          { text: "Email", value: "email" },
          { text: "Roles", value: "roles" },
          { text: "Actions", value: "actions", sortable: false },
        ],
        users: [],
        items: [
          { 
            text: 'Home', 
            disabled: false, 
            link: '/dashboard',
          },
          { 
            text: 'Users Record', 
            disabled: true, 
          }
        ]
      }
    },

    methods: {
      getUsers(){
        Axios.get('/user/index').then( (response) => {
          console.log(response.data.users);
          this.users = response.data.users;
        });
      },

      editUser (item) {
        this.$router.push({ name: 'user.edit', params: { userid: item.id} });
      },

      deleteUser (userid) {

        const data = { userid: userid };

        Axios.post('/user/delete', data).then( (response) => {
          console.log(response.data);
        }, (error) => {
          console.log(error)
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

      showConfirmAlert(item) {
        this.$swal({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Delete record!'
        }).then((result) => { // <--

            if (result.value) { // <-- if confirmed
                
                
                const userid = item.id;
                const index = this.users.indexOf(item);

                //Call delete Patient function
                this.deleteUser(userid);

                //Remove item from array users
                this.users.splice(index, 1);

                this.$swal({
                  position: 'center',
                  icon: 'success',
                  title: 'Record has been deleted',
                  showConfirmButton: false,
                  timer: 2500
                });
            }
        });
      },
    },
    mounted() {
      this.getUsers();
    },
  };
</script>