import { api } from "./api";

export async function getSocialRedirectUrl(driver) {
    const res = await api.get(`/api/auth/social/${driver}/url`);
    return res.data;
}