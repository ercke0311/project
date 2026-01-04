<template>
  <div style="max-width: 600px; margin: 40px auto;">
    <h2>Home</h2>
    <p>登入成功 🎉</p>
    <button @click="handleLogout">登出</button>
    <button @click="me">測試 /me API</button>
    <button @click="doRefresh" :disabled="loadingRefresh">測試 refresh</button>

    <pre v-if="user">{{ user }}</pre>

    <div v-if="refreshData" style="margin-top:12px;">
      <h3>/refresh 回傳</h3>
      <pre>{{ refreshData }}</pre>
    </div>
  </div>
</template>

<script setup>
    import { ref } from "vue";
    import { useRouter } from "vue-router";
    import { logout, refreshToken } from "../services/auth";
    import { api } from "../services/api";

    const user = ref(null);

    const me = async () => {
        const res = await api.get("/api/auth/me");
        user.value = res.data;
    };

    const router = useRouter();

    const handleLogout = async () => {
        await logout();
        router.push("/login");
    };

    const refreshData = ref(null);
    const loadingRefresh = ref(false);
    const error = ref("");

    const doRefresh = async () => {
            error.value = "";
            loadingRefresh.value = true;
        try {
            const data = await refreshToken();
            refreshData.value = data;
        } catch (e) {
            error.value = e?.response?.data?.message || "refresh 失敗";
        } finally {
            loadingRefresh.value = false;
        }
    };
</script>