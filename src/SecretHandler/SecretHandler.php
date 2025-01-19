<?php

namespace App\SecretHandler;

/**
 * Class for handling personally identifiable information (primarily tax ID numbers)
 */
class SecretHandler
{
    private SecretServiceInterface $piiService;

    public function __construct()
    {
        $this->piiService = new Piiano();
    }

    public function setTin($projectId, $tin): string|false
    {
        return $this->piiService->setTin($projectId, $tin);
    }

    public function getTin($secretId)
    {
        return $this->piiService->getTin($secretId);
    }
}
