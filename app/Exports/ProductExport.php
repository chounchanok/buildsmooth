<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class ProductExport implements FromView
{
    public function view(): View
    {
        $products = DB::table('product')->whereNull('product_delete')->get();

        return view('exports.product_excel', [
            'products' => $products
        ]);
    }
}
