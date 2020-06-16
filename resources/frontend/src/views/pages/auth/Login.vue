<template>
  <div>
    <!--begin::Head-->
    <div class="kt-login__head">
      
    </div>
    <!--end::Head-->

    <!--begin::Body-->
    <div class="kt-login__body" >
      <!--begin::Signin-->
      <div class="kt-login__form">
        <div class="kt-login__title">
          <h3>VIP Coupon</h3>
        </div>

        <!--begin::Form-->
        <b-form class="kt-form" @submit.stop.prevent="onSubmit">
          <div role="alert" class="alert alert-info" v-if="this.$store.getters.message">
            <div class="alert-text">
              {{ this.$store.getters.message }}
            </div>
          </div>

          <div
            role="alert"
            v-bind:class="{ show: errors.length }"
            class="alert fade alert-danger"
          >
            <div class="alert-text" v-for="(error, i) in errors" :key="i">
              {{ error }}
            </div>
          </div>

          <b-form-group
            id="example-input-group-1"
            label=""
            label-for="example-input-1"
          >
            <b-form-input
              id="example-input-1"
              name="example-input-1"
              placeholder="Username"
              v-model="$v.form.employee_no.$model"
              :state="validateState('employee_no')"
              aria-describedby="input-1-live-feedback"
            ></b-form-input>

            <b-form-invalid-feedback id="input-1-live-feedback">
              Username is required.
            </b-form-invalid-feedback>
          </b-form-group>

          <b-form-group
            id="example-input-group-2"
            label=""
            label-for="example-input-2"
          >
            <b-form-input
              type="password"
              id="example-input-2"
              name="example-input-2"
              placeholder="password"
              v-model="$v.form.password.$model"
              :state="validateState('password')"
              aria-describedby="input-2-live-feedback"
            ></b-form-input>

            <b-form-invalid-feedback id="input-2-live-feedback">
              Password is required.
            </b-form-invalid-feedback>
          </b-form-group>

          <!--begin::Action-->
          <div class="kt-login__actions">
            <a href="#" class="kt-link kt-login__link-forgot">
             
            </a>
            <b-button
              type="submit"
              id="kt_submit"
              class="btn btn-danger btn-elevate kt-login__btn-primary"
            >
              {{ $t("AUTH.LOGIN.BUTTON") }}
            </b-button>
          </div>
          <!--end::Action-->
        </b-form>
        <!--end::Form-->

        <!--begin::Divider-->
        <!-- <div class="kt-login__divider">
          <div class="kt-divider">
            <span></span>
            <span>OR</span>
            <span></span>
          </div>
        </div> -->
        <!--end::Divider-->

        <!--begin::Options-->
        <!-- <div class="kt-login__options">
          <a href="#" class="btn btn-primary kt-btn">
            <i class="fab fa-facebook-f"></i>
            Facebook
          </a>

          <a href="#" class="btn btn-info kt-btn">
            <i class="fab fa-twitter"></i>
            Twitter
          </a>

          <a href="#" class="btn btn-danger kt-btn">
            <i class="fab fa-google"></i>
            Google
          </a>
        </div> -->
        <!--end::Options-->
      </div>
      <!--end::Signin-->
    </div>
    <!--end::Body-->
  </div>
</template>

<style lang="scss" scoped>
.kt-spinner.kt-spinner--right:before {
  right: 8px;
}
</style>

<script>
import { mapState } from "vuex";
import { LOGIN, LOGOUT } from "@/store/auth.module";

import { validationMixin } from "vuelidate";
import { email, minLength, required } from "vuelidate/lib/validators";

export default {
  mixins: [validationMixin],
  name: "login",
  data() {
    return {
      // Remove this dummy login info
      form: {
        employee_no: "",
        password: ""
      }
    };
  },
  validations: {
    form: {
      employee_no: {
        required
      },
      password: {
        required,
        minLength: minLength(3)
      }
    }
  },
  created(){
    if(this.$store.getters.isAuthenticated){
      this.$router.push({ name: "dashboard" });
    }
  },
  methods: {
    validateState(name) {
      const { $dirty, $error } = this.$v.form[name];
      return $dirty ? !$error : null;
    },
    resetForm() {
      this.form = {
        employee_no: null,
        password: null
      };

      this.$nextTick(() => {
        this.$v.$reset();
      });
    },
    onSubmit() {
      this.$v.form.$touch();
      if (this.$v.form.$anyError) {
        return;
      }

      const employee_no = this.$v.form.employee_no.$model;
      const password = this.$v.form.password.$model;

      // clear existing errors
      this.$store.dispatch(LOGOUT);

      // set spinner to submit button
      const submitButton = document.getElementById("kt_submit");
      submitButton.classList.add(
        "kt-spinner",
        "kt-spinner--light",
        "kt-spinner--right"
      );
      
      // dummy delay
      setTimeout(() => {
        // send login request
        
        this.$store
          .dispatch(LOGIN, { employee_no, password })
          // go to which page after successfully login
          .then(() => this.$router.push({ name: "dashboard" }));

        submitButton.classList.remove(
          "kt-spinner",
          "kt-spinner--light",
          "kt-spinner--right"
        );
      }, 2000);
    }
  },
  computed: {
    ...mapState({
      errors: state => state.auth.errors
    }),
    backgroundImage() {
      return process.env.BASE_URL + "assets/media/bg/bg-4.jpg";
    }
  }
};
</script>
