<template>
  <div class="auth-page">
    <div class="auth-card">
      <div class="auth-header">
        <div class="brand">
          <div>
            <h2>歡迎回來</h2>
            <p>登入以繼續使用系統</p>
          </div>
        </div>
      </div>

      <form class="auth-form" @submit.prevent="submit">
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
              placeholder="請輸入密碼"
            />
          </div>
        </div>

        <button class="btn" :disabled="loading">
          <span>{{ loading ? "登入中..." : "登入" }}</span>
          <span class="arrow">→</span>
        </button>

        <div v-if="error" class="alert">
          <span class="dot"></span>
          <span>{{ error }}</span>
        </div>
      </form>

      <div class="auth-footer">
        還沒有帳號？
        <router-link to="/register">前往註冊</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
    import { useRouter } from "vue-router";
    import { reactive, ref } from "vue";
    import { login } from "../services/auth";

    const loading = ref(false);
    const error = ref("");
    const ok = ref(false);

    const form = reactive({
        email: "",
        password: "",
    });

    const router = useRouter();

    const submit = async () => {
        error.value = "";
        loading.value = true;

        try {
            await login(form);
            router.push("/");
        } catch (e) {
            console.error("login error:", e);
            error.value = e?.response?.data?.message || "登入失敗";
        } finally {
            loading.value = false;
        }
    };
</script>

<style scoped>
.auth-page {
  min-height: 100vh;
  display: grid;
  place-items: center;
  padding: 48px 16px;
  background:
    radial-gradient(1200px 600px at 10% 10%, rgba(99,102,241,.28), transparent 60%),
    radial-gradient(900px 500px at 90% 20%, rgba(34,197,94,.18), transparent 55%),
    linear-gradient(180deg, #0f0a1f, #070412);
  font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Noto Sans TC";
}

.auth-card {
  width: 100%;
  max-width: 440px;
  padding: 28px 26px 24px;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.10);
  border-radius: 18px;
  backdrop-filter: blur(14px);
  box-shadow:
    0 30px 80px rgba(0,0,0,0.55),
    0 2px 0 rgba(255,255,255,0.06) inset;
  color: #e5e7eb;
}

/* Header */
.brand {
  display: flex;
  gap: 12px;
  align-items: center;
  margin-bottom: 18px;
}

.logo {
  width: 44px;
  height: 44px;
  border-radius: 14px;
  display: grid;
  place-items: center;
  font-weight: 900;
  background: linear-gradient(135deg, #6366f1, #22c55e);
  color: #0b1020;
  box-shadow: 0 10px 25px rgba(99,102,241,.3);
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
  color: rgba(229,231,235,.7);
}

/* Form */
.field {
  margin: 14px 0;
}

.field label {
  display: block;
  margin-bottom: 8px;
  font-size: 13px;
  color: rgba(229,231,235,.8);
}

.input-wrap {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px;
  border-radius: 14px;
  background: rgba(17,24,39,.35);
  border: 1px solid rgba(255,255,255,.1);
  transition: border-color .2s, box-shadow .2s, transform .15s;
}

.input-wrap:focus-within {
  border-color: rgba(99,102,241,.55);
  box-shadow: 0 0 0 4px rgba(99,102,241,.18);
  transform: translateY(-1px);
}

.input-wrap input {
  width: 100%;
  background: transparent;
  border: none;
  outline: none;
  color: #f9fafb;
  font-size: 14px;
}

.input-wrap input::placeholder {
  color: rgba(229,231,235,.35);
}

.icon {
  width: 28px;
  height: 28px;
  border-radius: 10px;
  display: grid;
  place-items: center;
  background: rgba(255,255,255,.06);
  border: 1px solid rgba(255,255,255,.08);
  font-size: 14px;
}

/* Button */
.btn {
  margin-top: 10px;
  width: 100%;
  padding: 12px;
  border: none;
  border-radius: 14px;
  font-weight: 800;
  color: #0b1020;
  cursor: pointer;
  background: linear-gradient(135deg, #6366f1, #22c55e);
  box-shadow:
    0 16px 35px rgba(99,102,241,.22),
    0 10px 22px rgba(34,197,94,.16);
  display: flex;
  justify-content: center;
  gap: 8px;
  transition: transform .15s, box-shadow .15s, opacity .15s;
}

.btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow:
    0 22px 55px rgba(99,102,241,.28),
    0 14px 30px rgba(34,197,94,.18);
}

.btn:disabled {
  opacity: .55;
  cursor: not-allowed;
}

/* Error */
.alert {
  margin-top: 14px;
  padding: 12px;
  border-radius: 14px;
  display: flex;
  gap: 10px;
  align-items: center;
  background: rgba(220,38,38,.12);
  border: 1px solid rgba(220,38,38,.25);
  color: #fee2e2;
  font-size: 13px;
}

.dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: #ef4444;
  box-shadow: 0 0 0 4px rgba(239,68,68,.2);
}

/* Footer */
.auth-footer {
  margin-top: 18px;
  text-align: center;
  font-size: 13px;
  color: rgba(229,231,235,.65);
}

.auth-footer a {
  color: #a5b4fc;
  font-weight: 700;
  text-decoration: none;
}

.auth-footer a:hover {
  text-decoration: underline;
}
</style>