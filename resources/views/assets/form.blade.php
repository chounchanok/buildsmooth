@extends('../layout/' . $layout)

@section('subhead')
    <title>{{ isset($asset) ? 'แก้ไขสินทรัพย์' : 'เพิ่มสินทรัพย์ใหม่' }} - Buildsmooth</title>
@endsection

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            {{ isset($asset) ? 'แก้ไขข้อมูลสินทรัพย์' : 'เพิ่มสินทรัพย์ใหม่' }}
        </h2>
    </div>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12">
            <!-- BEGIN: Form Layout -->
            <form
                action="{{ isset($asset) ? route('assets.update', $asset->asset_id) : route('assets.store') }}"
                method="POST"
                enctype="multipart/form-data">
                @csrf
                @if (isset($asset))
                    @method('PUT')
                @endif

                <div class="intro-y box p-5">
                    <div class="grid grid-cols-12 gap-x-5">
                        <div class="col-span-12 xl:col-span-8">
                            <label for="asset_name" class="form-label">ชื่อสินทรัพย์</label>
                            <input id="asset_name" name="asset_name" type="text" class="form-control w-full"
                                placeholder="เช่น รถแบคโฮ CAT-01" value="{{ old('asset_name', $asset->asset_name ?? '') }}" required>
                        </div>
                        <div class="col-span-12 xl:col-span-4 mt-3 xl:mt-0">
                            <label for="asset_code" class="form-label">รหัสสินทรัพย์ (ถ้ามี)</label>
                            <input id="asset_code" name="asset_code" type="text" class="form-control w-full"
                                placeholder="เช่น BK-CAT-001" value="{{ old('asset_code', $asset->asset_code ?? '') }}">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="description" class="form-label">รายละเอียด</label>
                        <textarea id="description" name="description" class="form-control w-full"
                            placeholder="รายละเอียดเพิ่มเติมเกี่ยวกับสินทรัพย์">{{ old('description', $asset->description ?? '') }}</textarea>
                    </div>

                    <div class="grid grid-cols-12 gap-x-5 mt-3">
                        <div class="col-span-12 xl:col-span-6">
                            <label for="start_date" class="form-label">วันที่เริ่มใช้งาน</label>
                            <input id="start_date" name="start_date" type="date" class="form-control w-full"
                                value="{{ old('start_date', isset($asset) ? optional($asset->start_date)->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-span-12 xl:col-span-6 mt-3 xl:mt-0">
                            <label for="end_date" class="form-label">วันที่สิ้นสุดการใช้งาน</label>
                            <input id="end_date" name="end_date" type="date" class="form-control w-full"
                                value="{{ old('end_date', isset($asset) ? optional($asset->end_date)->format('Y-m-d') : '') }}">
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-x-5 mt-3">
                        <div class="col-span-12 xl:col-span-4">
                            <label for="status" class="form-label">สถานะ</label>
                            <select id="status" name="status" class="form-select w-full">
                                @php
                                    $statuses = ['Available', 'In Use', 'Under Maintenance', 'Retired'];
                                    $currentStatus = old('status', $asset->status ?? 'Available');
                                @endphp
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}" {{ $currentStatus == $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                         <div class="col-span-12 xl:col-span-4 mt-3 xl:mt-0">
                            <label for="project_id" class="form-label">กำหนดให้โครงการ</label>
                            <select id="project_id" name="project_id" class="form-select w-full">
                                <option value="">-- ไม่กำหนด --</option>
                                @php
                                    $currentProject = old('project_id', $asset->project_id ?? '');
                                @endphp
                                @foreach ($projects as $project)
                                    <option value="{{ $project->project_id }}" {{ $currentProject == $project->project_id ? 'selected' : '' }}>
                                        {{ $project->project_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <?php //dd($asset->assigned_user, $asset->team_members); ?>
                        <div class="col-span-12 xl:col-span-4 mt-3 xl:mt-0">
                            <label for="assigned_user" class="form-label">ผู้ดูแล</label>
                            <select id="assigned_user" name="assigned_user" class="form-select w-full">
                                <option value="">-- เลือกลูกค้า --</option>
                                @foreach($customerUsers as $user)
                                    <option value="{{ $user->user_id }}" {{ (!empty($asset) ? ($user->user_id == $asset->assigned_user ? 'selected' : '') : '') }}>
                                        {{ $user->first_name }} {{ $user->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <div class="mt-3">                    
                        <label for="team_members" class="form-label">ทีมผู้รับผิดชอบ</label>
                        <select id="team_members" name="team_members" class="form-select w-full">
                            <option value="">-- เลือกลูกค้า --</option>
                            @foreach($teamUsers as $user)
                                <option value="{{ $user->user_id }}" {{ (!empty($asset) ? ($user->user_id == $asset->team_members ? 'selected' : '') : '') }}>
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-3">
                        <div>
                            <label for="images" class="form-label">แนบไฟล์รูปภาพ (jpg/png)</label>
                            <input id="images" name="images[]" type="file" class="form-control" multiple>
                        </div>
                         <div>
                            <label for="documents" class="form-label">แนบไฟล์เอกสาร (pdf)</label>
                            <input id="documents" name="documents[]" type="file" class="form-control" multiple>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="document_detail" class="form-label">คำอธิบายรูปภาพ/เอกสาร</label>
                        <textarea id="document_detail" name="document_detail" class="form-control" placeholder="คำอธิบายเพิ่มเติมเกี่ยวกับไฟล์ที่แนบ">{{ old('document_detail', $asset->document_detail ?? '') }}</textarea>
                    </div>

                    <div class="text-right mt-5">
                        <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary w-24 mr-1">ยกเลิก</a>
                        <button type="submit" class="btn btn-primary w-24">{{ isset($asset) ? 'อัปเดต' : 'บันทึก' }}</button>
                    </div>
                </div>
            </form>
            <!-- END: Form Layout -->
        </div>
    </div>
@endsection

@once
    @push('scripts')
        <script src="//unpkg.com/alpinejs" defer></script>
    @endpush
@endonce
