@extends('../layout/' . $layout)

@section('subhead')
    <title>รายการโครงการทั้งหมด - Build Smooth</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('dist/css/datatables.min.css') }}">
@endsection

@section('subcontent')
    <h2 class="intro-y text-lg font-medium mt-10">รายการโครงการทั้งหมด</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <!-- <a href="{{ url('book_form') }}"><button class="btn btn-primary shadow-md mr-2">เพิ่มนัดหมายใหม่</button></a> -->
            <div class="dropdown">
                <button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">
                    <span class="w-5 h-5 flex items-center justify-center">
                        <i class="w-4 h-4" data-lucide="plus"></i>
                    </span>
                </button>
                <div class="dropdown-menu w-40">
                    <ul class="dropdown-content">
                        <li>
                            <a href="{{ route('projects.create') }}" class="btn btn-primary shadow-md mr-2">เพิ่มโครงการใหม่</a>
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
                        <th class="whitespace-nowrap">ชื่อโครงการ</th>
                        <th class="text-center whitespace-nowrap">สถานะ</th>
                        <th class="text-center whitespace-nowrap">วันที่เริ่ม</th>
                        <th class="text-center whitespace-nowrap">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($projects as $project)
                        <tr class="intro-x">
                            <td>
                                <a href="" class="font-medium whitespace-nowrap">{{ $project->project_name }}</a>
                                <div class="text-slate-500 text-xs whitespace-nowrap mt-0.5">{{ $project->description }}</div>
                            </td>
                            <td class="w-40">
                                @php
                                    $status_class = '';
                                    switch ($project->status) {
                                        case 'In Progress':
                                            $status_class = 'text-warning';
                                            break;
                                        case 'Completed':
                                            $status_class = 'text-success';
                                            break;
                                        case 'On Hold':
                                            $status_class = 'text-slate-500';
                                            break;
                                        case 'Cancelled':
                                            $status_class = 'text-danger';
                                            break;
                                        default: // Not Started
                                            $status_class = 'text-primary';
                                    }
                                @endphp
                                <div class="flex items-center justify-center {{ $status_class }}">
                                    <i data-lucide="check-square" class="w-4 h-4 mr-2"></i> {{ $project->status }}
                                </div>
                            </td>
                            <td class="text-center">{{ date('d/m/Y', strtotime($project->start_date)) }}</td>
                            <td class="table-report__action w-56">
                                <div class="flex justify-center items-center">
                                    <a class="flex items-center mr-3" href="{{ route('projects.edit', $project->project_id) }}">
                                        <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> แก้ไข
                                    </a>
                                    {{-- ปุ่มลบ --}}
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- END: Data List -->
    </div>
    <!-- END: Delete Confirmation Modal -->
@endsection

@section('script')
@endsection
<script src="https://code.jquery.com/jquery-1.12.4.js" integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU=" crossorigin="anonymous"></script>
<script src="{{ asset('dist/js/datatables.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        new DataTable('.datatable');
    });
</script>
