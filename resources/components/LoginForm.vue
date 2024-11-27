<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-lg">
      <h1 class="text-2xl font-semibold text-center mb-6">{{ $t("login") }}</h1>

      <form @submit.prevent="login">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">{{ $t("email") }}</label>
          <input
            v-model="email"
            type="email"
            class="mt-1 block w-full p-2 border rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-500"
          />
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">{{ $t("password") }}</label>
          <input
            v-model="password"
            type="password"
            class="mt-1 block w-full p-2 border rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-500"
          />
        </div>

        <button
          type="submit"
          class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700"
        >
          {{ $t("submit") }}
        </button>
      </form>
    </div>
  </div>
</template>

<script>
  import axios from "axios";

  export default {
    data() {
      return {
        email: "",
        password: "",
        errorMessage: "",
      };
    },
    methods: {
      async login() {
        try {
          this.errorMessage = "";

          await axios.get("/sanctum/csrf-cookie"); 
          const response = await axios.post("/api/login", {
            email: this.email,
            password: this.password,
          });

          const token = response.data.token;
          localStorage.setItem("authToken", token);
          this.$router.push({ name: "quiz" });
        } catch (error) {
          this.errorMessage =
            error.response?.data?.message || "Login failed. Please try again.";
        }
      },
    },
  };
</script>
