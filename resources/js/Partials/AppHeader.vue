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
        <inertia-link :href="$route('logout')" as="button" method="get" class="btn btn-sm nav-link logout-link " style="display: inline;background: #FF4A31" type="button">Logout</inertia-link>
      </li>

      <!-- <li class="nav-item dropdown" v-if="user">
        <a class="nav-link" @click="toggleDropdown">
          <span>
                <div class="d-felx badge-pill">
                  <span class="fa fa-user mr-2"></span>
                  <span><b>{{user.name}}</b></span>
                  <span class="fa fa-angle-down ml-2"></span>
                </div>
          </span>
        </a>
        <div v-if="isDropdownOpen" class="dropdown-menu" style="left: -2.5em;">
          <inertia-link :href="$route('manage-account')" class="dropdown-item">
            <i class="fa fa-cog"></i> Manage Account
          </inertia-link>
          <inertia-link :href="$route('logout')" method="get" class="dropdown-item">
            <i class="fa fa-power-off"></i> Logout
          </inertia-link>
          <inertia-link :href="$route('logout')" method="get" class="dropdown-item">
            <i class="fa fa-power-off"></i> Logout 2
          </inertia-link>
        </div>
      </li> -->
      
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-light-orange elevation-4" style="position: fixed;">  
    <!-- Brand Logo -->
    <div class="brand-link text-center">
      <span class="brand-text font-weight-bold text-orange" style="font-size: 22px;"><b style="color: #000;">Project Monitoring</b> IMS</span>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          
          <li class="nav-item">
            <a :href="$route('dashboard.index')" class="nav-link" :class="$route().current('dashboard.index') ? 'active' : ''">
              <i class="fas fa-th-large nav-icon"></i>
              <p>Dashboard</p>
            </a>
          </li>

          <li class="nav-item" :class="$route().current().indexOf('members') >= 0 ? 'menu-open' : ''">
            <a href="#" class="nav-link" :class="$route().current().indexOf('members') >= 0 ? 'active' : ''">
              <i class="nav-icon fas fa-users"></i>
              <p>
                Members
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a :href="$route('members.manage_member')" class="nav-link" :class="$route().current('members.manage_member') ? 'active' : ''">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Manage</p>
                </a>
              </li>
              <li class="nav-item" v-if="user && (user.role === 'Super Admin' || user.role === 'Admin 1' || user.role === 'Admin 2')">
                <a :href="$route('members.list_members')" class="nav-link" :class="$route().current('members.list_members') ? 'active' : ''">
                  <i class="far fa-circle nav-icon"></i>
                  <p>List</p>
                </a>
              </li>
              <li class="nav-item" v-if="user && user.role === 'Super Admin'">
                <a :href="$route('members.add_member')" class="nav-link" :class="$route().current('members.add_member') ? 'active' : ''">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add New</p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item" :class="$route().current().indexOf('projects') >= 0 ? 'menu-open' : ''">
            <a href="#" class="nav-link" :class="$route().current().indexOf('projects') >= 0 ? 'active' : ''">
              <i class="nav-icon fas fa-th-list"></i>
              <p>
                Projects
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item" v-if="user && user.role === 'Admin 1'">
                <a :href="$route('projects.add_project')" class="nav-link" :class="$route().current('projects.add_project') ? 'active' : ''">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add New</p>
                </a>
              </li>
              <li class="nav-item">
                <a :href="$route('projects.list_projects')" class="nav-link" :class="$route().current('projects.list_projects') ? 'active' : ''">
                  <i class="far fa-circle nav-icon"></i>
                  <p>List</p>
                </a>
              </li>
            </ul>
          </li>
          
          <li class="nav-item" v-if="user && (user.role === 'Admin 2' || user.role === 'User')">
            <a :href="$route('tasks.page_tasks')" class="nav-link" :class="$route().current('tasks.page_tasks') ? 'active' : ''">
              <i class="nav-icon fas fa-tasks"></i>
              <p>Tasks</p>
            </a>
          </li>

          <li class="nav-item" v-if="user && (user.role === 'Super Admin' || user.role === 'Admin 1' || user.role === 'Admin 2')">
            <a :href="$route('reports.page_reports')" class="nav-link" :class="$route().current('reports.page_reports') ? 'active' : ''">
              <i class="nav-icon fas fa-newspaper"></i>
              <p>Reports</p>
            </a>
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

    // name: "AppHeader",
    // setup() {
    //   const user = computed(() => usePage().props.value.auth.user);
    //   const isDropdownOpen = ref(false);

    //   const toggleDropdown = () => {
    //     isDropdownOpen.value = !isDropdownOpen.value;
    //   };

    //   return {
    //     user,
    //     isDropdownOpen,
    //     toggleDropdown,
    //   };
    // },
}
</script>
