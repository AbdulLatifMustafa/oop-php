<?php

  date_default_timezone_set('Asia/Riyadh');
//token
$token = '';
$apiURL = "https://api.telegram.org/bot$token/";

// Function to send a message
function sendMessage($chatId, $message, $replyMarkup = null, $parseMode = 'HTML') {
    global $apiURL;
    $url = $apiURL . "sendMessage?chat_id=$chatId&text=" . urlencode($message) . "&parse_mode=$parseMode";
    if ($replyMarkup) {
        $url .= "&reply_markup=" . urlencode(json_encode($replyMarkup));
    }
    file_get_contents($url);
}

// Function to save user data
function saveUserData($data) {
    $filename = 'users.json';
    $jsonData = json_decode(file_get_contents($filename), true) ?? [];
    $jsonData[] = $data;
file_put_contents($filename, json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Function to handle messages
function handleMessage($update) {
    $chatId = $update['message']['chat']['id'];
    $username = $update['message']['from']['username'];
    $userId = $update['message']['from']['id'];
    $fullName = $update['message']['from']['first_name'] . ' ' . $update['message']['from']['last_name'];
    $date = date('Y-m-d H:i:s', $update['message']['date']);

    $replyMarkup = [
        'inline_keyboard' => [
            [['text' => 'بيع الحسابات', 'callback_data' => 'sell']],
            [['text' => 'شراء النقود', 'callback_data' => 'buy']]
        ]
    ];

    sendMessage($chatId, "اختر أحد الخيارات التالية:", $replyMarkup);

    $userData = [
        'user_id' => $userId,
        'username' => $username,
        'full_name' => $fullName,
        'date' => $date,
        'message' => $update['message']['text']
    ];

    saveUserData($userData);
}

// Function to handle callback queries
function handleCallbackQuery($callbackQuery) {
    $chatId = $callbackQuery['message']['chat']['id'];
    $data = $callbackQuery['data'];
    $username = $callbackQuery['from']['username'];
    $userId = $callbackQuery['from']['id'];
    $fullName = $callbackQuery['from']['first_name'] . ' ' . $callbackQuery['from']['last_name'];
    $date = date('Y-m-d H:i:s', $callbackQuery['message']['date']);

    $groupChatIds = [
        'sell' => '', // Sell accounts group ID
        'buy' => ''    // Buy currency group ID
    ];

    $groupChatId = $groupChatIds[$data] ?? null;

    if ($groupChatId) {
        $action = ($data == 'sell') ? 'بيع الحسابات' : 'شراء النقود';

        $userData = [
            'user_id' => $userId,
            'username' => $username,
            'full_name' => $fullName,
            'date' => $date,
            'action' => $action
        ];

        saveUserData($userData);

        $message = "
     <b>قام المستخدم: $fullName</b>\n
<a href='https://t.me/$username'>@$username</a>\n
<b> ($action) بالضغط على زر </b>\n
<b>في </b><i>$date</i>

        ";
        sendMessage($groupChatId, $message);
        sendMessage($chatId, "تم إرسال طلبك بنجاح إلى المجموعة $action.");
    } else {
        sendMessage($chatId, "حدث خطأ في معالجة الطلب.");
    }
}

// Main function to process updates
function processUpdate($update) {
    if (isset($update['message'])) {
        handleMessage($update);
    }

    if (isset($update['callback_query'])) {
        handleCallbackQuery($update['callback_query']);
    }
}

// Getting updates from Telegram
$update = json_decode(file_get_contents("php://input"), true);
processUpdate($update);

?>
