import { createI18n } from "vue-i18n";

const messages = {
  en: {
    login: "Login",
    logout: "Logout",
    email: "Email",
    password: "Password",
    submit: "Submit",
    quiz: "Quiz",
    welcome: "Welcome to the Knowledge Quiz!",
    question: "Question",
    answer_placeholder: "Type your answer here...",
    total_score: "Total Score",
    next_question: "Next Question",
  },
  ru: {
    login: "Войти",
    logout: "Выйти",
    email: "Электронная почта",
    password: "Пароль",
    submit: "Отправить",
    quiz: "Квиииз",
    welcome: "Добро пожаловать в викторину знаний!",
    question: "Вопрос",
    answer_placeholder: "Введите ваш ответ здесь...",
    total_score: "Общий счет",
    next_question: "Следующий вопрос",
  },
};

const i18n = createI18n({
  legacy: false,
  locale: "en",
  messages,
});

export default i18n;
