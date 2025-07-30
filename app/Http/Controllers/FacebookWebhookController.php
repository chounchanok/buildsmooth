<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FacebookWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Verify Token Validation
        if ($request->get('hub_verify_token') === env('FACEBOOK_VERIFY_TOKEN')) {
            return response($request->get('hub_challenge'), 200);
        }

        // Process Messages
        $data = $request->all();
        if (!empty($data['entry'])) {
            foreach ($data['entry'] as $entry) {
                foreach ($entry['messaging'] as $message) {
                    if (isset($message['message'])) {
                        $sender = $message['sender']['id'];
                        $text = $message['message']['text'];

                        // ส่งข้อความตอบกลับ
                        $this->sendMessage($sender, "ข้อความที่ได้รับ: $text");
                    }
                }
            }
        }

        return response('EVENT_RECEIVED', 200);
    }

    private function sendMessage($recipientId, $messageText)
    {
        $pageAccessToken = env('FACEBOOK_PAGE_ACCESS_TOKEN');
        $url = "https://graph.facebook.com/v12.0/me/messages?access_token=$pageAccessToken";

        $response = [
            'recipient' => ['id' => $recipientId],
            'message' => ['text' => $messageText],
        ];

        $client = new \GuzzleHttp\Client();
        $client->post($url, ['json' => $response]);
    }
}
