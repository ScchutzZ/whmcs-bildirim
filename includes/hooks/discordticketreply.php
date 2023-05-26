<?php
if (!defined("WHMCS"))
die("This file cannot be accessed directly");
use WHMCS\Database\Capsule;

add_hook('TicketUserReply', 1, function($vars) {
    $ticketid = $vars['ticketid'];
    $clientid = $vars['userid'];
    $message = $vars['message'];

    // Discord Webhook URL'si
    $webhook_url = 'WEBHOOK-URL';

    // Ticket bilgilerini al
    $ticket = Capsule::table('tbltickets')
        ->where('id', $ticketid)
        ->first();

    // Kullanıcı bilgilerini al
    $user = Capsule::table('tblclients')
        ->select('firstname', 'lastname', 'email')
        ->where('id', $clientid)
        ->first();

    // Mesajı oluştur
    $discord_message = "Tickete müşteri tarafından yeni bir yanıt gönderildi.\n\n";
    $discord_message .= '**Konu:** ' . $ticket->title . PHP_EOL;
    $discord_message .= '**Müşteri:** ' . $user->firstname . ' ' . $user->lastname . ' (' . $user->email . ')' . PHP_EOL;
    $discord_message .= '**Yanıt:** ' . $message . PHP_EOL;
    $discord_message .= "**Ticket URL:** https://whmcs-admin-panel-adresiniz.com/admin/supporttickets.php?action=view&id={$vars['ticketid']}";

    // Data array
    $data = [
        'content' => $discord_message
    ];

    // GuzzleHttp kütüphanesi ile Discord'a istek at
    $client = new \GuzzleHttp\Client();
    $response = $client->post($webhook_url, [
        'json' => $data
    ]);
});
