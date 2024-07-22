<?php

date_default_timezone_set('Asia/Riyadh');

class TelegramBot {
    private $token;
    private $apiURL;
    private $randomMessages;
    private $filename;

    public function __construct($token) {
        $this->token = $token;
        $this->apiURL = "https://api.telegram.org/bot$token/";
        $this->filename = 'users.json';
        $this->randomMessages = [
            "مرحبًا! كيف يمكنني مساعدتك اليوم؟",
            "تذكر أن لديك خصم خاص اليوم!",
            "لا تنسَ الاطلاع على عروضنا الجديدة.",
            "إذا كان لديك أي سؤال، لا تتردد في طرحه.",
            "تفاعل معنا لتحصل على جوائز!"
        ];
    }

    // Send a message
    public function sendMessage($chatId, $message, $replyMarkup = null, $parseMode = 'HTML') {
        $url = $this->apiURL . "sendMessage?chat_id=$chatId&text=" . urlencode($message) . "&parse_mode=$parseMode";
        if ($replyMarkup) {
            $url .= "&reply_markup=" . urlencode(json_encode($replyMarkup));
        }
        file_get_contents($url);
    }

    // Save user data
    public function saveUserData($data) {
        $jsonData = json_decode(file_get_contents($this->filename), true) ?? [];

        foreach ($jsonData as &$user) {
            if ($user['user_id'] == $data['user_id']) {
                $user = array_merge($user, $data);
                file_put_contents($this->filename, json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                return;
            }
        }

        $jsonData[] = $data;
        file_put_contents($this->filename, json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    // Send random messages
    public function sendRandomMessages() {
        $jsonData = json_decode(file_get_contents($this->filename), true) ?? [];

        foreach ($jsonData as &$user) {
            $lastMessageTime = strtotime($user['last_message_time'] ?? '1970-01-01 00:00:00');
            $currentTime = time();

            if ($currentTime - $lastMessageTime >= 10) { // 10 seconds
                $chatId = $user['user_id'];
                $randomMessage = $this->randomMessages[array_rand($this->randomMessages)];
                $this->sendMessage($chatId, $randomMessage);
                $user['last_message_time'] = date('Y-m-d H:i:s'); // Update last message time
            }
        }

        file_put_contents($this->filename, json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    // Handle incoming messages
    public function handleMessage($update) {
        $chatId = $update['message']['chat']['id'];
        $username = $update['message']['from']['username'] ?? null;
        $userId = $update['message']['from']['id'];
        $fullName = $this->truncateFullName($update['message']['from']['first_name'] . ' ' . $update['message']['from']['last_name']);
        $date = date('Y-m-d H:i:s', $update['message']['date']);

        $replyMarkup = [
            'inline_keyboard' => [
                [['text' => 'بيع الحسابات', 'callback_data' => 'sell']],
                [['text' => 'شراء النقود', 'callback_data' => 'buy']]
            ]
        ];

        $this->sendMessage($chatId, "اختر أحد الخيارات التالية:", $replyMarkup);

        $userData = [
            'user_id' => $userId,
            'username' => $username,
            'full_name' => $fullName,
            'date' => $date,
            'message' => $update['message']['text'],
            'last_message_time' => $date // Save last message time
        ];

        $this->saveUserData($userData);
    }

    // Handle callback queries
    public function handleCallbackQuery($callbackQuery) {
        $chatId = $callbackQuery['message']['chat']['id'];
        $data = $callbackQuery['data'];
        $username = $callbackQuery['from']['username'] ?? null;
        $userId = $callbackQuery['from']['id'];
        $fullName = $this->truncateFullName($callbackQuery['from']['first_name'] . ' ' . $callbackQuery['from']['last_name']);
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

            $this->saveUserData($userData);

            $message = "
<b>قام المستخدم: $fullName</b>\n
" . ($username ? "<a href='https://t.me/$username'>@$username</a>\n" : "<b>لا يوجد اسم مستخدم</b>\n") . "
<b> ($action) بالضغط على زر </b>\n
<b>في </b><i>$date</i>
            ";

            $this->sendMessage($groupChatId, $message);
            $this->sendMessage($chatId, "تم إرسال طلبك بنجاح إلى المجموعة $action.");
        } else {
            $this->sendMessage($chatId, "حدث خطأ في معالجة الطلب.");
        }
    }

    // Process incoming updates
    public function processUpdate($update) {
        if (isset($update['message'])) {
            $this->handleMessage($update);
        }

        if (isset($update['callback_query'])) {
            $this->handleCallbackQuery($update['callback_query']);
        }
    }

    // Truncate full name to 30 characters
    private function truncateFullName($fullName) {
        return (strlen($fullName) > 30) ? mb_substr($fullName, 0, 30) . '...' : $fullName;
    }
}

// Main execution
$token = '';
$bot = new TelegramBot($token);

// Getting updates from Telegram
$update = json_decode(file_get_contents("php://input"), true);
$bot->processUpdate($update);

// Call the function to send random messages (add this line to your cron job script)
$bot->sendRandomMessages();


