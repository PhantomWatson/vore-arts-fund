<?php

namespace App\SecretHandler;

interface SecretServiceInterface
{
    public function getTin($secretId): string|false;
    public function setTin($projectId, $tin): string|false;
}
