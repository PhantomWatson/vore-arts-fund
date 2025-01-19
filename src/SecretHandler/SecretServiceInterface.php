<?php

namespace App\SecretHandler;

interface SecretServiceInterface
{
    public function getTin($secretId);
    public function setTin($projectId, $tin);
}
