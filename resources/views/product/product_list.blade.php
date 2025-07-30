@extends('../layout/' . $layout)

@section('subhead')
    <title>สินค้าและบริการทั้งหมด - AMC AIR</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('dist/css/datatables.min.css') }}">
@endsection

@section('subcontent')
    <h2 class="intro-y text-lg font-medium mt-10">สินค้าและบริการทั้งหมด</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <a href="{{ url('product_form') }}"><button class="btn btn-primary shadow-md mr-2">เพิ่มสินค้าและบริการใหม่</button></a>
            <button type="button" class="btn btn-primary shadow-md mr-2 button_import">นำเข้าสินค้า</button>

            <div class="dropdown">
                <button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">
                    <span class="w-5 h-5 flex items-center justify-center">
                        <i class="w-4 h-4" data-lucide="plus"></i>
                    </span>
                </button>
                <div class="dropdown-menu w-40">
                    <ul class="dropdown-content">
                        <li>
                            <a href="{{ url('export_products') }}" class="dropdown-item">
                                <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export to Excel
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <form action="{{ url('product_upload') }}" id="form_upload" method="post" enctype="multipart/form-data">
                @csrf
                <input type="file" id="product_upload" name="product_upload" class="form-control" style="display: none;">
            </form>
        </div>
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
            <table class="table table-form datatable -mt-2" style="background-color: white;">
                <thead>
                    <tr>
                        <th class="text-center" >#</th>
                        <th >ประเภท</th>
                        <th >รหัสสินค้า</th>
                        <th >สินค้าและบริการ</th>
                        <th >ราคา</th>
                        <th >หน่วย</th>
                        <th >สถานะ</th>
                        <th >จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    @if(!empty($product))
                        @foreach ($product as $_product)
                            <tr class="intro-x">
                                <td class="text-center">{{ $i }}</td>
                                <td>{{ $_product->product_type }}</td>
                                <td>{{ $_product->product_code }}</td>
                                <td>
                                    <a href="" class="font-medium whitespace-nowrap">{{ $_product->product_name }}</a>
                                </td>
                                <td>{{ $_product->product_price }}</td>
                                <td>{{ $_product->product_unit }}</td>
                                <td class="w-40">
                                    <div class="flex {{ $_product->product_active == 'T' ? 'text-success' : 'text-danger' }}">
                                        <i data-lucide="check-square" class="w-4 h-4 mr-2"></i> {{ $_product->product_active == 'T' ? 'Active' : 'Inactive' }}
                                    </div>
                                </td>
                                <td class="table-report__action w-56">
                                    <div class="flex justify-center items-center">
                                        <a class="flex items-center text-warning mr-3" href="{{ url('product_edit') }}/{{ $_product->product_id }}">
                                            <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Edit
                                        </a>
                                        <a class="flex items-center text-danger" href="javascript:;" data-tw-toggle="modal" ref="{{ $_product->product_id }}">
                                            <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Delete
                                        </a>
                                    </div>
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
<script type="text/javascript">
    $(document).ready(function(){
        new DataTable('.datatable');

        $('.button_import').click(function(){
            $('#product_upload').click();
        });

        $('#product_upload').change(function(){
            if(confirm('ยืนยันการอัพโหลดไฟล์ ?')){
                $('#form_upload').submit();
            }
        });

        $(document).on('click', 'a[data-tw-toggle="modal"]', function () {
            var product_id = $(this).attr('ref');
            console.log(product_id);

            if (confirm('ยืนยันการลบสินค้าชิ้นนี้ ?')) {

                $.ajax({
                    url: '{{ url("product_delete") }}/' + product_id,
                    type: 'GET',
                    success: function (response) {
                        alert('Product deleted successfully!');
                        location.reload();
                    },
                    error: function (xhr, status, error) {
                        alert('Error occurred while deleting the order.');
                    }
                });
            } else {
                // ถ้าผู้ใช้กด Cancel
                console.log('User canceled the status delete');
            }
        });
    });
</script>
