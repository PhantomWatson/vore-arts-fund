<?php
namespace App\Alert;

use Cake\Core\Configure;
use Cake\Log\Log;

class Slack
{
    /**
     * @var int Rough max length for a message
     */
    const MAX_LENGTH = 39000;
    public $content;
    public $curlResult;
    private $channel;
    private $url;
    public $username = 'Automated Alerts';

    public function __construct($alertType)
    {
        $this->channel = $this->getSlackChannel($alertType);
        $this->url = Configure::read('slackWebhookUrls.' . $this->channel);
    }

    /**
     * If this isn't the production environment, declares the environment at the top of the message
     *
     * @return void
     */
    public function prependEnvironmentToMessage(): void
    {
        require_once(ROOT . DS . 'config' . DS . 'environment.php');
        $environment = getEnvironment();

        if ($environment == 'production') {
            return;
        }

        $this->content = "*($environment environment)*\n" . $this->content;
    }

    /**
     * @param $alertType
     * @return string
     */
    public function getSlackChannel($alertType)
    {
        $slackChannel = match ($alertType) {
            Alert::TYPE_APPLICATIONS => '#applications',
            Alert::TYPE_TRANSACTIONS => '#transactions',
            default => false
        };
        if ($slackChannel) {
            return $slackChannel;
        }

        $this->addLine('Unknown alert channel: ' . $alertType);
        return '#errors';
    }

    /**
     * Adds $line and a newline to the message being built
     *
     * @param string $line Line of text to add
     * @return void
     */
    public function addLine($line)
    {
        $this->content .= $line . "\n";
    }

    /**
     * Transforms special characters in the provided message to make them Slack-friendly
     *
     * @return string
     */
    public static function encode(string $content): string
    {
        return str_replace(
            ['&', '<', '>'],
            [
                urlencode('&amp;'),
                urlencode('&lt;'),
                urlencode('&gt;')
            ],
            $content
        );
    }

    private function beforeSend()
    {
        $this->prependEnvironmentToMessage();
    }

    /**
     * Sends a message to Slack using the Slack Poster app
     *
     * @return bool
     */
    public function send()
    {
        $this->beforeSend();

        $data = 'payload=' . json_encode(['text' => $this->content]);
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $this->curlResult = curl_exec($ch);
        curl_close($ch);

        return $this->curlResult == 'ok';
    }
}
