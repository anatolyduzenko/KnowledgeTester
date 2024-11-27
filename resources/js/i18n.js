import { createI18n } from "vue-i18n";

const messages = {
  en: {
    login: "Login",
    email: "Email",
    password: "Password",
    submit: "Submit",
    quiz: "Quiz",
    welcome: "Welcome to the PHP Knowledge Quiz!",
    question: "Question",
    answer_placeholder: "Type your answer here...",
    total_score: "Total Score",
    next_question: "Next Question",
  },
  ru: {
    login: "Войти",
    email: "Электронная почта",
    password: "Пароль",
    submit: "Отправить",
    quiz: "Викторина",
    welcome: "Добро пожаловать в викторину знаний PHP!",
    question: "Вопрос",
    answer_placeholder: "Введите ваш ответ здесь...",
    total_score: "Общий счет",
    next_question: "Следующий вопрос",
  },
};

const i18n = createI18n({
  locale: "en",
  messages,
});

export default i18n;
