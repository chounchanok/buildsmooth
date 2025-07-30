@extends('../layout/' . $layout)

@section('subhead')
    <title>พนักงานทั้งหมด - Build Smooth</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('dist/css/datatables.min.css') }}">
@endsection

@section('subcontent')
    <h2 class="intro-y text-lg font-medium mt-10">พนักงานทั้งหมด</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <a href="{{ url('employee_form') }}"><button class="btn btn-primary shadow-md mr-2">เพิ่มพนักงานใหม่</button></a>
            <div class="dropdown">
                <button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">
                    <span class="w-5 h-5 flex items-center justify-center">
                        <i class="w-4 h-4" data-lucide="plus"></i>
                    </span>
                </button>
                <div class="dropdown-menu w-40">
                    <ul class="dropdown-content">
                        <li>
                            <a href="" class="dropdown-item">
                                <i data-lucide="printer" class="w-4 h-4 mr-2"></i> Print
                            </a>
                        </li>
                        <li>
                            <a href="" class="dropdown-item">
                                <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export to Excel
                            </a>
                        </li>
                        <li>
                            <a href="" class="dropdown-item">
                                <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export to PDF
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
            <table class="table table-form datatable -mt-2" style="background-color: white;">
                <thead>
                    <tr>
                        <th class="text-center" >#</th>
                        <th >ตำแหน่ง</th>
                        <th >ชื่อ</th>
                        <th >เบอร์โทร</th>
                        <th >จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    @if(!empty($employee))
                        @foreach ($employee as $_employee)
                            <tr class="intro-x">
                                <td class="text-center">{{ $i }}</td>
                                <td>{{ $_employee->employee_position }}</td>
                                <td>{{ $_employee->employee_name }}</td>
                                <td>{{ $_employee->employee_tel }}</td>
                                <td class="table-report__action w-56">
                                    <div class="flex">
                                        <a class="flex items-center mr-3" href="{{ url('employee_edit') }}/{{ $_employee->employee_id }}">
                                            <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> แก้ไข
                                        </a>
                                        <a class="flex items-center text-danger" href="javascript:;" data-tw-toggle="modal" ref="{{ $_employee->employee_id }}">
                                        <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> ลบ
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
<script type="text/javascript">
    $(document).ready(function(){
        new DataTable('.datatable');
    });

    $(document).on('click', 'a[data-tw-toggle="modal"]', function () {
            var employee_id = $(this).attr('ref');
            console.log(employee_id);

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to delete this order?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, change it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: '{{ url("delete_employee") }}/' + employee_id,
                        type: 'GET',
                        success: function (response) {
                            alert('Order deleted successfully!');
                            $('#delete-confirmation-modal').modal('hide');
                            location.reload();
                        },
                        error: function (xhr, status, error) {
                            alert('Error occurred while deleting the order.');
                        }
                    });
                } else {
                    // ถ้าผู้ใช้กด Cancel
                    console.log('User canceled the status change');
                }
            });
        });
</script>
