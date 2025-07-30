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

class WorkplaceController extends Controller
{

    public function workplace_list()
    {
        $data['order'] = DB::table('order')->leftjoin('customer','customer.customer_id','order.order_customer')
                                    ->where('order_status','!=',5)
                                    ->leftjoin('employee_work','employee_work.work_order','=','order.order_id')
                                    ->where('work_employee',Auth::user()->employee_id)
                                    ->orderBy('order_created','DESC')->get();
        return view('workplace/workplace_list',$data);
    }

    public function workplace_form()
    {
        $count_order = DB::table('order')->where('order_status','!=','0')->count();
        $data['order_no'] = 'OD-'.date('Ym').sprintf("%04d", ($count_order+1));
        $data['order_docno'] = 'IV-'.date('Ym').sprintf("%04d", ($count_order+1));
        $data['product'] = DB::table('product')->whereNull('product_delete')->orderBy('product_name','ASC')->get();
        return view('workplace/workplace_form',$data);
    }

    public function workplace_form_edit($id){
        $order = DB::table('order')->where('order_id',$id)->first();
        $data['customer'] = DB::table('customer')->where('customer_id',$order->order_customer)->first();
        $data['order_product'] = DB::table('order_product')->where('orderproduct_order',$order->order_id)->get();
        $data['order_product_addon'] = DB::table('order_product')->where('orderproduct_addon','T')->where('orderproduct_order',$order->order_id)->get();
        $data['order'] = $order;
        $data['work'] = DB::table('employee_work')->select('work_employee')->groupBy('work_employee')->where('work_order',$order->order_id)->get();
        $data['work_order'] = DB::table('employee_work')->where('work_order',$order->order_id)->first();
        $data['product'] = DB::table('product')->whereNull('product_delete')->orderBy('product_name','ASC')->get();
        $data['employee'] = DB::table('employee')->orderBy('employee_name','ASC')->get();
        $data['order_image_front'] = DB::table('order_image')->where('image_type','Front')->where('image_order',$id)->orderBy('image_id','ASC')->get();
        return view('workplace/workplace_form',$data);
    }

    public function check_employee(Request $request){
        $order_datework = DateTime::createFromFormat('d/m/Y', $request->input('order_datework'))?->format('Y-m-d');
        $check_free = DB::table('employee_work')
                            ->where('work_date', $order_datework)
                            ->where(function ($query) use ($request) {
                                $query->where(function ($q) use ($request) {
                                    $q->where('work_timestart', '<', $request->input('order_timeend'))
                                      ->where('work_timeend', '>', $request->input('order_timestart'));
                                });
                            })
                            ->get();
        $array_free = array();
        if(!empty($check_free)){
            foreach ($check_free as $key => $_free) {
                array_push($array_free, $_free->work_employee);
            }
        }
        $employeee = DB::table('employee')->get();
        if(!empty($array_free)){
            $employeee = DB::table('employee')->whereNotIn('employee_id',$array_free)->get();
        }

        return json_encode($employeee);
    }

    public function save_order_work(Request $request){
        // dd($request->input());
        $service_products = $request->input('service_product');
        $service_qtys = $request->input('service_qty');
        $service_prices = $request->input('service_price');
        $now = date('Y-m-d H:i:s');

        if(!empty($service_products)){
            foreach ($service_products as $index => $product_id) {
                if(!empty($product_id)){
                     DB::table('order_product')->insert([
                        'orderproduct_order' => $request->order_id,
                        'orderproduct_product' => $product_id,
                        'orderproduct_qty' => $service_qtys[$index],
                        'orderproduct_price' => $service_prices[$index],
                        'orderproduct_created' => $now,
                        'orderproduct_updated' => $now,
                    ]);
                }
            }
        }

        if ($request->hasFile('order_work_image')) {
            foreach ($request->file('order_work_image') as $index => $file) {
                // ตั้งชื่อไฟล์ใหม่ให้ไม่ซ้ำกัน
                $filename = time() . '_' . $index . '.' . $file->getClientOriginalExtension();

                // บันทึกไฟล์ไปที่ storage/public/order_images
                $filePath = $file->storeAs('order_images', $filename, 'public');

                // บันทึกข้อมูลลงฐานข้อมูล
                $data_image = [
                    'image_name' => $filename,
                    'image_path' => $filePath,
                    'image_order' => $request->order_id,
                    'image_type' => 'Back',
                    'image_created' => now(),
                ];
                DB::table('order_image')->insert($data_image);
            }
        }

        if(!empty($request->order_id)){
            $data_order['order_sumprice'] = $request->input('order_sumprice');
            $data_order['order_discount'] = $request->input('order_discount');
            $data_order['order_balance'] = $request->input('order_balance');
            $data_order['order_deposit'] = $request->input('order_deposit');
            $data_order['order_total'] = $request->input('order_total');
            $data_order['order_comment'] = htmlspecialchars($request->input('order_comment'));
            $data_order['order_updated'] = date('Y-m-d H:i:s');
            DB::table('order')->where('order_id',$request->order_id )->update($data_order);
        }

        return redirect('workplace_sign/'.$request->order_id);
    }

    public function workplace_sign($id){
        $order = DB::table('order')->where('order_id',$id)->first();
        $data['customer'] = DB::table('customer')->where('customer_id',$order->order_customer)->first();
        $data['order_product'] = DB::table('order_product')->where('orderproduct_order',$order->order_id)->get();
        $data['order_product_addon'] = DB::table('order_product')->where('orderproduct_addon','T')->where('orderproduct_order',$order->order_id)->get();
        $data['order'] = $order;
        $data['work'] = DB::table('employee_work')->select('work_employee')->groupBy('work_employee')->where('work_order',$order->order_id)->get();
        $data['work_order'] = DB::table('employee_work')->where('work_order',$order->order_id)->first();
        $data['product'] = DB::table('product')->whereNull('product_delete')->orderBy('product_name','ASC')->get();
        $data['employee'] = DB::table('employee')->orderBy('employee_name','ASC')->get();
        return view('workplace/workplace_sign',$data);
    }

    public function save_order_sign(Request $request){
        $data_order['order_sign'] = htmlspecialchars($request->input('signature'));
        $data_order['order_sign_timestamp'] = date('Y-m-d H:i:s');
        $data_order['order_sign_point'] = $request->input('order_sign_point');
        DB::table('order')->where('order_id',$request->order_id )->update($data_order);
        return redirect('workplace_list');
    }

}
