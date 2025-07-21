<template>
  <div v-if="visible" class="modal-overlay">
    <div class="modal-content">
      <h3 class="modal-title">{{ title }}</h3>
      <p class="modal-message">{{ message }}</p>
      <div class="modal-actions">
        <button @click="onCancel" class="btn-cancel">Batal</button>
        <button @click="onConfirm" class="btn-confirm">Yakin</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ConfirmationModal',
  props: {
    visible: {
      type: Boolean,
      required: true,
    },
    title: {
      type: String,
      default: 'Konfirmasi',
    },
    message: {
      type: String,
      required: true,
    },
  },
  emits: ['confirm', 'cancel'],
  methods: {
    onConfirm() {
      this.$emit('confirm');
    },
    onCancel() {
      this.$emit('cancel');
    },
  },
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 2000;
  animation: fade-in 0.2s ease;
}
.modal-content {
  background: white;
  padding: 24px 32px;
  border-radius: 16px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.2);
  width: 90%;
  max-width: 450px;
  text-align: center;
  animation: slide-down 0.3s ease-out;
}
@keyframes fade-in {
  from { opacity: 0; }
  to { opacity: 1; }
}
@keyframes slide-down {
  from { transform: translateY(-30px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}
.modal-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #333;
  margin: 0 0 12px;
}
.modal-message {
  font-size: 1rem;
  color: #555;
  line-height: 1.6;
  margin-bottom: 24px;
}
.modal-actions {
  display: flex;
  justify-content: center;
  gap: 16px;
}
.modal-actions button {
  padding: 10px 24px;
  border-radius: 50px;
  font-weight: 600;
  cursor: pointer;
  border: none;
  font-size: 1rem;
  transition: all 0.3s ease;
}
.btn-cancel {
  background-color: transparent;
  border: 2px solid #ccc;
  color: #555;
}
.btn-cancel:hover {
  background-color: #f0f0f0;
}
.btn-confirm {
  background-color: #e74c3c;
  color: white;
}
.btn-confirm:hover {
  background-color: #c0392b;
}
</style> 