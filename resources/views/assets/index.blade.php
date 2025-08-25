@extends('../layout/' . $layout)

@section('subhead')
    <title>จัดการสินทรัพย์ - Buildsmooth</title>
    <link rel="stylesheet" href="{{ asset('dist/css/datatables.min.css') }}">
@endsection

@section('subcontent')
    <h2 class="intro-y text-lg font-medium mt-10">รายการสินทรัพย์ทั้งหมด</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <a href="{{ route('assets.create') }}" class="btn btn-primary shadow-md mr-2">เพิ่มสินทรัพย์ใหม่</a>
        </div>
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
            <table class="table table-form datatable -mt-2" style="background-color: white;">
                <thead>
                    <tr>
                        <th class="whitespace-nowrap">ชื่อสินทรัพย์</th>
                        <th class="whitespace-nowrap">รหัส</th>
                        <th class="text-center whitespace-nowrap">สถานะ</th>
                        <th class="text-center whitespace-nowrap">โครงการ</th>
                        <th class="text-center whitespace-nowrap">ผู้ดูแล</th>
                        <th class="text-center whitespace-nowrap">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assets as $asset)
                        <tr class="intro-x">
                            <td>
                                <a href="{{ route('assets.edit', $asset->asset_id) }}" class="font-medium whitespace-nowrap">{{ $asset->asset_name }}</a>
                            </td>
                            <td>{{ $asset->asset_code ?? '-' }}</td>
                            <td class="w-40 text-center">{{ $asset->status }}</td>
                            <td class="text-center">{{ $asset->project->project_name ?? '-' }}</td>
                            <td class="text-center">{{ $asset->assignedUser->first_name.' '.$asset->assignedUser->last_name ?? '-' }}</td>
                            <td class="table-report__action w-56">
                                <div class="flex justify-center items-center">
                                    <a class="flex items-center mr-3" href="{{ route('assets.edit', $asset->asset_id) }}">
                                        <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> แก้ไข
                                    </a>
                                    <a class="flex items-center text-danger" href="javascript:;" data-tw-toggle="modal"
                                        data-tw-target="#delete-confirmation-modal-{{ $asset->asset_id }}">
                                        <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> ลบ
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <!-- BEGIN: Delete Confirmation Modal -->
                        <div id="delete-confirmation-modal-{{ $asset->asset_id }}" class="modal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-body p-0">
                                        <div class="p-5 text-center">
                                            <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                                            <div class="text-3xl mt-5">คุณแน่ใจหรือไม่?</div>
                                            <div class="text-slate-500 mt-2">คุณต้องการลบสินทรัพย์ "{{ $asset->asset_name }}" ใช่หรือไม่?</div>
                                        </div>
                                        <div class="px-5 pb-8 text-center">
                                            <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-24 mr-1">ยกเลิก</button>
                                            <form action="{{ route('assets.destroy', $asset->asset_id) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger w-24">ลบ</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END: Delete Confirmation Modal -->
                    @empty
                        <tr class="intro-x">
                            <td colspan="6" class="text-center">ยังไม่มีข้อมูลสินทรัพย์ในระบบ</td>
                        </tr>
                    @endforelse
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
    });
</script>
