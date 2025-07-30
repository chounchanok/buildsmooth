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

class ReportController extends Controller
{
	public function report_sale()
    {
        $data['employee'] = DB::table('employee')->where('employee_position','LIKE','พนักงานขาย')->get();
        $data['datestart'] = date('Y-m-d').' 00:00:00';
    	$data['dateend'] = date('Y-m-d').' 23:59:59';
    	$data['datestart_show'] = date('Y-m-d');
    	$data['dateend_show'] = date('Y-m-d');
        return view('report/report_sale',$data);
    }

    public function report_sale_date(Request $request){
        $data['employee'] = DB::table('employee')->where('employee_position','LIKE','พนักงานขาย')->get();
    	$data['datestart'] = $request->input('datestart').' 00:00:00';
    	$data['dateend'] = $request->input('dateend').' 23:59:59';
    	$data['datestart_show'] = $request->input('datestart');
    	$data['dateend_show'] = $request->input('dateend');
        return view('report/report_sale',$data);
    }

    public function report_technician()
    {
        $data['employee'] = DB::table('employee')->where('employee_position','LIKE','ช่าง%')->get();
        $data['datestart'] = date('Y-m-d').' 00:00:00';
    	$data['dateend'] = date('Y-m-d').' 23:59:59';
    	$data['datestart_show'] = date('Y-m-d');
    	$data['dateend_show'] = date('Y-m-d');
        return view('report/report_technician',$data);
    }

    public function report_technician_date(Request $request){
        $data['employee'] = DB::table('employee')->where('employee_position','LIKE','ช่าง%')->get();
    	$data['datestart'] = $request->input('datestart').' 00:00:00';
    	$data['dateend'] = $request->input('dateend').' 23:59:59';
    	$data['datestart_show'] = $request->input('datestart');
    	$data['dateend_show'] = $request->input('dateend');
        return view('report/report_technician',$data);
    }

    public function report_product()
    {
        $data['product'] = DB::table('product')->get();
        $data['datestart'] = date('Y-m-d').' 00:00:00';
    	$data['dateend'] = date('Y-m-d').' 23:59:59';
    	$data['datestart_show'] = date('Y-m-d');
    	$data['dateend_show'] = date('Y-m-d');
        return view('report/report_product',$data);
    }

    public function report_product_date(Request $request){
    	$data['product'] = DB::table('product')->get();
    	$data['datestart'] = $request->input('datestart').' 00:00:00';
    	$data['dateend'] = $request->input('dateend').' 23:59:59';
    	$data['datestart_show'] = $request->input('datestart');
    	$data['dateend_show'] = $request->input('dateend');
        return view('report/report_product',$data);
    }
}