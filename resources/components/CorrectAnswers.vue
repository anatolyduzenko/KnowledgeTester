<template>
    <div class="border-solid border-b my-4">
      <!-- Accordion Header -->
      <h4 
        @click="toggleAccordion" 
        class="cursor-pointer font-medium flex justify-between items-center"
      >
        {{ title }}
        <span>
          <svg
            v-if="isOpen"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="w-4 h-4 transform rotate-180"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M19.5 15l-7.5-7.5-7.5 7.5"
            />
          </svg>
          <svg
            v-else
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="w-4 h-4"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M19.5 15l-7.5-7.5-7.5 7.5"
            />
          </svg>
        </span>
      </h4>
  
      <!-- Accordion Content -->
      <div v-show="isOpen" class="mt-2">
        <div v-for="(option, key) in answers" :key="key" class="mb-2">
          <p>{{ key }}. {{ option }}</p>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      answers: {
        type: Object, // Accepts an object containing answers
        required: true,
      },
      title: {
        type: String, // Accepts a title for the accordion
        required: true,
      },
    },
    data() {
      return {
        isOpen: false, // Accordion state
      };
    },
    methods: {
      toggleAccordion() {
        this.isOpen = !this.isOpen; // Toggle accordion visibility
      },
    },
  };
  </script>
  