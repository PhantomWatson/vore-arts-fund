<?php
namespace App\Alert;

class Alert {
    const TYPE_APPLICATIONS = 'applications';
    const TYPE_TRANSACTIONS = 'transactions';

    public string $content = '';

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
     * Adds an unordered bulleted list to the message content
     *
     * @param array $list
     * @return void
     */
    public function addList(array $list)
    {
        foreach ($list as $item) {
            $this->addLine("• $item");
        }
    }

    public function send(string $alertType)
    {
        // Don't send alerts when running tests
        if (defined('PHPUNIT_RUNNING') && constant('PHPUNIT_RUNNING')) {
            return;
        }

        // Send through Slack
        $slack = new Slack();
        $slack->setChannel($alertType);
        $slack->content = $this->content;
        require_once(ROOT . DS . 'config' . DS . 'environment.php');
        $slack->declareEnvironment(getEnvironment());
        $slack->sendLegacy(); // Trying both of these until it's confirmed that both work the same
        $slack->send();
    }
}
