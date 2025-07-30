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
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Imports\ProductsImport;
use App\Exports\ProductExport;

class ProductController extends Controller
{

    public function product_list()
    {
        $data['product'] = DB::table('product')->whereNull('product_delete')->get();
        return view('product/product_list',$data);
    }

    public function product_form()
    {
        $data['product_type'] = DB::table('product')->select('product_type')->groupBy('product_type')->get();
        return view('product/product_form',$data);
    }

    public function product_edit($id)
    {
        $data['product_type'] = DB::table('product')->select('product_type')->groupBy('product_type')->get();
        $data['product'] = DB::table('product')->where('product_id',$id)->first();
        return view('product/product_form',$data);
    }

    public function save_product(Request $request)
    {
        $data['product_code'] = $request->input('product_code');
        $data['product_type'] = $request->input('product_type');
        $data['product_name'] = $request->input('product_name');
        $data['product_price'] = $request->input('product_price');
        $data['product_unit'] = $request->input('product_unit');
        $data['product_active'] = $request->input('product_active');
        $data['product_delete'] = NULL;

        if(!empty($request->input('product_id'))){
            DB::table('product')->where('product_id',$request->input('product_id'))->update($data);
        }else{
            DB::table('product')->insert($data);
        }

        $product_now = DB::table('product')->where('product_code',$request->input('product_code'))->first();
        return redirect('product_list');
    }

    public function export_products(Request $request)
    {
        return Excel::download(new ProductExport, 'products.xlsx');
    }

    public function product_delete($id){
        $data['product_delete'] = date('Y-m-d H:i:s');
        DB::table('product')->where('product_id',$id)->update($data);
        return redirect('product_list');
    }

    public function product_upload(Request $request)
    {
        $request->validate([
            'product_upload' => 'required|file|mimes:xlsx,xls'
        ]);

        $file = $request->file('product_upload');
        $data = Excel::toArray([], $file);

        foreach ($data[0] as $key => $row) {
            if($key > 3){
                // สมมุติว่า columns เรียง: [product_code, product_type, product_name, product_price, product_active]
                $product_code = $row[1] ?? null;
                $product_type = $row[4] ?? null;
                $product_name = $row[2] ?? null;
                $product_unit = $row[3] ?? null;
                $product_price = isset($row[6]) ? floatval($row[6]) : 0.0;
                // $product_active = $row[1] ?? 'F';
                $product_active = 'T';

                if (!$product_code || !$product_name) {
                    continue; // ข้ามถ้ามีค่าว่างใน field สำคัญ
                }

                $existing = DB::table('product')
                    ->where('product_code', $product_code)
                    ->where('product_type', $product_type)
                    ->where('product_name', $product_name)
                    ->first();

                $data = [
                    'product_code' => $product_code,
                    'product_type' => $product_type,
                    'product_name' => $product_name,
                    'product_price' => $product_price,
                    'product_unit' => $product_unit,
                    'product_active' => $product_active,
                    'product_delete' => null,
                ];

                if ($existing) {
                    DB::table('product')->where('product_id', $existing->product_id)->update($data);
                } else {
                    DB::table('product')->insert($data);
                }
            }
        }

        return redirect('product_list')->with('success', 'อัปโหลดสำเร็จ');
    }

}
