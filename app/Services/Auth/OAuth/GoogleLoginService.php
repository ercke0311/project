<?php

namespace App\Services\Auth\OAuth;

class GoogleLoginService extends AbstractSocialiteLogin implements OAuthLoginInterface
{
    protected $provider = 'google';
}