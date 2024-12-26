<template>
  <div class="min-h-screen bg-gray-100 flex items-center justify-center">
    <div class="w-full max-w-4xl bg-white p-6 rounded-lg shadow-lg">
      <h1 class="text-2xl font-semibold text-center mb-6">{{ $t("quiz") }}</h1>
      
      <ThemeSelect @update-data="updateQuestionsData"/>
      <!-- Questions -->
      <div v-if="!loading && questions.length && !showResults">
        <div v-for="(question, index) in questions" :key="index" class="mb-6">
          <p class="font-medium">
            {{ index + 1 }}. {{ question.question }}
          </p>
          <p v-if="JSON.parse(question.correct_option).length > 1" class="text-sm text-gray-500 mb-2">
              {{ $t('multiple_choices') }}
          </p>
          <hr/>
          <div v-for="(option, key) in JSON.parse(question.options)" :key="key" class="mb-2">
            <div class="flex items-center">
              <input
                  type="checkbox"
                  :id="'question-' + question.id + '-' + key"
                  :value="key"
                  v-model="answers[question.id]"
                  class="mr-2"
              />
              <label :for="'question-' + question.id + '-' + key" class="cursor-pointer">
                  {{ key }}. {{ option }}
              </label>
            </div>
          </div>
        </div>
        <button
          @click="submitQuiz"
          class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700"
        >
          {{ $t("submit") }}
        </button>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center">{{ $t("loading") }}</div>

      <!-- Results -->
      <div v-if="showResults" class="mt-6">
        <h2 class="text-xl font-semibold text-green-600">{{ $t("total_score") }}: {{ totalScore }} / 100 </h2>
        <ul class="mt-4">
          <li
            v-for="result in results"
            :key="result.question_id"
            class="mb-4 p-4 border rounded-lg"
          >
            <strong>Q {{ result.question_id }}: {{ result.question }}</strong> {{ result.score }} / 10
            <p class="border-solid border-t border-b my-4">{{ result.feedback }}</p>
            <div v-if="result.user_answer.length > 0" class="border-solid border-b my-4">
              <h4 class="font-medium flex justify-between items-center">{{ $t('provided_answers') }}</h4>
              <p v-html="result.user_answer"></p>
            </div>
            <CorrectAnswers 
              :answers="result.correct_answers" 
              title="Correct Answers"
            />
          </li>
        </ul>
        <button
          @click="restartQuiz"
          class="mt-4 bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700"
        >
          Restart Quiz
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import ThemeSelect from './ThemeSelect.vue';
import CorrectAnswers from "./CorrectAnswers.vue";

export default {
  components: { ThemeSelect, CorrectAnswers },
  data() {
    return {
      questions: [],
      answers: {},
      loading: true,
      results: null,
      showResults: false, 
      results: [], 
      totalScore: 0, 
    };
  },
  methods: {
    async submitQuiz() {
      try {
        this.loading = true;

        const payload = Object.keys(this.answers).map((question_id) => ({
          question_id,
          user_answer: this.answers[question_id],
        }));

        const response = await axios.post("/api/evaluate",{
          responses: payload 
        }, { headers: { 
            Authorization: `Bearer ${localStorage.getItem("authToken")}`,
            "Accept-Language": this.$i18n.locale,
          }
        });
        this.results = response.data.details;
        this.totalScore = response.data.totalScore;
        this.showResults = true;
      } catch (error) {
        console.error("Error submitting quiz:", error);
      } finally {
        this.loading = false;
      }
    },
    updateQuestionsData(data) {
        if(data.length > 0) {
          this.loading = false;
          this.questions = data[0];
          console.log(data);
          this.answers = data[1];
        } else {
          this.loading = true;
        }
    },
    restartQuiz() {
      this.results = [];
      this.totalScore = 0;
      this.showResults = false;
      this.$emit('reload-questions', [this.questions, this.answers]);
    },
    loadingData() {
      this.loading = true;
      this.results = [];
      this.totalScore = 0;
      this.showResults = false;
    }
  },
  mounted() {
    document.addEventListener("reload-questions", this.loadingData);
  },
  beforeUnmount() {
    document.removeEventListener("reload-questions", this.loadingData);
  },
};
</script>
