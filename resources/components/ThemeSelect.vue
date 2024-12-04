<template>
    <div class="p-6 bg-gray-50 mb-6 border-b-2 border-t-2 border-solid">
        <div class="mb-4">
            <label 
                for="theme" 
                class="block text-sm font-medium text-gray-700 mb-2"
            >
                {{ $t('questions_theme') }}:
            </label>
            <select 
                v-model="selectedTheme" 
                @change="fetchQuestionsData"
                class="block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            >
                <option value="">-- === --</option>
                <option value="oop_design_patterns">{{ $t("oop_design_patterns") }}</option>
                <option value="laravel">{{ $t('laravel') }}</option>
                <option value="sql_nosql">{{ $t('sql_nosql') }}</option>
                <option value="python_django">{{ $t('python_django') }}</option>
                <option value="php_basics">{{ $t('php_basics') }}</option>
                <option value="advanced_php">{{ $t('advanced_php') }}</option>
                <option value="software_architecture">{{ $t('software_architecture') }}</option>
                <option value="security">{{ $t('security') }}</option>
                <option value="web_technologies">{{ $t('web_technologies') }}</option>
                <option value="networks">{{ $t('networks') }}</option>
                <option value="software_testing_debugging">{{ $t('testing_debugging') }}</option>
            </select>
        </div>
    </div>

</template>


<script>
export default {
    data() {
        return {
            selectedTheme: '', 
            questions: [],
        };
    },
    methods: {
        async fetchQuestionsData() {
            this.$emit('update-data', this.questions);
            try {
                const response = await axios.get(`/api/questions?theme=${this.selectedTheme}`, {
                headers: { 
                    Authorization: `Bearer ${localStorage.getItem("authToken")}`,
                    "Accept-Language": this.$i18n.locale,
                }
                });
                this.questions = response.data;
                this.$emit('update-data', this.questions);
            } catch (error) {
                console.error("Error fetching questions", error);
            }
       },
    },
    mounted() {
        this.fetchQuestionsData(); 
        document.addEventListener("reload-questions", this.fetchQuestionsData);
    },
    beforeUnmount() {
        document.removeEventListener("reload-questions", this.fetchQuestionsData);
    },
};
</script>
