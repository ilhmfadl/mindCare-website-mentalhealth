<template>
  <div class="question-block">
    <h2>{{ questionText }}</h2>
    <div class="answer-row">
      <label v-for="n in 5" :key="n" :class="['answer', 'answer-' + n]">
        <input 
          type="radio" 
          :name="'q' + questionIndex" 
          :value="n" 
          :checked="modelValue === n"
          @change="$emit('update:modelValue', n)" 
        />
        <span>{{ n }}</span>
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
.question-block {
  max-width: 940px;
  margin: 0 auto 32px auto;
  padding: 0 24px;
}
.question-block h2 {
  font-size: 1.3rem;
  font-weight: 500;
  margin-bottom: 18px;
  color: #333;
}
.answer-row {
  display: flex;
  gap: 168px; /* Was 32px, reduced for a more compact look */
  justify-content: flex-start;
  margin-bottom: 16px;
}
.answer {
  display: flex;
  flex-direction: column;
  align-items: center;
  font-size: 1.1rem;
  cursor: pointer;
}
.answer input[type="radio"] {
  display: none;
}
.answer span {
  display: inline-block;
  width: 44px;
  height: 44px;
  line-height: 44px;
  border-radius: 50%;
  border: 2px solid; /* Set border style, color will be inherited */
  text-align: center;
  font-size: 1.3rem;
  font-weight: 600;
  margin-bottom: 4px;
  transition: background-color 0.2s, transform 0.2s;
}

/* Base scaling from edges to center */
.answer-1 span,
.answer-5 span {
  transform: scale(1.15);
}
.answer-2 span,
.answer-4 span {
  transform: scale(1.0);
}
.answer-3 span {
  transform: scale(0.85);
}

/* Hover effect for each answer circle, scaling up from their base size */
.answer-1:hover span,
.answer-5:hover span {
  transform: scale(1.25);
}
.answer-2:hover span,
.answer-4:hover span {
  transform: scale(1.1);
}
.answer-3:hover span {
  transform: scale(1.0);
}

/* Matching colors from the legend in tes_diri.vue */
.answer-1 span { color: #e21313; border-color: #e21313; }
.answer-2 span { color: #f76c10; border-color: #f76c10; }
.answer-3 span { color: #f8bc3a; border-color: #f8bc3a; } /* Dark text for yellow */
.answer-4 span { color: #98db9c; border-color: #98db9c; }
.answer-5 span { color: #4DB6AC; border-color: #4DB6AC; }

/* --- FIX: Specific rules for checked state --- */
.answer input[type="radio"]:checked + span {
  color: #fff !important; /* Ensure number is always white when selected */
}

.answer-1 input[type="radio"]:checked + span { background-color: #e21313; }
.answer-2 input[type="radio"]:checked + span { background-color: #f76c10; }
.answer-3 input[type="radio"]:checked + span { background-color: #f8bc3a; }
.answer-4 input[type="radio"]:checked + span { background-color: #98db9c; }
.answer-5 input[type="radio"]:checked + span { background-color: #4DB6AC; }

@media (max-width: 900px) {
  .question-block {
    padding: 0 2px;
    margin-bottom: 16px;
  }
  .question-block h2 {
    font-size: 0.92rem;
    margin-bottom: 8px;
  }
  .answer-row {
    gap: 53px;
    margin-bottom: 8px;
    margin-left: 10px;
    padding: 2px 0;
  }
  .answer {
    font-size: 0.8rem;
    margin-bottom: 6px;
  }
  .answer span {
    width: 28px;
    height: 28px;
    line-height: 28px;
    font-size: 0.8rem;
    margin-bottom: 2px;
  }
}
</style> 