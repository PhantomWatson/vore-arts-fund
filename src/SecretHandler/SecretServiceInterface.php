<?php

namespace App\SecretHandler;

interface SecretServiceInterface
{
    public function getTin($projectId, $secretKey): string;
    public function setTin($projectId, $tin): string|false;
}
