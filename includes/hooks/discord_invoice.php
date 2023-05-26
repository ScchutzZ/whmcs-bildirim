<?php
if (!defined("WHMCS"))
    die("This file cannot be accessed directly");

add_hook('InvoicePaid', 1, function($vars) {
    // Discord Webhook URL
    $webhook_url = 'WEBHOOK-URL';

    // Get the invoice data
    $invoiceid = $vars['invoiceid'];
    $invoice = localAPI('GetInvoice', array(
        'invoiceid' => $invoiceid,
    ));
    $userid = $invoice['userid'];
    $client = localAPI('GetClientsDetails', array(
        'clientid' => $userid,
    ));

    // Create the message to send to Discord
    $message = "Yeni bir fatura ödemesi yapıldı.\n\n";
    $message .= "**Fatura ID:** #{$invoiceid}\n";
    $message .= "**Müşteri Adı:** {$client['fullname']}\n";
    $message .= "**Müşteri E-Posta:** {$client['email']}\n";
    $message .= "**URL:** https://whmcs-admin-panel-adresiniz.com/admin/invoices.php?action=edit&id={$vars['invoiceid']}";

    // Send the message to Discord
    $data = array(
        'content' => $message,
    );
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ),
    );

    // Make the API request
    $context  = stream_context_create($options);
    $result = file_get_contents($webhook_url, false, $context);

    return $result;
});
?>
