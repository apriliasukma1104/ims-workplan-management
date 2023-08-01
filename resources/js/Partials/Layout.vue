<template>
  
  <app-header></app-header>
          <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0" style="font-size: 21px;">{{ title }}</h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
       		<slot/>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
</template>
<script>
  import Toast from 'primevue/toast';
	import AppHeader from "./AppHeader";
  import Breadcrumb from 'primevue/breadcrumb';
	export default{
		name:"appLayout",
    data() {
        return {
            home: {icon: 'pi pi-home', to: '/'},
            items: [
                {label: 'Computer'},
                {label: 'Notebook'},
                {label: 'Accessories'},
                {label: 'Backpacks'},
                {label: 'Item'}
            ]
        }
    },
		props:{
			title:{
				default:null,
				type:String
			},
      subtitle:{
        default:null,
        type:String
      }
		},
		components: {
	       AppHeader,
         Toast,
         Breadcrumb
	    },
	}
</script>