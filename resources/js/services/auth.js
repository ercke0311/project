import { api } from "./api";
import { setAccessToken, clearAccessToken } from "../services/tokenStore";

export async function login(payload) {
    const res = await api.post("/api/auth/login", payload);
    setAccessToken(res.data.access_token);
    return res.data;
}

export async function logout() {
    try {
        await api.post("/api/auth/logout");
    } finally {
        clearAccessToken();
    }
}

export async function register(payload) {
    const res = await api.post("/api/auth/register", payload);
    return res.data;
}

export async function refreshToken() {
    const res = await api.post("/api/auth/refresh");
    setAccessToken(res.data.access_token);
    return res.data;
}