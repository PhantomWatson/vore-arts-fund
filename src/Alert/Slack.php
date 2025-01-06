<?php
namespace App\Alert;

use Cake\Core\Configure;

class Slack
{
    /**
     * @var int Rough max length for a message
     */
    const MAX_LENGTH = 39000;
    public $content;
    public $curlResult;
    private $channel;
    public $username = 'Automated Alerts';

    public function declareEnvironment($environment)
    {
        if ($environment == 'production') {
            return;
        }

        $this->username .= " ($environment)";
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

    public function setChannel($alertType)
    {
        $this->channel = $this->getSlackChannel($alertType);
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

    /**
     * Sends a message to Slack
     *
     * @return bool
     */
    public function send()
    {
        $data = 'payload=' . json_encode([
            'channel' => $this->channel,
            'text' => $this->content,
            'username' => $this->username
        ]);
        $url = Configure::read('slackWebhookUrl');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $this->curlResult = curl_exec($ch);
        curl_close($ch);

        // Reset content
        $this->content = '';

        return $this->curlResult == 'ok';
    }
}
