<?php

namespace App\SecretHandler;

use Cake\Datasource\Exception\RecordNotFoundException;

/**
 * Class for handling personally identifiable information (primarily tax ID numbers)
 */
class SecretHandler
{
    private SecretServiceInterface $piiService;

    public function __construct()
    {
        $this->piiService = new SecretEncrypter();
    }

    /**
     * @param int $projectId
     * @param string $tin
     * @throws \SodiumException
     * @return string|false
     */
    public function setTin($projectId, $tin): string|false
    {
        $retval = $this->piiService->setTin($projectId, $tin);
        sodium_memzero($tin);
        return $retval;
    }

    /**
     * @param int $projectId
     * @param string $secretKey Base64 encoded
     * @throws \Exception
     * @throws RecordNotFoundException
     * @return string
     */
    public function getTin($projectId, $secretKey): string
    {
        return $this->piiService->getTin($projectId, $secretKey);
    }
}
