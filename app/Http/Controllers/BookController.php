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

class BookController extends Controller
{

    public function book_list()
    {
        $data['order'] = DB::table('order')->leftjoin('customer','customer.customer_id','order.order_customer')->where('order_status','!=',5)->orderBy('order_created','DESC')->get();
        return view('book/book_list',$data);
    }

    public function book_form()
    {
        $count_order = DB::table('order')->where('order_status','!=','0')->count();
        $data['order_no'] = 'OD-'.date('Ym').sprintf("%04d", ($count_order+1));
        $data['order_docno'] = 'IV-'.date('Ym').sprintf("%04d", ($count_order+1));
        $data['product'] = DB::table('product')->whereNull('product_delete')->orderBy('product_name','ASC')->get();
        return view('book/book_form',$data);
    }

    public function book_form_edit($id){
        $order = DB::table('order')->where('order_id',$id)->first();
        $data['customer'] = DB::table('customer')->where('customer_id',$order->order_customer)->first();
        $data['order_product'] = DB::table('order_product')->where('orderproduct_order',$order->order_id)->get();
        $data['order'] = $order;
        $data['work'] = DB::table('employee_work')->select('work_employee')->groupBy('work_employee')->where('work_order',$order->order_id)->get();
        $data['work_order'] = DB::table('employee_work')->where('work_order',$order->order_id)->first();
        $data['product'] = DB::table('product')->whereNull('product_delete')->orderBy('product_name','ASC')->get();
        $data['order_image_front'] = DB::table('order_image')->where('image_type','Front')->where('image_order',$order->order_id)->orderBy('image_id','ASC')->get();
        $data['order_image_back'] = DB::table('order_image')->where('image_type','Back')->where('image_order',$order->order_id)->orderBy('image_id','ASC')->get();
        $data['employee'] = DB::table('employee')->orderBy('employee_name','ASC')->get();
        $data['order_product_addon'] = DB::table('order_product')->where('orderproduct_addon','T')->where('orderproduct_order',$order->order_id)->get();
        return view('book/book_form',$data);
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

    public function save_order(Request $request){
        $work_date = DateTime::createFromFormat('d/m/Y', $request->input('order_datework'))?->format('Y-m-d');

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
        
        // dd($work_date,$request->input());
        if(!empty($request->service_employee)) {
            foreach ($request->service_employee as $key => $service_employee) {
                $data['work_employee'] = $service_employee;
                $data['work_date'] = $work_date;
                $data['work_timestart'] = $request->order_timestart;
                $data['work_timeend'] = $request->order_timeend;
                $data['work_customer'] = $request->order_customer;
                $data['work_order'] = $request->order_id;
                if(!empty($request->work_id)){
                    $data['work_updated'] = date('Y-m-d H:i:s');
                    DB::table('employee_work')->where('work_id',$request->work_id)->update($data);
                }else{
                    $data['work_created'] = date('Y-m-d H:i:s');
                    DB::table('employee_work')->where('work_order',$request->order_id)->insert($data);
                }
            }
        }

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

        if(!empty($request->order_id)){
            $data_order['order_status'] = $request->input('order_status');
            $data_order['order_comment'] = htmlspecialchars($request->input('order_comment'));
            $data_order['order_updated'] = date('Y-m-d H:i:s');
            DB::table('order')->where('order_id',$request->order_id )->update($data_order);
        }

        return redirect('book_form_edit/'.$request->order_id);

    }

    public function calendar()
    {
        return view('calendar/calendar');
    }

    public function get_calendar_events(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $events = DB::table('order')
                    ->leftJoin('customer', 'order.order_customer', '=', 'customer.customer_id')
                    ->leftJoin('employee_work', 'employee_work.work_order', '=', 'order.order_id')
                    ->whereMonth('employee_work.work_date', $month)
                    ->whereYear('employee_work.work_date', $year)
                    ->select(
                        'order.order_id',
                        'order.order_status',
                        'order.order_comment',
                        DB::raw("CONCAT(customer.customer_firstname, ' ', customer.customer_lastname) AS title"),
                        'customer.customer_company',
                        'employee_work.work_date AS start',
                        'employee_work.work_timestart',
                        'employee_work.work_timeend'
                    )
                    ->distinct()
                    ->get();
        // dd($events);
        // แปลงข้อมูลให้อยู่ในรูปแบบ JSON ที่ FullCalendar ใช้
        $formattedEvents = $events->map(function ($event) {
            $employee_work = DB::table('employee_work')->select('work_employee')->where('work_order',$event->order_id)->get();
            // dd($employee);
            $employee_name = '';
            if(!empty($employee_work)){
                foreach($employee_work as $_employee_work){
                    $employee = DB::table('employee')->where('employee_id', $_employee_work->work_employee)->first();
                    $employee_name .= 'ช่าง'.$employee->employee_name.', ';
                }
            }
            $employee_name = substr($employee_name, 0, -2);

            if($event->order_status == 1){
                $show_color = 'text-warning'; $show_status = 'รอนัดหมาย';
            }elseif($event->order_status == 2){
                $show_color = 'text-warning'; $show_status = 'รอดำเนินการ';
            }elseif($event->order_status == 3){
                $show_color = 'text-warning'; $show_status = 'รอชำระเงิน';
            }elseif($event->order_status == 4){
                $show_color = 'text-success'; $show_status = 'ดำเนินการเสร็จสิ้น';
            }elseif($event->order_status == 5){
                $show_color = 'text-danger'; $show_status = 'ยกเลิกออเดอร์';
            }

            return [
                'id' => $event->order_id,
                'title' => 'ลูกค้า '.$event->customer_company.' '.$event->title.' | '.( !empty($employee_name) ? $employee_name : '').' | สถานะ '.$show_status,
                'start' => $event->start . 'T' . $event->work_timestart,
                'end' => $event->start . 'T' . $event->work_timeend,
                'timestart' => $event->work_timestart,
                'timeend' => $event->work_timeend,
                'customer' => $event->title,
                'employee' => ( !empty($employee_name) ? $employee_name : ''),
                'status' => $show_status,
                'order_comment' => $event->order_comment,
            ];
        });

        return response()->json($formattedEvents);
    }

}
