<?php

namespace App\Services\Auth\Social;

use App\Models\SocialAccount;
use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Cookie;

class SocialAccountService
{
    public function __construct(private AuthService $authService){}

    public function handleSocialLogin(string $provider, array $profile, array $meta): Cookie
    {
        return DB::transaction(function () use ($provider, $profile, $meta) {
            $social = SocialAccount::where('provider', $provider)
                ->where('provider_id', $profile['provider_id'])
                ->first();

            if ($social) {
                $user = $social->user;
            } else {
                $user = User::firstOrCreate(
                    ['email' => $profile['email']],
                    [
                        'name'  => $profile['name'],
                        'password' => null,
                    ]
                );

                SocialAccount::create([
                    'user_id'     => $user->id,
                    'provider'    => $provider,
                    'provider_id' => $profile['provider_id'],
                    'name'        => $profile['name'],
                    'email'       => $profile['email'],
                ]);
            }

            return $this->authService->createRefreshToken($user->id, $meta);
        });
    }
}