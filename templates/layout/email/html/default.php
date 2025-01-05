<?php
/**
 * @var \App\View\AppView $this
 * @var string|null $subject
 */

$host = $this->getRequest()->host() ?: 'voreartsfund.org';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>
        <?= $subject ?? '' ?>
    </title>

</head>
<body
    style="font-family: Helvetica, sans-serif;-webkit-font-smoothing: antialiased;font-size: 16px;line-height: 1.3;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-color: #f4f5f6;margin: 0;padding: 0;">
<div class="header" style="background: #ffffff;border: 1px solid #eaebed;clear: both;padding: 24px;text-align: center;">
    <a href="https://<?= $host ?>" style="color: #0867ec;text-decoration: underline;">
        <img src="https://<?= $host ?>/img/logo/logo.wordmark.v5.white.png" alt="Vore Arts Fund" title="Vore Arts Fund"
             style="border: 0;margin: auto;max-height: 150px;max-width: 75%;">
    </a>
</div>

<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body"
       style="border-collapse: separate;mso-table-lspace: 0pt;mso-table-rspace: 0pt;width: 100%;background-color: #f4f5f6;">
    <tr>
        <td style="font-family: Helvetica, sans-serif;font-size: 16px;vertical-align: top;">&nbsp;</td>
        <td class="container"
            style="font-family: Helvetica, sans-serif;font-size: 16px;vertical-align: top;max-width: 600px;padding: 0;padding-top: 24px;width: 600px;margin: 0 auto !important;">
            <div class="content"
                 style="box-sizing: border-box;display: block;margin: 0 auto;max-width: 600px;padding: 0;">

                <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="main"
                       style="border-collapse: separate;mso-table-lspace: 0pt;mso-table-rspace: 0pt;width: 100%;background: #ffffff;border: 1px solid #eaebed;border-radius: 16px;">
                    <tr>
                        <td class="wrapper"
                            style="font-family: Helvetica, sans-serif;font-size: 16px;vertical-align: top;box-sizing: border-box;padding: 24px;">
                            <?= $this->fetch('content') ?>
                        </td>
                    </tr>
                </table>

                <div class="footer" style="clear: both;padding-top: 24px;text-align: center;width: 100%;">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0"
                           style="border-collapse: separate;mso-table-lspace: 0pt;mso-table-rspace: 0pt;width: 100%;">
                        <tr>
                            <td class="content-block"
                                style="font-family: Helvetica, sans-serif;font-size: 12px;vertical-align: top;color: #9a9ea6;text-align: center;">
                                This email was sent by <a href="https://<?= $host ?>">VoreArtsFund.org</a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </td>
        <td style="font-family: Helvetica, sans-serif;font-size: 16px;vertical-align: top;">&nbsp;</td>
    </tr>
</table>
</body>
</html>
