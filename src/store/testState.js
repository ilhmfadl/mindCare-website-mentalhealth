import { reactive, watch, computed } from 'vue';

const saved = localStorage.getItem('testState');
const initial = saved ? JSON.parse(saved) : {
  testCompleted: false,
  score: 0,
  resultType: null,
  hasilTesTerakhir: null, // hasil tes diri terakhir user
  lastTestDate: null, // tanggal tes terakhir
  userId: null // ID user yang melakukan tes
};

export const testState = reactive(initial);

watch(testState, (val) => {
  localStorage.setItem('testState', JSON.stringify(val));
}, { deep: true });

// Computed property untuk mengecek apakah user sudah melakukan tes
export const hasCompletedTest = computed(() => {
  return testState.testCompleted && testState.hasilTesTerakhir !== null;
});

// Fungsi untuk menyimpan hasil tes
export function saveTestResult(result, userId) {
  testState.testCompleted = true;
  testState.hasilTesTerakhir = result;
  testState.lastTestDate = new Date().toISOString();
  testState.userId = userId;
}

// Fungsi untuk reset state saat logout atau mulai tes baru
export function resetTestState() {
  testState.testCompleted = false;
  testState.score = 0;
  testState.resultType = null;
  testState.hasilTesTerakhir = null;
  testState.lastTestDate = null;
  testState.userId = null;
}

// Fungsi baru untuk reset hanya data tes yang sedang berlangsung
// tanpa menghapus data tes yang sudah selesai
export function resetCurrentTestOnly() {
  testState.score = 0;
  testState.resultType = null;
  // TIDAK reset testCompleted, hasilTesTerakhir, lastTestDate, dan userId
  // agar menu "Hasil Tes Diri" tetap ditampilkan
}

// Fungsi untuk mengecek apakah user yang sedang login sudah melakukan tes
export function checkUserTestStatus(currentUserId) {
  if (!currentUserId || testState.userId !== currentUserId) {
    // Jika user berbeda, reset state
    resetTestState();
    return false;
  }
  return hasCompletedTest.value;
} 