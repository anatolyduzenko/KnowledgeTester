<template>
  <div class="min-h-screen bg-gray-100 flex items-center justify-center">
    <div class="w-full max-w-4xl bg-white p-6 rounded-lg shadow-lg">
      <h1 class="text-2xl font-semibold text-center mb-6">{{ $t("quiz") }}</h1>

      <!-- Questions -->
      <div v-if="!loading && questions.length && !showResults">
        <div v-for="(question, index) in questions" :key="question.id" class="mb-6">
          <p class="font-medium">
            {{ index + 1 }}. {{ question.question }}
          </p>
          <textarea
            v-model="answers[question.id]"
            class="mt-2 w-full p-2 border rounded-lg focus:ring focus:ring-blue-200"
            rows="3"
            :placeholder="$t('answer_placeholder')"
          ></textarea>
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
        <h2 class="text-xl font-semibold text-green-600">{{ $t("total_score") }}: {{ totalScore }}</h2>
        <ul class="mt-4">
          <li
            v-for="result in results"
            :key="result.question_id"
            class="mb-4 p-4 border rounded-lg"
          >
            <strong>Q{{ result.question_id }}:</strong> {{ result.score }} / 10
            <p>{{ result.feedback }}</p>
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

export default {
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
    async fetchQuestions() {
      this.loading = true;
      try {
        const response = await axios.get("/api/questions", {
          headers: { 
            Authorization: `Bearer ${localStorage.getItem("authToken")}`,
            "Accept-Language": this.$i18n.locale,
          }
        });
        this.questions = response.data;
      } catch (error) {
        console.error("Error fetching questions", error);
      } finally {
        this.loading = false;
      }
    },
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
    restartQuiz() {
      this.answers = {};
      this.results = [];
      this.totalScore = 0;
      this.showResults = false;
      this.fetchQuestions(); // Reload questions
    },
  },
  mounted() {
    this.fetchQuestions();
    document.addEventListener("reload-questions", this.fetchQuestions);
  },
  beforeUnmount() {
    document.removeEventListener("reload-questions", this.fetchQuestions);
  },
};
</script>
