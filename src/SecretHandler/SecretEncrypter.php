<?php

namespace App\SecretHandler;

use App\Alert\Alert;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\InternalErrorException;
use Cake\ORM\TableRegistry;

/**
 * Class for encrypting and decrypting tax ID numbers
 */
class SecretEncrypter implements SecretServiceInterface
{

    public function __construct()
    {

    }

    /**
     * Returns the new Piiano object ID on success, or FALSE on failure
     *
     * Also sends a Slack message on both success and failure
     *
     * @param int $projectId
     * @param string $tin
     * @return string|false
     * @throws \SodiumException
     */
    public function setTin($projectId, $tin): string|false
    {
        // Encrypt TIN
        $encrypted = sodium_crypto_box_seal($tin, $this->getPublicKeyBin());
        $encryptedBase64 = sodium_bin2base64($encrypted, SODIUM_BASE64_VARIANT_ORIGINAL);

        // Securely erase $tin
        sodium_memzero($tin);

        // Save TIN
        $projectsTable = TableRegistry::getTableLocator()->get('Projects');
        $project = $projectsTable->get($projectId);
        $project->tin = $encryptedBase64;
        return (bool)$projectsTable->save($project);
    }

    /**
     * Return the decrypted TIN or throw an exception with the reason why we can't
     *
     * @param string $projectId
     * @param string $secretKey Base64 encoded
     * @return string
     * @throws \Exception
     * @throws RecordNotFoundException
     */
    public function getTin($projectId, $secretKey): string
    {
        $secretKeyBin = sodium_base642bin($secretKey, SODIUM_BASE64_VARIANT_ORIGINAL);
        $publicKeyBin = $this->getPublicKeyBin();
        $reconstructedKeypair = sodium_crypto_box_keypair_from_secretkey_and_publickey($secretKeyBin, $publicKeyBin);

        // Fetch encrypted TIN
        $projectsTable = TableRegistry::getTableLocator()->get('Projects');
        $project = $projectsTable->get($projectId);
        $encryptedTin = $project->tin;
        if (!$encryptedTin) {
            throw new \Exception('This project does not have a tax ID number saved');
        }

        // Decrypt
        $encryptedTinBin = sodium_base642bin($encryptedTin, SODIUM_BASE64_VARIANT_ORIGINAL);
        $decrypted = sodium_crypto_box_seal_open($encryptedTinBin, $reconstructedKeypair);

        // Securely erase secrets
        sodium_memzero($secretKey);
        sodium_memzero($reconstructedKeypair);

        if (!$decrypted) {
            throw new \Exception('There was an error decrypting that tax ID number');
        }

        return $decrypted;
    }

    /**
     * @throws \SodiumException
     */
    private function getPublicKeyBin()
    {
        $publicKey = Configure::read('tinEncryptionPublicKey');
        return sodium_base642bin($publicKey, SODIUM_BASE64_VARIANT_ORIGINAL);
    }
}
