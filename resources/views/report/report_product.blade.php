@extends('../layout/' . $layout)

@section('subhead')
    <title>รายงานการขายสินค้า - AMC AIR</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('dist/css/datatables.min.css') }}">
@endsection

@section('subcontent')
    <h2 class="intro-y text-lg font-medium mt-10">รายงานการขายสินค้า</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">

        <form method="POST" action="{{ url('report_product') }}" class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            @csrf
            <input type="date" class="form-control col-span-5 sm:col-span-5 md:col-span-5 2xl:col-span-5" name="datestart" value="{{ $datestart_show }}">
            <input type="date" class="form-control col-span-5 sm:col-span-5 md:col-span-5 2xl:col-span-5" name="dateend" value="{{ $dateend_show }}">
            <button type="submit" class="btn btn-success col-span-1 sm:col-span-1 md:col-span-1 2xl:col-span-1">ค้นหา</button>

            <div class="dropdown col-span-1 sm:col-span-1 md:col-span-1 2xl:col-span-1">
                <button type="button" class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">
                    <span class="w-5 h-5 flex items-center justify-center">
                        <i class="w-4 h-4" data-lucide="plus"></i>
                    </span>
                </button>
                <div class="dropdown-menu w-40">
                    <ul class="dropdown-content">
                        <li>
                            <a href="#" class="dropdown-item" onclick="exportTableToExcel('reportTable', 'Technician Report {{ $datestart_show }} to {{$dateend_show }}')">
                                <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export to Excel
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </form>
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
            <table id="reportTable" class="table table-form datatable -mt-2" style="background-color: white;">
                <thead>
                    <tr>
                        <th class="text-center" >#</th>
                        <th >ประเภท</th>
                        <th >สินค้าและบริการ</th>
                        <th >ยอดรวม</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    @if(!empty($product))
                        @foreach ($product as $_product)
                            <?php 
                                $summary_order = DB::table('order_product')
                                                ->leftjoin('order','order.order_id','=','order_product.orderproduct_order')
                                                ->whereBetween('order_date',[$datestart,$dateend])
                                                ->where('orderproduct_product',$_product->product_id)
                                                ->sum('orderproduct_price');
                            ?>
                            <tr class="intro-x">
                                <td class="text-center">{{ $i }}</td>
                                <td>{{ $_product->product_type }}</td>
                                <td>{{ $_product->product_name }}</td>
                                <td class="w-40">
                                    {{ number_format($summary_order,2,'.',',') }}
                                </td>

                            </tr>
                            <?php $i++; ?>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        <!-- END: Data List -->
    </div>
@endsection

@section('script')
@endsection
<script src="https://code.jquery.com/jquery-1.12.4.js" integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU=" crossorigin="anonymous"></script>
<script src="{{ asset('dist/js/datatables.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        new DataTable('.datatable');
    });

    function exportTableToExcel(tableID, filename = '') {
        let table = $('#'+tableID).DataTable(); // อ้างอิง DataTable
        let data = table.rows({ search: 'applied' }).data().toArray(); // ดึงข้อมูลทั้งหมด

        let headers = [];
        $('#' + tableID + ' thead tr th').each(function () {
            headers.push($(this).text().trim()); // ดึงชื่อหัวข้อของตาราง
        });

        let rows = [headers]; // เริ่มต้นด้วย headers
        data.forEach(row => {
            let rowData = [];
            for (let key in row) {
                rowData.push(row[key]); // ดึงค่าของแต่ละ column
            }
            rows.push(rowData);
        });

        let ws = XLSX.utils.aoa_to_sheet(rows);
        let wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

        filename = filename ? filename + ".xlsx" : "export.xlsx";
        XLSX.writeFile(wb, filename);
    }
</script>
