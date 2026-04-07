<?php

namespace App\SecretHandler;

interface SecretServiceInterface
{
    public function getTin($projectId, $secretKey): string|false;
    public function setTin($projectId, $tin): string|false;
}
