import { reactive, watch } from 'vue';

const saved = localStorage.getItem('testState');
const initial = saved ? JSON.parse(saved) : {
  testCompleted: false,
  score: 0,
  resultType: null,
  hasilTesTerakhir: null // hasil tes diri terakhir user
};

export const testState = reactive(initial);

watch(testState, (val) => {
  localStorage.setItem('testState', JSON.stringify(val));
}, { deep: true });

// Tambahkan fungsi untuk reset state saat logout
export function resetTestState() {
  testState.testCompleted = false;
  testState.score = 0;
  testState.resultType = null;
  testState.hasilTesTerakhir = null;
} 