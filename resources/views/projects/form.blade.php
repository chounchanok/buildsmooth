@extends('../layout/' . $layout)

@section('subhead')
    {{-- เช็คว่าเป็นการสร้างหรือแก้ไขเพื่อเปลี่ยน Title --}}
    <title>{{ isset($project) ? 'แก้ไขโครงการ' : 'เพิ่มโครงการใหม่' }} - Buildsmooth</title>
@endsection

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            {{ isset($project) ? 'แก้ไขโครงการ' : 'เพิ่มโครงการใหม่' }}
        </h2>
    </div>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12">
            {{-- กำหนด action และ method ของฟอร์มแบบไดนามิก --}}
            <form
                action="{{ isset($project) ? route('projects.update', $project->project_id) : route('projects.store') }}"
                method="POST">
                @csrf
                {{-- หากเป็นการแก้ไข ให้เพิ่ม method PUT --}}
                @if (isset($project))
                    @method('PUT')
                @endif

                <div class="intro-y box p-5">
                    <div>
                        <label for="project_name" class="form-label">ชื่อโครงการ</label>
                        <input id="project_name" name="project_name" type="text" class="form-control w-full"
                            placeholder="เช่น โครงการคอนโด The Grand Buildsmoot"
                            value="{{ old('project_name', $project->project_name ?? '') }}" required>
                    </div>

                    <div class="mt-3">
                        <label for="description" class="form-label">รายละเอียดโครงการ</label>
                        <textarea id="description" name="description" class="form-control w-full"
                            placeholder="รายละเอียดเพิ่มเติมเกี่ยวกับโครงการ">{{ old('description', $project->description ?? '') }}</textarea>
                    </div>

                    <div class="mt-3">
                        <label for="address" class="form-label">ที่อยู่ / สถานที่ตั้ง</label>
                        <textarea id="address" name="address" class="form-control w-full"
                            placeholder="123 ถ.สุขุมวิท กรุงเทพฯ">{{ old('address', $project->address ?? '') }}</textarea>
                    </div>

                    <div class="grid grid-cols-12 gap-x-5 mt-3">
                        <div class="col-span-12 xl:col-span-4">
                            <label for="start_date" class="form-label">วันที่เริ่มโครงการ</label>
                            <input id="start_date" name="start_date" type="date" class="form-control w-full"
                                value="{{ old('start_date', isset($project) ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-span-12 xl:col-span-4 mt-3 xl:mt-0">
                            <label for="end_date" class="form-label">วันที่สิ้นสุดโครงการ</label>
                            <input id="end_date" name="end_date" type="date" class="form-control w-full"
                                value="{{ old('end_date', isset($project) ? \Carbon\Carbon::parse($project->end_date)->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-span-12 xl:col-span-4 mt-3 xl:mt-0">
                            <label for="status" class="form-label">สถานะ</label>
                            <select id="status" name="status" class="form-select w-full">
                                @php
                                    $statuses = ['Not Started', 'In Progress', 'Completed', 'On Hold', 'Cancelled'];
                                    $currentStatus = old('status', $project->status ?? 'Not Started');
                                @endphp
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}" {{ $currentStatus == $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="text-right mt-5">
                        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary w-24 mr-1">ยกเลิก</a>
                        <button type="submit" class="btn btn-primary w-24">{{ isset($project) ? 'อัปเดต' : 'บันทึก' }}</button>
                    </div>
                </div>
            </form>
            </div>
    </div>
@endsection
