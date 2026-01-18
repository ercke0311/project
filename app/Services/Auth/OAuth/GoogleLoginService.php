<?php

namespace App\Services\Auth\OAuth;

class GoogleLoginService extends BaseSocialiteLogin implements OAuthLoginInterface
{
    protected $provider = 'google';
}