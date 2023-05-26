<?php
if (!defined("WHMCS"))
die("This file cannot be accessed directly");
use WHMCS\Database\Capsule;

add_hook('AdminAreaPage', 1, function($vars) {
    // Oturum açan adminin ID'sini alın
    $adminId = $_SESSION['adminid'];

    // Geçerli oturum açıldıysa ve önceki oturum açılmadıysa bildirim gönderin
    if (!isset($_SESSION['last_admin_id']) || $_SESSION['last_admin_id'] != $adminId) {
        // Admin bilgilerini alın
        $admin = Capsule::table('tbladmins')->where('id', $adminId)->first();
        $adminName = $admin->username;

        // Discord Webhook URL'sini ve içeriğini belirleyin
        $webhookUrl = 'WEBHOOK-URL';
        $message = "Admin paneline giriş yapıldı.\n**Giriş Yapan Admin:** $adminName\n**Giriş Tarihi:** " . date('Y-m-d H:i:s');

        // Discord'a bildirim göndermek için CURL kullanın
        $ch = curl_init($webhookUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['content' => $message]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);

        // Son oturum açan adminin ID'sini güncelleyin
        $_SESSION['last_admin_id'] = $adminId;
    }
});
