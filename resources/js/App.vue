<template>
  <v-app>

    <!-- Navbar -->
    <v-app-bar
      dense
      app
    >
      
      <v-btn
          icon
          @click.stop="mini = !mini"
        >
        <v-app-bar-nav-icon></v-app-bar-nav-icon>
      </v-btn>

      <v-spacer></v-spacer>

      <v-btn icon>
        <v-icon>mdi-heart</v-icon>
      </v-btn>

      <v-btn icon>
        <v-icon>mdi-magnify</v-icon>
      </v-btn>

      <v-menu
        left
        bottom
      >
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            icon
            v-bind="attrs"
            v-on="on"
          >
            <v-icon>mdi-dots-vertical</v-icon>
          </v-btn>
        </template>

        <v-list>
          <v-list-item
            v-for="n in 5"
            :key="n"
            @click="() => {}"
          >
            <v-list-item-title>Option {{ n }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </v-app-bar> 
    
    <!-- Sidebar -->
    <v-navigation-drawer
      v-model="drawer"
      :mini-variant.sync="mini"
      dark
      permanent
      app
    >
      <v-list-item>
        <v-list-item-content>
          <v-list-item-title class="title">
            Application
          </v-list-item-title>
          <v-list-item-subtitle>
            subtext
          </v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>

      <v-divider></v-divider>

      <v-list>
        <v-list-item link>
          <v-list-item-icon>
            <v-icon>mdi-view-dashboard</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Dashboard</v-list-item-title>
        </v-list-item>
        <v-list-group
          v-for="item in items"
          :key="item.title"
          v-model="item.active"
          :prepend-icon="item.action"
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
          >
            <v-list-item-content>
              <v-list-item-title v-text="child.title"></v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list-group>
      </v-list>
    </v-navigation-drawer>


    <!-- Content -->
    <v-container class="full-height" fluid>
      <v-row>
        <v-col>
        </v-col>
      </v-row>
    </v-container>
    
  </v-app>
</template>

<script>

  export default {
    data () {
      return {
        drawer: true,
        mini: false,
        items: [
          {
            action: 'mdi-silverware-fork-knife',
            active: true,
            items: [
              { title: 'Breakfast & brunch' },
              { title: 'New American' },
              { title: 'Sushi' },
            ],
            title: 'Dining',
          },
          {
            action: 'mdi-school',
            items: [{ title: 'List Item' }],
            title: 'Education',
          },
          {
            action: 'mdi-run',
            items: [{ title: 'List Item' }],
            title: 'Family',
          },
          {
            action: 'mdi-bottle-tonic-plus',
            items: [{ title: 'List Item' }],
            title: 'Health',
          },
          {
            action: 'mdi-content-cut',
            items: [{ title: 'List Item' }],
            title: 'Office',
          },
          {
            action: 'mdi-tag',
            items: [{ title: 'List Item' }],
            title: 'Promotions',
          },
        ],
        right: null,
      }
    },
  }

</script>