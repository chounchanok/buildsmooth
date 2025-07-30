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

class TaskController extends Controller
{

    public function task_manager_list()
    {
        $data['status'] = 0;
        $data['order'] = DB::table('order')->leftjoin('customer','customer.customer_id','order.order_customer')->orderBy('order_created','DESC')->get();
        return view('task/task_manager_list',$data);
    }

    public function task_manager_list_status(Request $request)
    {
        $data['status'] = $request->input('status');
        if($request->input('status') != 0){
            $data['order'] = DB::table('order')->leftjoin('customer','customer.customer_id','order.order_customer')->where('order_status',$request->input('status'))->orderBy('order_created','DESC')->get();
        }else{
            $data['order'] = DB::table('order')->leftjoin('customer','customer.customer_id','order.order_customer')->orderBy('order_created','DESC')->get();
        }
        return view('task/task_manager_list',$data);
    }

    public function task_manager_form()
    {
        $count_order = DB::table('order')->where('order_status','!=','0')->count();
        $count_order_a = DB::table('order')->where('order_type','A')->where('order_status','!=','0')->count();
        $data['order_no'] = 'OD-'.date('Ym').sprintf("%04d", ($count_order+1));
        $data['order_docno'] = 'A-'.date('Ym').sprintf("%04d", ($count_order_a+1));
        $data['product'] = DB::table('product')->where('product_active','T')->whereNull('product_delete')->orderBy('product_name','ASC')->get();
        return view('task/task_manager_form',$data);
    }

    public function count_order_type(Request $request){
        if(!empty($request->order_id)){
            $count_order_a = DB::table('order')->where('order_type',$request->order_type)->where('order_id','!=',$request->order_id)->where('order_status','!=','0')->count();
        }else{
            $count_order_a = DB::table('order')->where('order_type',$request->order_type)->where('order_status','!=','0')->count();
        }
        $data['order_docno'] = strtoupper($request->order_type).'-'.date('Ym').sprintf("%04d", ($count_order_a+1));
        return json_encode($data);
    }

    public function check_customer(Request $request){
        $customer_check = DB::table('customer')->where('customer_code',$request->customer_code)->first();
        echo json_encode($customer_check);
    }

    public function task_manager_edit($id)
    {
        $order = DB::table('order')->where('order_id',$id)->first();
        $data['customer'] = DB::table('customer')->where('customer_id',$order->order_customer)->first();
        $data['order_product'] = DB::table('order_product')->where('orderproduct_order',$order->order_id)->get();
        $data['order'] = $order;
        $data['product'] = DB::table('product')->whereNull('product_delete')->orderBy('product_name','ASC')->get();
        $data['order_image_front'] = DB::table('order_image')->where('image_type','Front')->where('image_order',$order->order_id)->orderBy('image_id','ASC')->get();
        return view('task/task_manager_form',$data);
    }

    public function save_task_order(Request $request)
    {
        // dd($request->input(), $request->file());
        DB::beginTransaction(); // เริ่ม transaction
        try {
            $now = Carbon::now();

            // ตรวจสอบและบันทึกไฟล์
            $filePath = null;
            if ($request->hasFile('order_file')) {
                $file = $request->file('order_file');
                $fileName = 'order_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('orders', $fileName, 'public'); // บันทึกไฟล์ใน storage/app/public/orders
            }

            // 1. ตรวจสอบข้อมูลลูกค้า
            $customer = DB::table('customer')
                ->where('customer_tel', $request->input('customer_tel'))
                ->first();

            if ($customer) {
                // อัพเดตข้อมูลลูกค้า
                $customer_id = $customer->customer_id;
                DB::table('customer')
                    ->where('customer_id', $customer_id)
                    ->update([
                        'customer_code' => $request->input('customer_code'),
                        'customer_annual' => (!empty($request->input('customer_annual')) ? $request->input('customer_annual') : 'F'),
                        'customer_firstname' => $request->input('customer_firstname'),
                        'customer_lastname' => $request->input('customer_lastname'),
                        'customer_company' => $request->input('customer_company'),
                        'customer_taxid' => $request->input('customer_taxid'),
                        'customer_tel2' => $request->input('customer_tel2'),
                        'customer_address' => $request->input('customer_address'),
                        'customer_setup_address' => $request->input('customer_setup_address'),
                        'customer_googlemap' => $request->input('customer_googlemap'),
                        'customer_updated' => $now,
                    ]);
            } else {
                // เพิ่มข้อมูลลูกค้าใหม่
                $customer_id = DB::table('customer')->insertGetId([
                    'customer_code' => $request->input('customer_code'),
                    'customer_annual' => (!empty($request->input('customer_annual')) ? $request->input('customer_annual') : 'F'),
                    'customer_firstname' => $request->input('customer_firstname'),
                    'customer_lastname' => $request->input('customer_lastname'),
                    'customer_company' => $request->input('customer_company'),
                    'customer_taxid' => $request->input('customer_taxid'),
                    'customer_tel' => $request->input('customer_tel'),
                    'customer_tel2' => $request->input('customer_tel2'),
                    'customer_address' => $request->input('customer_address'),
                    'customer_setup_address' => $request->input('customer_setup_address'),
                    'customer_googlemap' => $request->input('customer_googlemap'),
                    'customer_created' => $now,
                    'customer_updated' => $now,
                ]);
            }

            // 2. บันทึกข้อมูล Order

            if ($request->hasFile('order_work_front')) {
                foreach ($request->file('order_work_front') as $index => $file) {
                    // ตั้งชื่อไฟล์ใหม่ให้ไม่ซ้ำกัน
                    $filename = time() . '_' . $index . '.' . $file->getClientOriginalExtension();

                    // บันทึกไฟล์ไปที่ storage/public/order_images
                    $filePath = $file->storeAs('order_images', $filename, 'public');

                    // บันทึกข้อมูลลงฐานข้อมูล
                    $data_image = [
                        'image_name' => $filename,
                        'image_path' => $filePath,
                        'image_order' => $request->order_id,
                        'image_type' => 'Front',
                        'image_created' => now(),
                    ];
                    DB::table('order_image')->insert($data_image);
                }
            }

            $order_date = DateTime::createFromFormat('d/m/Y', $request->input('order_date'))?->format('Y-m-d');
            $order_installments = $request->input('order_installment', []);

            // ลบค่าว่าง/null
            $filtered_installments = array_filter($order_installments, function($value) {
                return $value !== null && $value !== '';
            });

            $order_installments_date = $request->input('order_installment_date', []);

            // ลบค่าว่าง/null
            $filtered_installments_date = array_filter($order_installments_date, function($value) {
                return $value !== null && $value !== '';
            });

            if(empty($request->input('order_id'))){
                $order_data['order_no'] = $request->input('order_no');
                $order_data['order_customer'] = $customer_id;
                $order_data['order_type'] = $request->input('order_type');
                $order_data['order_docno'] = $request->input('order_docno');
                $order_data['order_sumprice'] = $request->input('order_sumprice');
                $order_data['order_discount'] = $request->input('order_discount');
                $order_data['order_balance'] = $request->input('order_balance');
                $order_data['order_deposit'] = $request->input('order_deposit');
                $order_data['order_total'] = $request->input('order_total');
                $order_data['order_installment'] = json_encode(array_values($filtered_installments));
                $order_data['order_installment_date'] = json_encode(array_values($filtered_installments_date));
                $order_data['order_file'] = $filePath;
                $order_data['order_date'] = $order_date;
                $order_data['order_taxname'] = $request->input('order_taxname');
                $order_data['order_taxno'] = $request->input('order_taxno');
                $order_data['order_taxtel'] = $request->input('order_taxtel');
                $order_data['order_taxemail'] = $request->input('order_taxemail');
                $order_data['order_taxaddress'] = $request->input('order_taxaddress');
                $order_data['order_status'] = $request->input('order_status');
                $order_data['order_created'] = $now;
                $order_data['order_updated'] = $now;
                $order_data['order_employee'] = Auth::user()->employee_id;
                $order_data['order_employee_tel'] = $request->input('order_employee_tel');
                $order_data['order_comment'] = htmlspecialchars($request->input('order_comment'));
                $order_id = DB::table('order')->insertGetId($order_data);
            }else{

                $order_data['order_no'] = $request->input('order_no');
                $order_data['order_customer'] = $customer_id;
                $order_data['order_type'] = $request->input('order_type');
                $order_data['order_docno'] = $request->input('order_docno');
                $order_data['order_sumprice'] = $request->input('order_sumprice');
                $order_data['order_discount'] = $request->input('order_discount');
                $order_data['order_balance'] = $request->input('order_balance');
                $order_data['order_deposit'] = $request->input('order_deposit');
                $order_data['order_total'] = $request->input('order_total');
                $order_data['order_installment'] = json_encode(array_values($filtered_installments));
                $order_data['order_installment_date'] = json_encode(array_values($filtered_installments_date));
                if ($request->hasFile('order_file')) {
                    $order_data['order_file'] = $filePath;
                }
                $order_data['order_date'] = $order_date;
                $order_data['order_taxname'] = $request->input('order_taxname');
                $order_data['order_taxno'] = $request->input('order_taxno');
                $order_data['order_taxtel'] = $request->input('order_taxtel');
                $order_data['order_taxemail'] = $request->input('order_taxemail');
                $order_data['order_taxaddress'] = $request->input('order_taxaddress');
                $order_data['order_status'] = $request->input('order_status');
                $order_data['order_updated'] = $now;
                $order_data['order_employee_tel'] = $request->input('order_employee_tel');
                $order_data['order_comment'] = htmlspecialchars($request->input('order_comment'));

                DB::table('order')->where('order_id',$request->input('order_id'))->update($order_data);
                DB::table('order_product')->where('orderproduct_order',$request->input('order_id'))->delete();
                $order_id = $request->input('order_id');
            }

            // 3. บันทึกข้อมูลสินค้าใน Order (Order Products)
            $service_products = $request->input('service_product');
            $service_qtys = $request->input('service_qty');
            $service_prices = $request->input('service_price');

            if(!empty($service_products)){
                foreach ($service_products as $index => $product_id) {
                    DB::table('order_product')->insert([
                        'orderproduct_order' => $order_id,
                        'orderproduct_product' => $product_id,
                        'orderproduct_qty' => $service_qtys[$index],
                        'orderproduct_price' => $service_prices[$index],
                        'orderproduct_created' => $now,
                        'orderproduct_updated' => $now,
                    ]);
                }
            }

            $data_check['notification_order'] = $order_id;
            DB::table('notification')->where($data_check)->delete();
            if(!empty($request->input('notification_date'))){
                foreach ($request->input('notification_date') as $k => $_notification_date) {
                    DB::table('notification')->insert([
                        'notification_date' => DateTime::createFromFormat('d/m/Y', $_notification_date)?->format('Y-m-d'),
                        'notification_order' => $order_id,
                        'notification_action' => 'F',
                        'notification_created' => $now,
                        'notification_updated' => $now,
                    ]);
                }
            }

            DB::commit(); // บันทึก transaction
            return response()->json(['message' => 'Order saved successfully!'], 200);
        } catch (\Exception $e) {
            DB::rollBack(); // ย้อนกลับ transaction ในกรณีที่เกิดข้อผิดพลาด
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    public function delete_task_order($order_id){
        $data['order_status'] = 4;
        DB::table('order')->where('order_id',$order_id)->update($data);
        return response()->json(['message' => 'Order Delete successfully!'], 200);
    }

    public function change_status_order(Request $request){
        $data['order_status'] = $request->input('order_status');
        DB::table('order')->where('order_id',$request->input('order_id'))->update($data);
        return response()->json(['message' => 'Order update status successfully!'], 200);
    }

}
