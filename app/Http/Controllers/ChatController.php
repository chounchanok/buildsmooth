<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Events\NewChatMessage;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    // 📌 ดึงแชทล่าสุด (ใช้ในหน้าแสดงแชท)
    public function getChat()
    {
        try {
            $chats = Chat::latest()->take(20)->get();
            return response()->json($chats);
        } catch (\Exception $e) {
            Log::error('Error fetching chat: ' . $e->getMessage());
            return response()->json(['error' => 'เกิดข้อผิดพลาด'], 500);
        }
    }

    // 📌 บันทึกข้อความใหม่ลงฐานข้อมูล และ Broadcast ไปที่ Pusher
    public function storeChat(Request $request)
    {
        $request->validate([
            'user' => 'required|string',
            'message' => 'required|string',
        ]);

        try {
            $chat = Chat::create([
                'user' => $request->user,
                'message' => $request->message,
            ]);

            broadcast(new NewChatMessage($chat))->toOthers();

            return response()->json($chat);
        } catch (\Exception $e) {
            Log::error('Error saving chat: ' . $e->getMessage());
            return response()->json(['error' => 'เกิดข้อผิดพลาด'], 500);
        }
    }
}
