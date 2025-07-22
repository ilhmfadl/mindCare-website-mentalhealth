<template>
  <div class="tes-diri-container">
    <section class="hero">
      <div class="hero-content">
        <h1>Kenali Kondisi Mentalmu Sekarang</h1>
        <p class="desc">Kuesioner ini dirancang untuk membantu Anda memahami kondisi kesehatan mental Anda saat ini. Setiap pertanyaan memiliki skala 1-5 dengan penjelasan:</p>
        <div class="skala-row">
          <div class="skala skala-1"><span>1</span><br> Tidak Pernah</div>
          <div class="skala skala-2"><span>2</span><br> Jarang</div>
          <div class="skala skala-3"><span>3</span><br> Kadang</div>
          <div class="skala skala-4"><span>4</span><br> Sering</div>
          <div class="skala skala-5"><span>5</span><br> Selalu</div>
        </div>
        <p class="note">Silakan jawab dengan jujur sesuai dengan perasaan Anda dalam 2 minggu terakhir. Tidak ada jawaban yang salah.</p>
      </div>
    </section>

    <!-- Question Section (Paginated) -->
    <section v-if="!showResults" class="question-section">
      <div class="progress-indicator">
        Halaman {{ currentPage + 1 }} dari {{ totalPages }}
      </div>

      <QuestionItem
        v-for="(question, index) in paginatedQuestions"
        :key="getGlobalQuestionIndex(index)"
        :question-text="question"
        :question-index="getGlobalQuestionIndex(index)"
        v-model="answers[getGlobalQuestionIndex(index)]"
      />
      
      <div class="submission-area">
        <button v-if="!isLastPage" @click="nextPage" class="submit-button" :disabled="!isCurrentPageAnswered">
          Selanjutnya
        </button>
        <button v-if="isLastPage" @click="submitTest" class="submit-button" :disabled="!allQuestionsAnswered">
          Kirim Jawaban
        </button>
      </div>
    </section>

    <!-- Results Section -->
    <ResultIsDepresi v-if="showResults" :score="totalScore" />
  </div>
</template>

<script>
import QuestionItem from '../components/QuestionItem.vue';
import ResultIsDepresi from './result/resultIsDepresi.vue';
import { testState } from '../store/testState';

export default {
  name: 'TesDiri',
  components: {
    QuestionItem,
    ResultIsDepresi
  },
  data() {
    return {
      questions: [
        'Dalam 2 minggu terakhir, seberapa sering Anda merasa kurang tertarik atau senang dalam melakukan sesuatu?',
        'Seberapa sering Anda merasa murung, sedih, atau putus asa?',
        'Seberapa sering Anda merasa sulit tidur atau tidur terlalu banyak?',
        'Seberapa sering Anda merasa lelah atau kurang berenergi?',
        'Seberapa sering Anda merasa nafsu makan berkurang atau makan berlebihan?',
        'Seberapa sering Anda merasa buruk tentang diri sendiri, atau merasa bahwa Anda adalah seorang yang gagal?',
        'Seberapa sering Anda merasa sulit berkonsentrasi pada sesuatu, seperti membaca koran atau menonton televisi?',
        'Seberapa sering Anda bergerak atau berbicara sangat lambat sehingga orang lain bisa memperhatikan?',
        'Seberapa sering Anda merasa gelisah atau resah sehingga Anda lebih sering bergerak dari biasanya?',
        'Seberapa sering Anda berpikir untuk menyakiti diri sendiri atau berpikir bahwa lebih baik Anda mati?'
      ],
      answers: Array(10).fill(null),
      showResults: false,
      totalScore: 0,
      currentPage: 0,
      questionsPerPage: 5,
    }
  },
  computed: {
    totalPages() {
      return Math.ceil(this.questions.length / this.questionsPerPage);
    },
    isLastPage() {
      return this.currentPage === this.totalPages - 1;
    },
    paginatedQuestions() {
      const start = this.currentPage * this.questionsPerPage;
      const end = start + this.questionsPerPage;
      return this.questions.slice(start, end);
    },
    isCurrentPageAnswered() {
      const start = this.currentPage * this.questionsPerPage;
      const end = start + this.questionsPerPage;
      const currentPageAnswers = this.answers.slice(start, end);
      return currentPageAnswers.every(answer => answer !== null);
    },
    allQuestionsAnswered() {
      return this.answers.every(answer => answer !== null);
    },
    resultInterpretation() {
      // Adjusted score ranges for 10 questions
      if (this.totalScore <= 14) return "Kondisi kesehatan mental Anda tampaknya sangat baik. Pertahankan!";
      if (this.totalScore <= 24) return "Ada beberapa gejala ringan. Penting untuk memperhatikan kesehatan mental Anda dan beristirahat.";
      if (this.totalScore <= 36) return "Anda menunjukkan gejala tingkat sedang. Pertimbangkan untuk berbicara dengan seorang teman atau profesional.";
      return "Anda menunjukkan gejala yang signifikan. Sangat disarankan untuk mencari bantuan profesional secepatnya.";
    }
  },
  methods: {
    getGlobalQuestionIndex(index) {
      return this.currentPage * this.questionsPerPage + index;
    },
    nextPage() {
      if (!this.isLastPage) {
        this.currentPage++;
      }
    },
    submitTest() {
      if (!this.allQuestionsAnswered) return;
      this.totalScore = this.answers.reduce((total, answer) => total + answer, 0);

      // Determine result type based on score or other logic
      // For now, let's assume it's always Depresi for this example
      const resultType = 'ResultIsDepresi'; 

      // Update the shared state
      testState.testCompleted = true;
      testState.score = this.totalScore;
      testState.resultType = resultType;

      // Navigate to the result page
      this.$router.push({ name: resultType, params: { score: this.totalScore } });
    },
    resetTest() {
      this.answers = Array(this.questions.length).fill(null);
      this.totalScore = 0;
      this.showResults = false;
      this.currentPage = 0;
      
      // Also reset the shared state
      testState.testCompleted = false;
      testState.score = 0;
      testState.resultType = null;
    }
  }
}
</script>

<style scoped>
.tes-diri-container {
  font-family: 'Inter', Arial, sans-serif;
  min-height: 100vh;
}
.hero {
  background: linear-gradient(45deg, #725c96 30%, #c09df7 100%);
  padding: 100px 0 32px 0;
  color: #ffffff;
  position: relative;
}
.hero::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image: url('data:image/svg+xml;utf8,<svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="p" width="100" height="100" patternUnits="userSpaceOnUse" patternTransform="rotate(45)"><path d="M50 0V100M0 50H100" stroke="%23B19CDA" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="none"/><rect width="100%" height="100%" fill="url(%23p)"/></svg>');
  opacity: 0.1;
  z-index: 1;
}
.hero-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
  position: relative;
  z-index: 2;
}
.hero-content h1 {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 50px;
}
.hero-content .desc {
  font-size: 1.1rem;
  margin-bottom: 18px;
}
.skala-row {
  display: flex;
  gap: 12px;
  margin: 24px 0;
}
.skala {
  flex: 1;
  border-radius: 12px;
  color: #fff;
  font-weight: 600;
  text-align: center;
  padding: 10px 8px;
  font-size: 1rem;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  transition: transform 0.2s;
}
.skala:hover {
    transform: translateY(-3px);
}
.skala-1 { background: #E57373; }
.skala-2 { background: #FFB74D; }
.skala-3 { background: #FFD54F; }
.skala-4 { background: #81C784; }
.skala-5 { background: #4DB6AC; }

.note {
  font-size: 0.9rem;
  margin-top: 8px;
  color: #f3f3f3;
}
.question-section, .results-section {
  background: #faf7f3;
  margin-top: -32px;
  padding: 50px 0 60px 0;
  position: relative;
  z-index: 3;
}
.progress-indicator {
  font-size: 1rem;
  font-weight: 500;
  color: #888;
  text-align: center;
  margin-bottom: 24px;
}
.submission-area {
  max-width: 900px;
  margin: 40px auto 0;
  padding: 0 24px;
  text-align: right;
}

.submit-button {
  background-color: #6A4C9B;
  color: white;
  border: none;
  padding: 1rem 2.5rem;
  border-radius: 30px;
  font-weight: 600;
  font-size: 1.1rem;
  cursor: pointer;
  transition: background-color 0.3s, transform 0.2s;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.submit-button:hover {
  background-color: #8A6FB9;
  transform: translateY(-2px);
}
.submit-button:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

/* --- New Styles for Results --- */
.results-section {
  max-width: 900px;
  margin: -130px auto 0;
  padding: 40px 24px 50px;
  border-radius: 32px;
  text-align: center;
  animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(15px); }
  to { opacity: 1; transform: translateY(0); }
}

.results-section h2 {
  font-size: 2.2rem;
  font-weight: 700;
  color: #333;
  margin-bottom: 1rem;
}

.results-section .score {
  font-size: 1.2rem;
  color: #555;
  margin-bottom: 1.5rem;
}

.results-section .score strong {
  font-size: 1.5rem;
  color: #6A4C9B;
}

.results-section .interpretation {
  font-size: 1.1rem;
  font-weight: 500;
  color: #444;
  line-height: 1.6;
  max-width: 600px;
  margin: 0 auto 2rem;
}

.actions-area {
  margin-top: 1rem;
}

.back-button {
  background-color: transparent;
  color: #6A4C9B;
  border: 2px solid #6A4C9B;
  padding: 0.8rem 2rem;
  border-radius: 30px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.2s ease-in-out;
}

.back-button:hover {
  background-color: #6A4C9B;
  color: white;
  transform: translateY(-3px);
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
</style> 