<?php

namespace App\Services\Auth\OAuth;

class GithubLoginService extends AbstractSocialiteLogin implements OAuthLoginInterface
{
    protected $provider = 'github';
}