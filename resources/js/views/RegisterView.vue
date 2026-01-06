<template>
  <div class="auth-page">
    <div class="auth-card">
      <div class="auth-header">
        <div class="brand">
          <div>
            <h2>建立帳號</h2>
          </div>
        </div>
      </div>

      <form class="auth-form" @submit.prevent="submit">
        <div class="field">
          <label>姓名</label>
          <div class="input-wrap">
            <span class="icon">👤</span>
            <input
              v-model.trim="form.name"
              type="text"
              required
              :disabled="loading"
              placeholder="名稱"
            />
          </div>
        </div>

        <div class="field">
          <label>Email</label>
          <div class="input-wrap">
            <span class="icon">✉️</span>
            <input
              v-model.trim="form.email"
              type="email"
              required
              :disabled="loading"
              placeholder="you@example.com"
            />
          </div>
        </div>

        <div class="field">
          <label>Password</label>
          <div class="input-wrap">
            <span class="icon">🔒</span>
            <input
              v-model="form.password"
              type="password"
              required
              :disabled="loading"
            />
          </div>
        </div>

        <div class="field">
          <label>確認密碼</label>
          <div class="input-wrap">
            <span class="icon">🔁</span>
            <input
              v-model="form.password_confirmation"
              type="password"
              required
              :disabled="loading"
              placeholder="再輸入一次"
            />
          </div>
        </div>

        <button class="btn" :disabled="loading">
          <span class="btn-text">{{ loading ? "註冊中..." : "註冊" }}</span>
          <span class="btn-arrow">→</span>
        </button>

        <div v-if="error" class="alert">
          <span class="alert-dot"></span>
          <span>{{ error }}</span>
        </div>
      </form>

      <div class="auth-footer">
        已經有帳號？
        <router-link to="/login">回登入</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { register } from "../services/auth";

const router = useRouter();
const loading = ref(false);
const error = ref("");

const form = reactive({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const submit = async () => {
    error.value = "";
    loading.value = true;

    try {
        await register(form);
        router.push("/login");
    } catch (e) {
        error.value = e?.response?.data?.errors ||
            "註冊失敗，請確認資料是否正確";
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
/* ===== Theme ===== */
.auth-page {
  min-height: 100vh;
  display: grid;
  place-items: center;
  padding: 48px 16px;
  background:
    radial-gradient(1200px 600px at 15% 10%, rgba(99,102,241,.25), transparent 60%),
    radial-gradient(900px 500px at 85% 20%, rgba(34,197,94,.18), transparent 55%),
    radial-gradient(1000px 700px at 60% 90%, rgba(244,114,182,.18), transparent 60%),
    linear-gradient(180deg, #0f0a1f, #070412);
  color: #e5e7eb;
  font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Noto Sans TC", Arial;
}

/* ===== Card ===== */
.auth-card {
  width: 100%;
  max-width: 460px;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.10);
  border-radius: 18px;
  backdrop-filter: blur(14px);
  box-shadow:
    0 30px 80px rgba(0,0,0,0.55),
    0 2px 0 rgba(255,255,255,0.06) inset;
  padding: 26px 22px 20px;
}

.auth-header {
  margin-bottom: 14px;
}

.brand {
  display: flex;
  gap: 12px;
  align-items: center;
}

.logo {
  width: 44px;
  height: 44px;
  border-radius: 14px;
  display: grid;
  place-items: center;
  font-weight: 800;
  letter-spacing: .5px;
  background: linear-gradient(135deg, #6366f1, #22c55e);
  box-shadow: 0 10px 25px rgba(99,102,241,.28);
  color: #0b1020;
}

.brand h2 {
  margin: 0;
  font-size: 20px;
  font-weight: 700;
  color: #f9fafb;
}

.brand p {
  margin: 4px 0 0;
  font-size: 13px;
  color: rgba(229,231,235,0.70);
}

/* ===== Form ===== */
.auth-form {
  margin-top: 14px;
}

.field {
  margin: 14px 0;
}

.field label {
  display: block;
  margin-bottom: 8px;
  font-size: 13px;
  color: rgba(229,231,235,0.78);
}

.input-wrap {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 12px;
  border-radius: 14px;
  border: 1px solid rgba(255,255,255,0.10);
  background: rgba(17,24,39,0.35);
  transition: border-color .18s, box-shadow .18s, transform .18s;
}

.icon {
  width: 28px;
  height: 28px;
  display: grid;
  place-items: center;
  border-radius: 10px;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.08);
  font-size: 14px;
}

.input-wrap input {
  width: 100%;
  border: none;
  outline: none;
  background: transparent;
  color: #f9fafb;
  font-size: 14px;
}

.input-wrap input::placeholder {
  color: rgba(229,231,235,0.35);
}

.input-wrap:focus-within {
  border-color: rgba(99,102,241,0.55);
  box-shadow: 0 0 0 4px rgba(99,102,241,0.16);
  transform: translateY(-1px);
}

.input-wrap input:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

/* ===== Button ===== */
.btn {
  width: 100%;
  margin-top: 10px;
  padding: 12px 14px;
  border: none;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  cursor: pointer;
  color: #0b1020;
  font-weight: 800;
  letter-spacing: .2px;
  background: linear-gradient(135deg, #6366f1, #22c55e);
  box-shadow:
    0 16px 35px rgba(99,102,241,0.22),
    0 10px 22px rgba(34,197,94,0.16);
  transition: transform .18s, box-shadow .18s, filter .18s, opacity .18s;
}

.btn:hover:not(:disabled) {
  transform: translateY(-1px);
  filter: brightness(1.05);
  box-shadow:
    0 22px 55px rgba(99,102,241,0.28),
    0 14px 30px rgba(34,197,94,0.18);
}

.btn:active:not(:disabled) {
  transform: translateY(0px);
}

.btn:disabled {
  opacity: 0.55;
  cursor: not-allowed;
}

.btn-arrow {
  font-size: 16px;
  opacity: 0.95;
}

/* ===== Error Alert ===== */
.alert {
  margin-top: 14px;
  padding: 12px 12px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  gap: 10px;
  background: rgba(220,38,38,0.12);
  border: 1px solid rgba(220,38,38,0.25);
  color: rgba(254,226,226,0.95);
  font-size: 13px;
}

.alert-dot {
  width: 10px;
  height: 10px;
  border-radius: 999px;
  background: #ef4444;
  box-shadow: 0 0 0 4px rgba(239,68,68,0.18);
}

/* ===== Footer ===== */
.auth-footer {
  margin-top: 18px;
  text-align: center;
  font-size: 13px;
  color: rgba(229,231,235,0.65);
}

.auth-footer a {
  color: #a5b4fc;
  text-decoration: none;
  font-weight: 700;
}

.auth-footer a:hover {
  text-decoration: underline;
}

/* ===== Hint ===== */
.auth-hint {
  margin-top: 14px;
  max-width: 460px;
  text-align: center;
  font-size: 12px;
  color: rgba(229,231,235,0.45);
}
</style>