<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use Illuminate\Support\Facades\Http;

class LineWebhookController extends Controller
{
    protected $bot;

    public function __construct()
    {
        $httpClient = new Client();
        $this->bot = new LINEBot(
            new \LINE\LINEBot\HTTPClient\CurlHTTPClient(env('LINE_ACCESS_TOKEN')),
            ['channelSecret' => env('LINE_CHANNEL_SECRET')]
        );
    }

    public function webhook(Request $request)
    {
        $data = $request->all();
        // Log::info('[LINE] Webhook received', $data);

        function getContent($datas)
        {
            $messageId = $datas['messageId'];
            $token = $datas['token'];

            $url = "https://api-data.line.me/v2/bot/message/{$messageId}/content";

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer {$token}"
                ),
            ));

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $err = curl_error($curl);

            curl_close($curl);

            $result = [
                'result' => $err ? 'E' : 'S',
                'message' => $err ?: 'Success',
                'http_code' => $httpCode,
                'response' => $response,
            ];

            return $result;
        }


        foreach ($data['events'] as $event) {

            $userId = $event['source']['userId'];

            $profile = $this->bot->getProfile($userId);
            if ($profile->isSucceeded()) {
                $userName = $profile->getJSONDecodedBody()['displayName']; // ชื่อผู้ใช้
            } else {
                $userName = 'ไม่พบชื่อผู้ใช้';
            }

            $check_user = \DB::table('line_user')->where('user_userID',$userId)->first();
            if(empty($check_user)){
                \DB::table('line_user')->insert([
                    'user_userID' => $userId,
                    'user_userName' => $userName,
                ]);
            }else{
                \DB::table('line_user')->where('user_userID',$userId)->update([
                    'user_userName' => $userName,
                ]);
            }

            if ($event['type'] === 'message' && $event['message']['type'] === 'text') {
                $replyToken = $event['replyToken'];

                $userMessage = $event['message']['text'];

                // บันทึกข้อความลงฐานข้อมูล
                \DB::table('line_messages')->insert([
                    'user_id' => $userId,
                    'user_name' => $userName,
                    'type' => 'Text',
                    'message' => $userMessage,
                    'status' => 'received',
                    'created_at' => now(),
                ]);

            }elseif ($event['type'] === 'message' && $event['message']['type'] === 'image') {

                $imageId = $event['message']['id'];
                $accessToken = env('LINE_ACCESS_TOKEN');

                $datas['messageId'] = $imageId;
                $datas['token'] = $accessToken;

                $images = getContent($datas);
                // Log::info('[LINE] Webhook images', $images);

                $fileName = null;
                if ($images['result'] === 'S') {
                    $imageBinary = $images['response'];
                    $fileName = uniqid('lineimg_') . '.jpg';
                    $savePath = storage_path('app/public/line_images/' . $fileName);

                    // Ensure directory exists
                    if (!file_exists(dirname($savePath))) {
                        mkdir(dirname($savePath), 0755, true);
                    }

                    file_put_contents($savePath, $imageBinary);
                }

                $userMessage = $fileName ? 'storage/line_images/' . $fileName : null;

                \DB::table('line_messages')->insert([
                    'user_id' => $userId,
                    'user_name' => $userName,
                    'type' => 'File',
                    'message' => $userMessage,
                    'status' => 'received',
                    'created_at' => now(),
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
