<template>
    <div class="relative min-h-screen bg-gray-100">
    <!-- Language Selector -->
    <div class="absolute top-4 right-4">
      <select
        v-model="$i18n.locale"
        @change="changeLanguage"
        class="bg-white border border-gray-300 rounded-md shadow-sm py-2 px-8 focus:outline-none focus:ring focus:ring-blue-200 text-gray-700"
      >
        <option value="en">English</option>
        <option value="ru">Русский</option>
      </select>
      <button v-if="isLoggedIn"
          @click="logout"
          class="bg-red-600 text-white py-2 px-4 mx-6 rounded-lg hover:bg-red-700 focus:ring focus:ring-red-300">
          {{ $t("logout") }}
        </button>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-6">
      <router-view />
    </div>
  </div>
</template>

<script>
    import { ref } from "vue";
    import axios from "axios";

    export default {
        data() {
            return {
                isLoggedIn: false,
            };
        },
        setup() {
            const currentLocale = ref(localStorage.getItem("locale") || "en");

            const changeLanguage = () => {
                localStorage.setItem("locale", currentLocale.value);
                document.dispatchEvent(new Event("reload-questions"));
            };

            const logout = async () => {
                try {
                    await axios.post("/api/logout", {}, {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem("authToken")}`,
                        },
                    });

                    localStorage.removeItem("authToken");

                    window.location.href = "/login";
                } catch (error) {
                    console.error("Error during logout:", error);
                }
            };

            return {
                currentLocale,
                changeLanguage,
                logout,
            };
        },
        mounted() {
            this.$i18n.locale = this.currentLocale;
            localStorage.setItem('locale', this.currentLocale); 
            this.isLoggedIn = !!localStorage.getItem('authToken');
        },
    };
</script>

  