<template>
  <div class="hold-transition login-page">
    <div class="row align-items-center">
      <div class="col-md-8">
        <div>
          <img
            src="images/bg.png"
            class="w-100 h-100"
          />
        </div>
      </div>
      <div class="col-md-4">
        <div class="d-flex justify-content-center">
          <div class="login-box">
            <div class="card card-outline card-danger">
              <div class="card-body">
                <form method="post" @submit.prevent="submit">
                  <errors-and-messages :errors="errors"></errors-and-messages>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="username" v-model="form.name" placeholder="Username" />
                      <div class="input-group-append">
                        <div class="input-group-text">
                          <span class="fas fa-envelope"></span>
                        </div>
                      </div>
                    </div>
                    <div class="input-group mb-3">
                      <input :type="showPassword ? 'password' : 'text'" class="form-control" name="password" id="password" v-model="form.password" placeholder="Password" />
                      <div class="input-group-append">
                        <div class="input-group-text">
                          <span class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'" @click="togglePassword"></span>
                        </div>
                      </div>
                    </div>                    
                    <div class="form-group">
                      <button class="btn btn-block btn-login text-white " type="submit">Login</button>
                    </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="text-center mt-4">
          <p>Made by Aprilia SD | PKL IMS 2023</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import AppHeader from "../../Partials/AppHeader";
import ErrorsAndMessages from "../../Partials/ErrorsAndMessages";
import {Inertia} from "@inertiajs/inertia";
import { usePage } from '@inertiajs/inertia-vue3'
import {reactive,inject} from 'vue';

export default {
    data() {
      return {
        showPassword: true,
        form: {
          password: '',
        },
      };
    },

    methods: {
      togglePassword() {
        this.showPassword = !this.showPassword;
      },
    },

    components: {
        ErrorsAndMessages,
        AppHeader
    },

    name: "Login",
    props: {
        errors: Object
    },

    setup() {
        const form = reactive({
            name: null,
            password: null,
            _token: usePage().props.value.csrf_token
        });
        function submit() {
            Inertia.post(route('login'), form);
        }
        return {
            form,
            submit
        }
    }
}
</script>

<style scoped>
    form {
        margin-top: 20px;
    }
    .btn-login {
    background-color: #FF4A31;
    border-color: #FF4A31;
  }
</style>