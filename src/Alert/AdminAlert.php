<?php
namespace App\Alert;

class AdminAlert
{
    public static function send(string $message): void
    {
        $alert = new Alert();
        $alert->addLine($message);
        $alert->send(Alert::TYPE_ADMIN);
    }
}
