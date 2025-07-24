<template>
  <div class="question-block questionnaire-row">
    <div class="question-left">
      <span class="question-text">{{ questionText }}</span>
    </div>
    <div class="answer-right">
      <label class="answer answer-ya">
        <input 
          type="radio" 
          :name="'q' + questionIndex" 
          value="1" 
          :checked="modelValue === 1"
          @change="$emit('update:modelValue', 1)" 
        />
        <span>
          <svg v-if="modelValue === 1" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="11" fill="#4DB6AC"/><path d="M7 13l3 3 7-7"/></svg>
          <svg v-else width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#4DB6AC" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="11" fill="#fff"/><path d="M7 13l3 3 7-7"/></svg>
        </span>
      </label>
      <label class="answer answer-tidak">
        <input 
          type="radio" 
          :name="'q' + questionIndex" 
          value="0" 
          :checked="modelValue === 0"
          @change="$emit('update:modelValue', 0)" 
        />
        <span>
          <svg v-if="modelValue === 0" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="11" fill="#e21313"/><path d="M8 8l8 8M16 8l-8 8"/></svg>
          <svg v-else width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#e21313" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="11" fill="#fff"/><path d="M8 8l8 8M16 8l-8 8"/></svg>
        </span>
      </label>
    </div>
  </div>
</template>

<script>
export default {
  name: 'QuestionItem',
  props: {
    questionText: {
      type: String,
      required: true
    },
    questionIndex: {
      type: Number,
      required: true
    },
    modelValue: {
      type: Number,
      default: null
    }
  },
  emits: ['update:modelValue']
}
</script>

<style scoped>
.questionnaire-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 100px;
  max-width: 1000px;
  margin: 0 auto 50px auto;
  padding: 0 12px 18px 12px;
  border-bottom: 1.5px solid #e0e0e0;
}
.questionnaire-row:last-child {
  border-bottom: none;
}
.question-left {
  flex: 1 1 60%;
  text-align: left;
}
.question-text {
  font-size: 1.1rem;
  color: #333;
  font-weight: 500;
}
.answer-right {
  display: flex;
  gap: 80px;
  align-items: center;
}
.answer {
  display: flex;
  align-items: center;
  font-size: 1rem;
  cursor: pointer;
  user-select: none;
}
.answer input[type="radio"] {
  display: none;
}
.answer span {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 44px;
  padding: 0;
  border-radius: 50%;
  border: 2px solid #949494;
  background: #fff;
  color: #ffffff;
  transition: background 0.2s, color 0.2s, border 0.2s, box-shadow 0.2s;
}
.answer-ya input[type="radio"]:checked + span {
  background: #4db65f;
  color: #fff;
  border-color: #4db670;
  box-shadow: 0 2px 8px rgba(77,182,172,0.13);
}
.answer-tidak input[type="radio"]:checked + span {
  background: #e21313;
  color: #fff;
  border-color: #e21313;
  box-shadow: 0 2px 8px rgba(226,19,19,0.13);
}
.answer span:hover {
  background: #f3f0fa;
  color: #6a4c9b;
}
@media (max-width: 900px) {
  .questionnaire-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
    padding: 0 2px;
    margin-bottom: 14px;
  }
  .question-left {
    width: 100%;
    margin-bottom: 2px;
  }
  .question-text {
    font-size: 0.92rem;
  }
  .answer-right {
    gap: 8px;
  }
  .answer span {
    width: 28px;
    height: 28px;
    min-width: 28px;
    padding: 0;
  }
}
</style> 