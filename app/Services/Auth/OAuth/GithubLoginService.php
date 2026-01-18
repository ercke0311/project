<?php

namespace App\Services\Auth\OAuth;

class GithubLoginService extends BaseSocialiteLogin implements OAuthLoginInterface
{
    protected $provider = 'github';
}