<?php

namespace App\SecretHandler;

use App\Alert\Alert;
use Cake\Core\Configure;
use Cake\Http\Exception\InternalErrorException;
use Cake\ORM\TableRegistry;

/**
 * Class for interfacing with the third-party Piiano secret-storage service
 */
class Piiano implements SecretServiceInterface
{
    private string $environment;

    public function __construct()
    {
        require_once(ROOT . DS . 'config' . DS . 'environment.php');
        $this->environment = getEnvironment();
    }

    private function getMode()
    {
        return Configure::read('Piiano.mode');
    }

    /**
     * Returns the new Piiano object ID on success, or FALSE on failure
     *
     * Also sends a Slack message on both success and failure
     *
     * @param int $projectId
     * @param string $tin
     * @return string|false
     */
    public function setTin($projectId, $tin): string|false
    {
        // Send request
        $ch = curl_init($this->getApiUrlBase() . '?' . $this->getReason());
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->getApiKey()
            ]
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->getPostPayload($projectId, $tin)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $curlResult = curl_exec($ch);
        curl_close($ch);

        // Send alert
        $success = $curlResult && (json_decode($curlResult)->id ?? null);
        $alertSafePayload = $this->getPostPayload($projectId, '(TIN redacted)');
        $alert = new Alert();
        $alert->addLine($success ? 'Saved TIN' : 'Failure to save TIN');
        $alert->addLine('Payload:');
        $alert->addLine('```' . print_r($alertSafePayload, true) . '```');
        $alert->addLine('cURL result:');
        $alert->addLine('```' . print_r($curlResult, true) . '```');
        $alert->send(Alert::TYPE_APPLICATIONS);

        return $success ? json_decode($curlResult)->id : false;
    }

    /**
     * We're only using write-only access. Retrieving TINs will require logging in to Piiano and using their interface.
     *
     * @param string $secretId
     * @return string|false
     */
    public function getTin($secretId): string|false
    {
        return false;
    }

    private function getApiUrlBase(): string
    {
        $piianoEnv = $this->getMode();
        $url = Configure::read('Piiano.' . $piianoEnv . '.endpoint');
        if (!$url) {
            throw new InternalErrorException('Piiano endpoint not set');
        }
        $collectionName = 'borrowers';
        $url .= "/api/pvlt/1.0/data/collections/$collectionName/objects";
        return $url;
    }

    private function getReason(): string
    {
        return 'reason=' . ($this->environment == 'production' ? 'AppFunctionality' : 'Maintenance');
    }


    private function getApiKey()
    {
        $piianoEnv = $this->getMode();
        $apiKey = Configure::read('Piiano.' . $piianoEnv . '.apiKey');
        if (!$apiKey) {
            throw new InternalErrorException('Piiano API key not set');
        }
        return $apiKey;
    }

    private function getPostPayload($projectId, $tin): array
    {
        $projectsTable = TableRegistry::getTableLocator()->get('Projects');
        $project = $projectsTable->get($projectId);

        return [
            'project_id' => $projectId,
            'environment' => $this->environment,
            'tin' => $tin,
            'name' => $project->check_name,
            'address' => $project->address . ' Muncie, IN',
            'zipcode' => $project->zipcode,
        ];
    }
}
