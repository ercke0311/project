<?php

namespace App\Services\Auth\OAuth;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class LineLoginService implements OAuthLoginInterface
{
    private string $clientId;
    private string $clientSecret;
    private string $authorizationUrl;
    private string $apiUrl;
    private string $redirectUri;

    public function __construct(
        private PkceService $pkceService,
    ) {
        $this->clientId = (string) config('services.line.client_id');
        $this->clientSecret = (string) config('services.line.client_secret');
        $this->redirectUri = (string) config('services.line.redirect');
        $this->authorizationUrl = (string) config('services.line.authorization_url');
        $this->apiUrl = (string) config('services.line.api_url');
    }
    
    public function driver(): string
    {
        return 'line';
    }

    /**
     * @return array{redirect_url: string, cookie: ?\Symfony\Component\HttpFoundation\Cookie}
     */
    public function getRedirectUrl(): array
    {
        $pkce = $this->pkceService->generatePkce($this->driver());

        $url =  $this->authorizationUrl.'?'.http_build_query([
                    'response_type' => 'code',
                    'redirect_uri' => $this->redirectUri,
                    'client_id' => $this->clientId,
                    'state' => $pkce['state'],
                    'code_challenge' => $pkce['code_challenge'],
                    'code_challenge_method' => 'S256',
                    'scope' => 'email profile openid',
                ]);

        $cookie = $this->pkceService->setStateWithCookie($this->driver(), $pkce['state']);

        return [
            'redirect_url' => $url,
            'cookie' => $cookie,
        ];
    }

    public function handleCallback(array $context): array
    {
        $codeVerifier = $this->pkceService->authorize($this->driver(),$context);
        $tokenResponse = $this->exchangeAccessToken($context['code'], $codeVerifier);

        $profile = $this->getUserProfile($tokenResponse['id_token']);
        
        return $profile;
    }

    private function exchangeAccessToken(string $code, string $codeVerifier): array
    {
        // Content-Type Required application/x-www-form-urlencoded
        $response = Http::asForm()
            ->post($this->apiUrl.'/oauth2/v2.1/token', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code_verifier' => $codeVerifier,
            ]);

        if (!$response->successful()) {
            Log::error('LINE token exchange failed', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            throw new UnauthorizedHttpException('OAuth', 'LINE token exchange failed');
        }

        return $response->json();
    }

    private function getUserProfile(string $idToken): array
    {
        // Content-Type Required application/x-www-form-urlencoded
        $verifyResponse = Http::asForm()->post($this->apiUrl.'/oauth2/v2.1/verify', [
            'id_token' => $idToken,
            'client_id' => $this->clientId,
        ]);

        if (! $verifyResponse->successful()) {
            Log::error('LINE verify failed', [
                'status' => $verifyResponse->status(),
                'body' => $verifyResponse->json(),
            ]);

            throw new UnauthorizedHttpException('OAuth', 'LINE verify failed');
        }

        $info = $verifyResponse->json();

        return [
            'provider_id' => $info['sub'],
            'name' => $info['name'],
            'picture' => $info['pictureUrl'] ?? null,
            'email' => $info['email'],
        ];
    }
}