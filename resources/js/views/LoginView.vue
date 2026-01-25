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

        <div class="social-buttons">
          <button
            type="button"
            class="social-btn google"
            @click.prevent="socialLogin('google')"
            aria-label="Google 登入"
          >
            <svg viewBox="0 0 48 48" aria-hidden="true">
              <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.6 32.7 29.3 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.2 6.1 29.4 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.3-.1-2.7-.4-3.5z"/>
              <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 16 19 12 24 12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.2 6.1 29.4 4 24 4 16.1 4 9.3 8.5 6.3 14.7z"/>
              <path fill="#4CAF50" d="M24 44c5.1 0 9.8-1.9 13.4-5.1l-6.2-5.1C29.4 35.3 26.8 36 24 36c-5.3 0-9.7-3.3-11.3-8l-6.6 5.1C9.2 39.5 16 44 24 44z"/>
              <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-1.2 3.3-4.1 5.8-8.1 5.8-5.3 0-9.7-3.3-11.3-8l-6.6 5.1C9.2 39.5 16 44 24 44c11.1 0 20-8.9 20-20 0-1.3-.1-2.7-.4-3.5z"/>
            </svg>
          </button>

          <button
            type="button"
            class="social-btn github"
            @click.prevent="socialLogin('github')"
            aria-label="GitHub 登入"
          >
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path fill="currentColor" d="M12 .5C5.7.5.5 5.7.5 12a11.5 11.5 0 0 0 7.9 11c.6.1.8-.2.8-.6v-2c-3.2.7-3.9-1.4-3.9-1.4-.5-1.3-1.3-1.6-1.3-1.6-1-.7.1-.7.1-.7 1.1.1 1.7 1.1 1.7 1.1 1 .1.8 1.9 2.6 2.3.1-.8.4-1.2.7-1.5-2.6-.3-5.4-1.3-5.4-5.7 0-1.3.5-2.3 1.2-3.2-.1-.3-.5-1.4.1-2.9 0 0 1-.3 3.2 1.2.9-.3 1.9-.4 2.9-.4s2 .1 2.9.4c2.2-1.5 3.2-1.2 3.2-1.2.6 1.5.2 2.6.1 2.9.8.9 1.2 1.9 1.2 3.2 0 4.4-2.8 5.4-5.4 5.7.4.3.8 1 .8 2.1v3.1c0 .4.2.7.8.6A11.5 11.5 0 0 0 23.5 12C23.5 5.7 18.3.5 12 .5z"/>
            </svg>
          </button>

          <button
            type="button"
            class="social-btn line"
            @click.prevent="socialLogin('line')"
            aria-label="LINE 登入"
          >
            <img :src="lineLogo" alt="LINE" />
          </button>
        </div>

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
    import { getSocialRedirectUrl } from "../services/socialAuth";
    import lineLogo from "../assets/line_login_button.png";

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

    const socialLogin = async (provider) => {
        try {
            const res = await getSocialRedirectUrl(provider);
            window.location.href = res.redirect_url;
        } catch (e) {
            alert(`${provider.toUpperCase()} 登入失敗`);
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

.social-buttons {
  margin-top: 14px;
  display: flex;
  gap: 12px;
  justify-content: center;
}

.social-btn {
  width: 56px;
  height: 56px;
  border-radius: 16px;
  border: 1px solid rgba(255,255,255,.12);
  background: rgba(15,23,42,.55);
  display: grid;
  place-items: center;
  cursor: pointer;
  transition: transform .15s, box-shadow .15s, border-color .15s;
}

.social-btn svg {
  width: 28px;
  height: 28px;
}

.social-btn img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  border-radius: 12px;
}

.social-btn.google {
  background: rgba(255,255,255,.9);
  border-color: rgba(255,255,255,.85);
}

.social-btn.github {
  color: #e5e7eb;
}

.social-btn.line {
  background: rgba(0,195,0,.16);
  border-color: rgba(0,195,0,.4);
}

.social-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 10px 25px rgba(0,0,0,.35);
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
