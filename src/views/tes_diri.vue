<template>
  <div class="tes-diri-container">
    <div v-if="loading" class="loading-overlay">
      <div class="loading-spinner"></div>
      <div class="loading-text">Memproses hasil tes...</div>
    </div>
    <section class="hero" v-if="!showResults">
      <div class="hero-content">
        <h1>Kenali Kondisi Mentalmu Sekarang</h1>
        <p class="desc">Petunjuk : </p>
        
        <p class="note">Bacalah petunjuk ini seluruhnya sebelum mulai mengisi. Pertanyaan berikut berhubungan dengan masalah yang mungkin mengganggu Anda selama 30 hari terakhir. Apabila Anda menganggap pertanyaan itu berlaku bagi Anda dan Anda mengalami masalah yang disebutkan dalam 30 hari terakhir, berilah tanda pada kolom Y. Sebaliknya, Apabila Anda menganggap pertanyaan itu tidak berlaku bagi Anda dan Anda tidak mengalami masalah yang disebutkan dalam 30 hari terakhir, berilah tanda pada kolom T. Jika Anda tidak yakin tentang jawabannya, berilah jawaban yang paling sesuai di antara Y dan T. Kami tegaskan bahwa, jawaban Anda bersifat rahasia, dan akan digunakan hanya untuk membantu pemecahan masalah Anda.

</p>
        
      </div>
    </section>

    <!-- Question Section (Paginated) -->
    <section v-if="!showResults" class="question-section">
      <div v-if="questions.length === 0 && !error" class="loading-questions">
        <div class="loading-spinner"></div>
        <p>Memuat pertanyaan tes...</p>
      </div>
      
      <div v-else-if="questions.length > 0" class="questions-content">
        <div class="progress-indicator">
          Halaman {{ currentPage + 1 }} dari {{ totalPages }}
          <span class="question-count">
            ({{ questions.length }} pertanyaan tersedia)
          </span>
        </div>

        <QuestionItem
          v-for="(question, index) in paginatedQuestions"
          :key="getGlobalQuestionIndex(index)"
          :question-text="question"
          :question-index="getGlobalQuestionIndex(index)"
          v-model="answers[getGlobalQuestionIndex(index)]"
        />
      </div>
      
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
    <ResultIsNeurosis v-if="showResults" :score="totalScore" />
    <div v-if="error" class="error-message" style="color:red;text-align:center;margin:16px 0;">{{ error }}</div>
    
    <!-- New Questions Notification -->
    <div v-if="showNewQuestionsNotification" class="new-questions-notification">
      <div class="notification-content">
        <span>🎉 Pertanyaan baru telah ditambahkan!</span>
        <button @click="showNewQuestionsNotification = false" class="notification-close">×</button>
      </div>
    </div>
    
    <div v-if="showResults" class="results-section">
      <h2>Hasil Tes Anda:</h2>
      <p>{{ hasilTes }}</p>
      <button class="back-button" @click="resetTest">Ulangi Tes</button>
    </div>
  </div>
</template>

<script>
import QuestionItem from '../components/QuestionItem.vue';
import ResultIsNeurosis from './result/resultIsNeurosis.vue';
import { testState } from '../store/testState';
import axios from 'axios';

export default {
  name: 'TesDiri',
  components: {
    QuestionItem,
    ResultIsNeurosis
  },
  data() {
    return {
      questions: [],
      answers: [],
      showResults: false,
      totalScore: 0,
      currentPage: 0,
      questionsPerPage: 5,
      loading: false,

      error: '',
      hasilTes: '',
      lastQuestionCount: 0,
      showNewQuestionsNotification: false,
      questionCheckInterval: null,
    }
  },
  async mounted() {
    await this.fetchQuestions();
    
    // Optional: Check for new questions every 30 seconds
    this.questionCheckInterval = setInterval(async () => {
      if (!this.showResults && !this.loading) {
        await this.checkForNewQuestions();
      }
    }, 30000); // 30 seconds
  },
  beforeDestroy() {
    // Clean up interval when component is destroyed
    if (this.questionCheckInterval) {
      clearInterval(this.questionCheckInterval);
    }
  },
  async beforeRouteEnter(to, from, next) {
    // This ensures questions are refreshed when navigating to this page
    next();
  },
  async beforeRouteUpdate(to, from, next) {
    // Refresh questions when the route updates
    await this.fetchQuestions();
    next();
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
    async fetchQuestions() {
      try {
        // Add timestamp to prevent caching and ensure latest questions
        const timestamp = new Date().getTime();
        const res = await axios.get(`https://mindcareindependent.com/api/tesdiri_questions.php?t=${timestamp}`, {
          headers: {
            'Cache-Control': 'no-cache',
            'Pragma': 'no-cache'
          }
        });
        
        if (res.data.success) {
          const newQuestions = res.data.questions.map(q => q.pertanyaan);
          
          // Check if there are new questions
          if (this.lastQuestionCount > 0 && newQuestions.length > this.lastQuestionCount) {
            this.showNewQuestionsNotification = true;
            setTimeout(() => {
              this.showNewQuestionsNotification = false;
            }, 5000);
          }
          
          this.questions = newQuestions;
          this.answers = Array(this.questions.length).fill(null);
          this.lastQuestionCount = this.questions.length;
          console.log('Fetched questions:', this.questions.length, 'questions');
        } else {
          this.error = 'Gagal mengambil pertanyaan tes.';
        }
      } catch (e) {
        console.error('Error fetching questions:', e);
        this.error = 'Gagal koneksi ke server.';
      }
    },

    async checkForNewQuestions() {
      try {
        const timestamp = new Date().getTime();
        const res = await axios.get(`https://mindcareindependent.com/api/tesdiri_questions.php?t=${timestamp}`, {
          headers: {
            'Cache-Control': 'no-cache',
            'Pragma': 'no-cache'
          }
        });
        
        if (res.data.success && res.data.questions.length > this.lastQuestionCount) {
          // New questions detected
          this.showNewQuestionsNotification = true;
          setTimeout(() => {
            this.showNewQuestionsNotification = false;
          }, 5000);
        }
      } catch (e) {
        console.error('Error checking for new questions:', e);
      }
    },
    getGlobalQuestionIndex(index) {
      return this.currentPage * this.questionsPerPage + index;
    },
    nextPage() {
      if (!this.isLastPage) {
        this.currentPage++;
      }
    },
    async submitTest() {
      if (!this.allQuestionsAnswered) return;
      const user = localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : null;
      this.loading = true;
      this.error = '';
      try {
        const formData = new FormData();
        if (user && user.id) {
          formData.append('user_id', user.id);
        }
        formData.append('jawaban', JSON.stringify(this.answers));
        console.log('Jawaban dikirim:', this.answers);
        const response = await axios.post('https://mindcareindependent.com/api/submit_tesdiri.php', formData);
        console.log('Response backend:', response.data);
        if (response.data.success) {
          this.hasilTes = response.data.hasil;
          this.showResults = true;
          let routeName = '';
          console.log('Hasil dari backend:', this.hasilTes, response.data.severity);
          if (this.hasilTes === 'Gejala Neurosis') routeName = 'ResultIsNeurosis';
          else if (this.hasilTes === 'Gejala Psikotik') routeName = 'ResultIsPsychotic';
          else if (this.hasilTes === 'Gejala PTSD') routeName = 'ResultIsPTSD';
          else if (this.hasilTes === 'Penggunaan Zat Psikotik') routeName = 'ResultIsPsychotic';
          else if (this.hasilTes === 'Normal') routeName = 'ResultIsNormal';
          console.log('Route name:', routeName);
          if (routeName) {
            testState.testCompleted = true;
            testState.resultType = routeName;
            testState.score = this.hasilTes;
            testState.severity = response.data.severity;
            
            // Update testState.hasilTesTerakhir dan localStorage dengan data terbaru
            const latestData = {
              hasil: this.hasilTes,
              severity: response.data.severity
            };
            
            // Jika user login, fetch data terbaru dari backend
            if (user && user.id) {
              try {
                const formFetch = new FormData();
                formFetch.append('user_id', user.id);
                const res = await axios.post('https://mindcareindependent.com/api/get_last_tesdiri.php', formFetch);
                if (res.data.success && res.data.data) {
                  // Pastikan severity ada dalam data dari backend
                  const backendData = res.data.data;
                  if (!backendData.severity) {
                    backendData.severity = response.data.severity; // Fallback ke severity dari response submit
                  }
                  testState.hasilTesTerakhir = backendData;
                  localStorage.setItem('lastTestResult', JSON.stringify(backendData));
                } else {
                  // Fallback ke data dari response submit
                  testState.hasilTesTerakhir = latestData;
                  localStorage.setItem('lastTestResult', JSON.stringify(latestData));
                }
              } catch (e) {
                console.error('Error fetching latest data:', e);
                // Fallback ke data dari response submit
                testState.hasilTesTerakhir = latestData;
                localStorage.setItem('lastTestResult', JSON.stringify(latestData));
              }
            } else {
              // Anonymous: update langsung dari response submit
              testState.hasilTesTerakhir = latestData;
              localStorage.setItem('lastTestResult', JSON.stringify(latestData));
            }
            
            console.log('Final testState.hasilTesTerakhir:', testState.hasilTesTerakhir);
            console.log('Final localStorage.lastTestResult:', localStorage.getItem('lastTestResult'));
            
            await this.$nextTick();
            this.$router.push({ name: routeName, params: { hasil: this.hasilTes, severity: response.data.severity } });
          } else {
            this.error = 'Tidak dapat menentukan halaman hasil. Hasil: ' + this.hasilTes;
          }
        } else {
          this.error = response.data.message;
        }
      } catch (e) {
        this.error = 'Gagal koneksi ke server: ' + e.message;
      } finally {
        this.loading = false;
      }
    },
    resetTest() {
      this.answers = Array(this.questions.length).fill(null);
      this.totalScore = 0;
      this.showResults = false;
      this.currentPage = 0;
      // Jangan reset testState agar hasil tetap persist
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
  padding: 120px 0 100px 0;
  color: #fff;
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
  font-size: 1.4rem;
  margin-bottom: 18px;
}
.skala-row, .skala {
  display: none !important;
}
.note {
  font-size: 1rem;
  margin-top: 8px;
  line-height: 1.7;
  text-align: justify;
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

.question-count {
  color: #6A4C9B;
  font-weight: 600;
  margin-left: 8px;
}

.loading-questions {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  color: #6A4C9B;
}

.loading-questions .loading-spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #6A4C9B;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

.loading-questions p {
  font-size: 1.1rem;
  font-weight: 600;
  margin: 0;
}

.new-questions-notification {
  position: fixed;
  top: 20px;
  right: 20px;
  background: #4CAF50;
  color: white;
  padding: 16px 20px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  z-index: 1000;
  animation: slideInRight 0.3s ease-out;
}

.notification-content {
  display: flex;
  align-items: center;
  gap: 12px;
}

.notification-close {
  background: none;
  border: none;
  color: white;
  font-size: 1.5rem;
  cursor: pointer;
  padding: 0;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: background-color 0.2s;
}

.notification-close:hover {
  background: rgba(255,255,255,0.2);
}

@keyframes slideInRight {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}
.submission-area {
  max-width: 1000px;
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

.loading-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(255,255,255,0.85);
  z-index: 2000;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}
.loading-spinner {
  width: 48px;
  height: 48px;
  border: 5px solid #e0e0e0;
  border-top: 5px solid #6A4C9B;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 18px;
}
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
.loading-text {
  font-size: 1.1rem;
  color: #6A4C9B;
  font-weight: 600;
}

@media (max-width: 900px) {
  .tes-diri-container {
    padding: 0 4px;
    font-size: 0.92rem;
  }
  .hero {
    min-height: 20px;
    padding-top: 80px;
    padding-bottom: 40px;
  }
  .hero-content {
    font-size: 0.85rem;
    padding: 0 2px;
  }
  .hero-content h1 {
    font-size: 1rem;
    margin-bottom: 6px;
  }
  .hero-content .desc {
    font-size: 0.9rem;
    margin-bottom: 6px;
  }
  .skala-row {
    gap: 19px;
    margin-left: 10px;
    margin-right: 10px;
    margin: 4px 10px;
  }
  .skala {
    font-size: 0.65rem;
    padding: 2px 0;
    border-radius: 4px;
    margin-bottom: 20px;
    margin-top: 20px;

  }
  .skala span {
    font-size: 0.65rem;
  }
  .note {
    font-size: 0.7rem;
    margin-top: 8px;
  }

  .question-section {
    padding: 10px 0 14px 0;
  }
  .progress-indicator {
    font-size: 0.92rem;
    margin-bottom: 10px;
  }
  .submission-area {
    padding: 0 2px;
    margin: 10px auto 0;
  }
  .submit-button {
    padding: 0.6rem 1.2rem;
    font-size: 0.92rem;
    border-radius: 18px;
  }
  .results-section h2 {
    font-size: 1.1rem;
  }
  .results-section .score {
    font-size: 0.95rem;
  }
  .results-section .interpretation {
    font-size: 0.92rem;
    max-width: 98vw;
  }
  .back-button {
    padding: 0.5rem 1.2rem;
    font-size: 0.92rem;
    border-radius: 18px;
  }
  .question-section label,
  .question-section .question-text,
  .question-section .answer-option {
    font-size: 0.85rem !important;
    padding: 2px 0 !important;
    margin-bottom: 2px !important;
  }
  .question-section input[type="radio"],
  .question-section input[type="checkbox"] {
    width: 18px;
    height: 18px;
  }
  
  .new-questions-notification {
    top: 10px;
    right: 10px;
    left: 10px;
    padding: 12px 16px;
  }
  
  .notification-content {
    gap: 8px;
  }
  
  .notification-close {
    font-size: 1.2rem;
    width: 20px;
    height: 20px;
  }
}
</style> 