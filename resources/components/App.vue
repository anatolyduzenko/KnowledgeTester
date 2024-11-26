<template>
    <div id="app" class="max-w-xl mx-auto p-6 bg-gray-50 min-h-screen">
      <h1 class="text-2xl font-bold text-center mb-6">PHP Knowledge Bot</h1>
  
      <div v-if="!loading" class="space-y-4">
        <div v-if="question">
          <div>
            <p class="text-lg font-medium mb-4">Question:</p>
            <p class="text-gray-700 mb-6">{{ question.question }}</p>
          </div>
          <textarea
            v-model="userAnswer"
            class="w-full p-3 border border-gray-300 rounded shadow-sm focus:ring focus:ring-blue-200"
            placeholder="Type your answer here..."
            rows="4"
          ></textarea>
          <button
            @click="submitAnswer"
            class="w-full bg-blue-600 text-white py-2 px-4 rounded shadow-md hover:bg-blue-700 focus:ring focus:ring-blue-300 mt-4"
          >
            Submit
          </button>
        </div>
        <div v-else>
          <p class="text-gray-500 text-center">No more questions available.</p>
        </div>
      </div>
  
      <div v-if="loading" class="text-center mt-6">
        <p class="text-gray-600">Loading...</p>
      </div>
  
      <div v-if="score" class="mt-6 bg-green-50 p-4 border border-green-300 rounded">
        <h3 class="text-lg font-semibold text-green-700">Result:</h3>
        <p class="text-gray-700">{{ score }}/10</p>
        <button
          @click="fetchQuestion"
          class="mt-4 bg-gray-600 text-white py-2 px-4 rounded shadow-md hover:bg-gray-700 focus:ring focus:ring-gray-300"
        >
          Next Question
        </button>
      </div>
      <div v-if="provideFeedback" class="mt-6 bg-green-50 p-4 border border-green-300 rounded">
        <p class="text-gray-700">{{ evaluation }}</p>
      </div>
    </div>
  </template>
  
  <script>
  import axios from "axios";
  
  export default {
    data() {
      return {
        question: null,
        userAnswer: "",
        evaluation: null,
        feedback: null,
        score: null,
        loading: false,
      };
    },
    methods: {
      async fetchQuestion() {
        this.loading = true;
        this.feedback = null;
        this.score = null;
        this.evaluation = null;
        try {
          const { data } = await axios.get("/api/question");
          this.question = data;
        } catch (error) {
          console.error(error);
        } finally {
          this.loading = false;
        }
      },
      async submitAnswer() {
        if (!this.userAnswer) {
          alert("Please provide an answer.");
          return;
        }
  
        this.loading = true;
        try {
          const { data } = await axios.post("/api/answer", {
            id: this.question.id,
            answer: this.userAnswer,
          });
          this.evaluation = data.evaluation || "No evaluation received.";
          this.feedback = data.feedback || "No feedback received.";
          this.score = data.score || "Score 0/10.";
        } catch (error) {
          console.error(error);
          this.response = "Something went wrong. Please try again.";
        } finally {
          this.loading = false;
        }
      },
    },
    mounted() {
      this.fetchQuestion();
    },
  };
  </script>
  