<template>
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item" v-if="user">
        <span class="navbar-text mr-3" v-if="user">
            Logged in as {{user.name}}
        </span>
        <inertia-link :href="$route('logout')" as="button" method="get" class="btn btn-sm nav-link logout-link " style="display: inline;background: #ff0008" type="button">Logout</inertia-link>
      </li>      
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-light-red elevation-4" style="position: fixed;">  
    <!-- Brand Logo -->
    <div class="brand-link text-center">
      <span style="font-size: 22px; color: #ff0008; font-weight: bold;"><b style="color: #000;">WorkPlan </b>Management</span>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          
          <li class="nav-item" v-if="user && (user.role === 'Kadep' || user.role === 'Staf')">
            <a :href="$route('dashboard.index')" class="nav-link" :class="$route().current('dashboard.index') ? 'active' : ''">
              <i class="fas fa-th-large nav-icon"></i>
              <p>Dashboard</p>
            </a>
          </li>

          <li class="nav-item" :class="$route().current().indexOf('members') >= 0 ? 'menu-open' : ''" v-if="user && user.role === 'Admin'">
            <a href="#" class="nav-link" :class="$route().current().indexOf('members') >= 0 ? 'active' : ''">
              <i class="nav-icon fas fa-users"></i>
              <p>
                Members
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item" v-if="user && user.role === 'Admin'">
                <a :href="$route('members.list_members')" class="nav-link" :class="$route().current('members.list_members') ? 'active' : ''">
                  <i class="far fa-circle nav-icon"></i>
                  <p>List</p>
                </a>
              </li>
              <li class="nav-item" v-if="user && user.role === 'Admin'">
                <a :href="$route('members.add_member')" class="nav-link" :class="$route().current('members.add_member') ? 'active' : ''">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add New</p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item" :class="$route().current().indexOf('workplans') >= 0 ? 'menu-open' : ''" v-if="user && (user.role === 'Kadep' || user.role === 'Kabag' || user.role === 'Staf')" >
            <a href="#" class="nav-link" :class="$route().current().indexOf('workplans') >= 0 ? 'active' : ''">
              <i class="nav-icon fas fa-th-list"></i>
              <p>
                Work Plans
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item" v-if="user && (user.role === 'Kadep' || user.role === 'Kabag' || user.role === 'Staf')">
                <a :href="$route('workplans.list_workplans')" class="nav-link" :class="$route().current('workplans.list_workplans') ? 'active' : ''">
                  <i class="far fa-circle nav-icon"></i>
                  <p>List</p>
                </a>
              </li>
              <li class="nav-item" v-if="user && user.role === 'Kabag'">
                <a :href="$route('workplans.add_workplan')" class="nav-link" :class="$route().current('workplans.add_workplan') ? 'active' : ''">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add New</p>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

</template>

<script>
import {computed} from "vue";
import {usePage} from "@inertiajs/inertia-vue3";

export default {
    name: "AppHeader",
    setup() {
        const user = computed(() => usePage().props.value.auth.user);
        return {
            user
        }
    },
}
</script>