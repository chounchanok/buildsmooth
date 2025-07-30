<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use DateTime;
use Session;
use Response;
use Yajra\DataTables\Facades\DataTables;
use File;
use Folklore\Image\Facades\Image;
use Gloudemans\Shoppingcart\Facades\Cart;
use Auth;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Excel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash; 

class EmployeeController extends Controller
{

    public function employee_list()
    {
        $data['employee'] = DB::table('employee')->get();
        return view('employee/employee_list',$data);
    }

    public function employee_form()
    {
        $data['employee_position'] = DB::table('position')->get();
        return view('employee/employee_form',$data);
    }

    public function employee_edit($id)
    {
        $data['employee_position'] = DB::table('position')->get();
        $data['employee'] = DB::table('employee')->where('employee_id',$id)->first();
        return view('employee/employee_form',$data);
    }

    public function save_employee(Request $request)
    {
        // dd($request->input());
        $data['employee_position'] = $request->input('employee_position');
        $data['employee_name'] = $request->input('employee_name');
        $data['employee_tel'] = $request->input('employee_tel');
        $data['employee_code'] = $request->input('employee_code');
        $data['employee_commission'] = $request->input('employee_commission');

        if(!empty($request->input('employee_position'))){
            $check_postiion = DB::table('position')->where('position_name','LIKE',$request->input('employee_position'))->first();
            if(empty($check_postiion)){
                $data_position['position_name'] = $request->input('employee_position');
                $data_position['created_at'] = date('Y-m-d H:i:s');
                $get_positionid = DB::table('position')->insertGetID($data_position);
            }else{
                $get_positionid = $check_postiion->position_id;
            }
        }

        if(!empty($request->input('employee_id'))){
            $data['employee_updated'] = date('Y-m-d H:i:s');
            DB::table('employee')->where('employee_id',$request->input('employee_id'))->update($data);
            $employee_id = $request->input('employee_id');

            $check_user = DB::table('users')->where('employee_id',$employee_id)->first();

            $data_users['name'] = $request->input('employee_name'); // ใช้ชื่อจาก employee_name
            $data_users['username'] = $request->input('employee_tel'); // ใช้ชื่อจาก employee_name
            $data_users['email'] = $request->input('employee_tel'); // ต้องมีฟิลด์ email ใน form
            $data_users['active'] = $request->input('employee_active') == 'T' ? 1 : 0; // เชื่อมโยง employee_id กับ users
            $data_users['position'] = $get_positionid;

            if(!empty($request->input('employee_password'))){
                $data_users['password'] = Hash::make($request->input('employee_password')); // รับรหัสผ่านจาก form
            }

            if(!empty($check_user)){
                DB::table('users')->where('employee_id',$employee_id)->update($data_users);
            }else{
                $data_users['employee_id'] = $employee_id; // เชื่อมโยง employee_id กับ users
                DB::table('users')->insert($data_users);
            }

        }else{
            $data['employee_active'] = 'T';
            $data['employee_created'] = date('Y-m-d H:i:s');
            DB::table('employee')->insert($data);

            $employee_id = DB::getPdo()->lastInsertId(); 

            // สร้างข้อมูล User ในตาราง users
            $data_users['name'] = $request->input('employee_name'); // ใช้ชื่อจาก employee_name
            $data_users['username'] = $request->input('employee_tel'); // ใช้ชื่อจาก employee_name
            $data_users['email'] = $request->input('employee_tel'); // ต้องมีฟิลด์ email ใน form
            $data_users['password'] = Hash::make($request->input('employee_password')); // รับรหัสผ่านจาก form
            $data_users['employee_id'] = $employee_id; // เชื่อมโยง employee_id กับ users
            $data_users['active'] = $request->input('employee_active') == 'T' ? 1 : 0; // เชื่อมโยง employee_id กับ users
            $data_users['position'] = $get_positionid;
            DB::table('users')->insert($data_users);
        }

        return redirect('employee_edit/'.$employee_id);
    }

    public function employee_delete($id){
        $data['employee_active'] = 'F';
        DB::table('employee')->where('employee_id',$id)->update($data);
        return redirect('employee_list');
    }

}
