<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Get the authenticated User's profile.
     * ดึงข้อมูลโปรไฟล์ของผู้ใช้ที่ล็อกอิน
     */
    public function show(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Update the authenticated User's profile.
     * อัปเดตข้อมูลโปรไฟล์
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users')->ignore($user->user_id, 'user_id'),
            ],
            // ไม่ให้อัปเดต email และ password จากฟังก์ชันนี้
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user->update($request->only('first_name', 'last_name', 'phone_number'));

        return response()->json($user);
    }

    /**
     * Change the authenticated User's password.
     * เปลี่ยนรหัสผ่าน
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password does not match'], 401);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password changed successfully']);
    }

    /**
     * Get notifications for the authenticated user.
     * ดึงรายการแจ้งเตือนทั้งหมด
     */
    public function notifications(Request $request)
    {
        // สมมติว่ามี Model Notification และมีความสัมพันธ์ (Relationship) ใน Model User
        $notifications = $request->user()->notifications()->latest()->paginate(15);

        return response()->json($notifications);
    }
}
