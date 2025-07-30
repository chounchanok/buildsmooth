<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use Session;
use Illuminate\Support\Facades\Storage;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\FileMessageBuilder;

class LineController extends Controller
{
    private $bot;

    public function __construct()
    {
        $httpClient = new CurlHTTPClient(env('LINE_ACCESS_TOKEN'));
        $this->bot = new LINEBot($httpClient, ['channelSecret' => env('LINE_CHANNEL_SECRET')]);
    }

    public function sendMessage(Request $request)
    {
        $userId = $request->userID; // ต้องแก้ให้เป็น userId ของลูกค้า
        $text = $request->message;

        $textMessageBuilder = new TextMessageBuilder($text);
        $this->bot->pushMessage($userId, $textMessageBuilder);

        \DB::table('line_messages')->insert([
            'user_id' => $userId,
            'type' => 'Text',
            'user_name' => 'Admin',
            'message' => $text,
            'status' => 'sent',
            'created_at' => now(),
        ]);

        return response()->json(['status' => 'sent']);
    }

    public function sendFileMessage(Request $request)
    {
        $userId = $request->userID;  // userId ของลูกค้าที่จะส่งไฟล์ไปให้
        $file = $request->file('file');  // รับไฟล์จาก request

        if (!$file) {
            return response()->json(['status' => 'error', 'message' => 'No file uploaded']);
        }

        $path = $file->store('files', 'public');

        $fileUrl = url('storage/' . $path);  // ใช้ url() เพื่อให้ได้ absolute URL
        // \Log::debug('Image URL:', ['url' => $fileUrl]);

        if (in_array($file->extension(), ['jpg', 'jpeg', 'png', 'gif'])) {
            $imageMessage = new ImageMessageBuilder(
                $fileUrl,  // URL ของไฟล์ที่เก็บ
                $fileUrl   // URL ของไฟล์ที่เก็บ (ตัวเดียวกัน)
            );

            $response = $this->bot->pushMessage($userId, $imageMessage);
            // \Log::debug('LINE response:', ['response' => $response]);
        }
        else {
            $fileMessage = new FileMessageBuilder(
                $fileUrl,  // URL ของไฟล์ที่เก็บ
                $file->getClientOriginalName()  // ชื่อไฟล์ที่แสดงใน LINE
            );
            $response = $this->bot->pushMessage($userId, $fileMessage);
            // \Log::debug('LINE response:', ['response' => $response]);
        }

        \DB::table('line_messages')->insert([
            'user_id' => $userId,
            'user_name' => 'Admin',
            'type' => 'File',
            'message' => 'storage/'.$path,
            'status' => 'sent',
            'created_at' => now(),
        ]);

        return response()->json(['status' => 'sent']);
    }

    public function callback(Request $request){
        dd($request->input());
    }

    public function make_as_read(Request $request) {
        \DB::table('line_messages')
            ->where('user_id', $request->user_id)
            ->where('status', 'received')
            ->update(['status' => 'read']);
        Session::put('user_ID', $request->user_id);
        return response()->json(['success' => true]);
    }

    public function get_line_chats(){
        return response()->json([
            'messages' => \DB::table('line_messages')->orderBy('created_at', 'asc')->get(),
            'users' => \DB::table('line_user')->orderBy('user_userName', 'asc')->get(),
            'session' => Session::get('user_ID'),
        ]);
    }
}
