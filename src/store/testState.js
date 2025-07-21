import { reactive } from 'vue';

export const testState = reactive({
  testCompleted: false,
  score: 0,
  resultType: null, // e.g., 'ResultIsDepresi', 'ResultIsAnxiety'
}); 