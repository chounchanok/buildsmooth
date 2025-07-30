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

class DashboardController extends Controller
{
	public function dashboard(Request $request)
    {
    	$datenow = date('Y-m-d');
    	$datestart = (!empty($request->input('datestart')) ? $request->input('datestart') : date('Y-m-d', strtotime($datenow.'-3 day')));
    	$dateend = (!empty($request->input('dateend')) ? $request->input('dateend') : date('Y-m-d', strtotime($datenow.'+3 day')));

    	$_month = date('Y-m');

    	$data['employee_sale'] = DB::table('employee')->where('employee_position','LIKE','พนักงานขาย')->get();
        $data['employee_technicial'] = DB::table('employee')->where('employee_position','LIKE','ช่าง%')->get();
        $data['product'] = DB::table('product')->get();
        $data['order'] = DB::table('order')->whereBetween('order_date',[$datestart, $dateend])->get();
        $data['order_count'] = DB::table('order')->whereBetween('order_date',[$datestart, $dateend])->count();
        $data['order_total'] = DB::table('order')->whereBetween('order_date',[$datestart, $dateend])->sum('order_total');
        $data['order_total_month'] = DB::table('order')->where('order_date','LIKE',$_month.'%')->sum('order_total');

        $order_summery = DB::table('order_product')
									->leftjoin('order','order.order_id','=','order_product.orderproduct_order')
									->whereBetween('order.order_date',[$datestart, $dateend])
									->get();
		$data['order_summery_product'] = 0;
		$data['order_summery_service'] = 0;

		if(!empty($order_summery)){
			foreach($order_summery as $key => $_order_summery) {
				$product = DB::table('product')->where('product_id',$_order_summery->orderproduct_product)->first();
				if($product->product_type == 'บริการ'){
					$data['order_summery_service'] += $_order_summery->orderproduct_price; 
				}else{
					$data['order_summery_product'] += $_order_summery->orderproduct_price; 
				}
			}
		}
		
		// dd($order_summery);

        $data['product_type'] = DB::table('product')->select('product_type')->groupBy('product_type')->get();

    	$data['datestart'] = $datestart.' 00:00:00';
    	$data['dateend'] = $dateend.' 23:59:59';
    	$data['datestart_show'] = $datestart;
    	$data['dateend_show'] = $dateend;

		$product = DB::table('product')->get();
		$top_products = [];
		$top_amounts = [];
		if(!empty($product)){
			$products_summary = [];
			foreach($product as $_product){
				$summary_order = DB::table('order_product')
					->leftJoin('order','order.order_id','=','order_product.orderproduct_order')
					->whereBetween('order_date',[$datestart,$dateend])
					->where('orderproduct_product',$_product->product_id)
					->sum('orderproduct_price');
				if($summary_order > 0){
					$products_summary[] = [
						'name' => $_product->product_name,
						'amount' => $summary_order
					];
				}
			}
			// Sort and get top 10
			usort($products_summary, function($a, $b) {
				return $b['amount'] <=> $a['amount'];
			});
			$top = array_slice($products_summary, 0, 10);
			foreach($top as $item){
				$top_products[] = $item['name'];
				$top_amounts[] = $item['amount'];
			}
		}

		$data['top_products'] = $top_products;
		$data['top_amounts'] = $top_amounts;

		$employee = DB::table('employee')->get();
		$top_employees = [];
		$top_employees_amounts = [];
				// dd($employee);
		if(!empty($employee)){
			$employee_summary = [];
			foreach($employee as $_employee){
				$summary_order_success = DB::table('order_product')
					->leftJoin('order','order.order_id','=','order_product.orderproduct_order')
					->whereBetween('order_date',[$datestart,$dateend])
					->where('order_employee',$_employee->employee_id)
					->where('order_status','>=','3')
					->sum('orderproduct_price');
				if($summary_order_success > 0){
					$employee_summary[] = [
						'name' => $_employee->employee_name,
						'amount' => $summary_order_success
					];
				}
			}
			usort($employee_summary, function($a, $b) {
				return $b['amount'] <=> $a['amount'];
			});
			$top = array_slice($employee_summary, 0, 10);
			foreach($top as $item){
				$top_employees[] = $item['name'];
				$top_employees_amounts[] = $item['amount'];
			}
		}

		// dd($employee_summary, $top_employees, $top_employees_amounts);

		$data['top_employees'] = $top_employees;
		$data['top_employees_amounts'] = $top_employees_amounts;

        return view('dashboard/dashboard',$data);
    }

    public function dailySummary(Request $request)
	{
	    $currentMonth = Carbon::now()->format('Y-m');
	    $daysInMonth = Carbon::now()->daysInMonth;

	    $data = [];
	    $labels = [];

	    for ($day = 1; $day <= $daysInMonth; $day++) {
	        $date = Carbon::createFromFormat('Y-m-d', "$currentMonth-" . str_pad($day, 2, '0', STR_PAD_LEFT))->format('Y-m-d');

	        $total = DB::table('order')
	            ->whereDate('order_date', $date)
	            ->sum('order_total');
	        if($total > 0){
	        	$labels[] = $date;
		        $data[] = $total;
	        }
	    }

	    return response()->json([
	        'labels' => $labels,
	        'data' => $data,
	    ]);
	}
}