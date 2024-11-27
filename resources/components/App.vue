<template>
    <div>
        <select v-model="$i18n.locale" class="mb-4">
        <option value="en">English</option>
        <option value="ru">Русский</option>
        </select>
        <router-view />
    </div>
</template>

<script>
    export default {
    data() {
        return {
        currentLocale: localStorage.getItem('locale') || 'en', 
        };
    },
    watch: {
        currentLocale(newLocale) {
        this.$i18n.locale = newLocale; 
        },
    },
    methods: {
        updateLocale() {
        localStorage.setItem('locale', this.currentLocale); 
        this.$i18n.locale = this.currentLocale; 
        },
    },
    mounted() {
        this.$i18n.locale = this.currentLocale;
    },
    };
</script>

  