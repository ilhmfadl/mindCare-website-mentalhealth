import { reactive, watch } from 'vue';

const saved = localStorage.getItem('testState');
const initial = saved ? JSON.parse(saved) : {
  testCompleted: false,
  score: 0,
  resultType: null,
};

export const testState = reactive(initial);

watch(testState, (val) => {
  localStorage.setItem('testState', JSON.stringify(val));
}, { deep: true }); 