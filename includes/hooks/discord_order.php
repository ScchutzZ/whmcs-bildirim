<?php
if (!defined("WHMCS"))
die("This file cannot be accessed directly");
add_hook('AfterShoppingCartCheckout', 1, function($vars) {
    // Discord Webhook URL
    $webhook_url = 'WEBHOOK-URL';

    // Get the order data
    $orderid = $vars['OrderID'];
    $order = localAPI('GetOrders', array(
        'id' => $orderid,
    ));
    $userid = $order['orders']['order'][0]['userid'];
    $client = localAPI('GetClientsDetails', array(
        'clientid' => $userid,
    ));

    // Create the message to send to Discord
    $message = "Yeni bir sipariş oluşturuldu.\n\n";
    $message .= "**Sipariş ID:** #{$vars['OrderID']}\n";
    $message .= "**Müşteri Adı:** {$client['fullname']}\n";
    $message .= "**Müşteri E-Posta:** {$client['email']}\n";
    $message .= "**Ödeme Yöntemi:** {$vars['PaymentMethod']}\n";
    $message .= "**URL:** https://whmcs-admin-panel-adresiniz.com/admin/orders.php?action=view&id={$vars['OrderID']}";

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