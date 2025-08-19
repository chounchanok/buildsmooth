@extends('../layout/' . $layout)

@section('subhead')
    <title>{{ isset($project) ? 'แก้ไขโครงการ' : 'กรอกรายละเอียดโครงการ' }} - Buildsmooth</title>
@endsection

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            {{ isset($project) ? 'แก้ไขโครงการ' : 'กรอกรายละเอียดโครงการ' }}
        </h2>
    </div>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12">
            <form
                action="{{ isset($project) ? route('projects.update', $project->project_id) : route('projects.store') }}"
                method="POST"
                enctype="multipart/form-data"
                x-data="{
                    projectType: '{{ old('project_type', $project->project_type ?? 'โครงการ') }}',
                    progress: {{ old('progress', $project->progress ?? 10) }},
                    teamMembers: {{ json_encode(old('team_members', $project->team_members ?? [''])) }},
                    customerContacts: {{ json_encode(old('customer_contacts', $project->customer_contacts ?? [''])) }}
                }">
                @csrf
                @if (isset($project))
                    @method('PUT')
                @endif

                <div class="intro-y box p-5">
                    {{-- ประเภทโครงการ --}}
                    <div>
                        <label class="form-label">ประเภทโครงการ</label>
                        <div class="flex flex-col sm:flex-row mt-2">
                            <div class="form-check mr-4">
                                <input id="type-project" class="form-check-input" type="radio" name="project_type" value="โครงการ" x-model="projectType" {{ old('project_type', $project->project_type ?? 'โครงการ') == 'โครงการ' ? 'checked' : '' }}>
                                <label class="form-check-label" for="type-project">งานโครงการ</label>
                            </div>
                            <div class="form-check mr-4 mt-2 sm:mt-0">
                                {{-- เพิ่ม checked condition ที่นี่ --}}
                                <input id="type-house" class="form-check-input" type="radio" name="project_type" value="บ้าน" x-model="projectType" {{ old('project_type', $project->project_type ?? '') == 'บ้าน' ? 'checked' : '' }}>
                                <label class="form-check-label" for="type-house">บ้าน</label>
                            </div>
                            <div class="form-check mr-2 mt-2 sm:mt-0">
                                {{-- เพิ่ม checked condition ที่นี่ --}}
                                <input id="type-other" class="form-check-input" type="radio" name="project_type" value="อื่นๆ" x-model="projectType" {{ old('project_type', $project->project_type ?? '') == 'อื่นๆ' ? 'checked' : '' }}>
                                <label class="form-check-label" for="type-other">อื่นๆ (โปรดระบุ)</label>
                            </div>
                        </div>
                        <div x-show="projectType === 'อื่นๆ'" x-transition class="mt-2">
                            <input id="project_type_other" name="project_type_other" type="text" class="form-control" placeholder="ระบุประเภทโครงการ" value="{{ old('project_type_other', $project->project_type_other ?? '') }}">
                        </div>
                    </div>


                    {{-- รหัสโครงการ, อ้างอิง, PO --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                        <div>
                            <label for="project_code" class="form-label">รหัสโครงการ</label>
                            <input id="project_code" name="project_code" type="text" class="form-control" placeholder="SR-E001" value="{{ old('project_code', $project->project_code ?? '') }}">
                        </div>
                        <div>
                            <label for="reference_code" class="form-label">รหัสอ้างอิง (ถ้ามี)</label>
                            <input id="reference_code" name="reference_code" type="text" class="form-control" placeholder="SD105" value="{{ old('reference_code', $project->reference_code ?? '') }}">
                        </div>
                        <div>
                            <label for="po_number" class="form-label">รหัสใบ PO</label>
                            <input id="po_number" name="po_number" type="text" class="form-control" placeholder="กรอกรหัสใบ PO" value="{{ old('po_number', $project->po_number ?? '') }}">
                        </div>
                    </div>

                    {{-- ชื่อโครงการ / บ้าน / อื่นๆ --}}
                    <div class="mt-3">
                        <label for="project_name" class="form-label">ชื่อโครงการ / บ้าน / อื่นๆ</label>
                        <input id="project_name" name="project_name" type="text" class="form-control" placeholder="Sนภา วิลเลจ" value="{{ old('project_name', $project->project_name ?? '') }}">
                    </div>

                    {{-- สถานที่ตั้ง --}}
                    <div class="mt-3">
                        <label for="location_address" class="form-label">สถานที่ตั้ง</label>
                        <textarea id="location_address" name="location_address" class="form-control" placeholder="กรอกที่อยู่">{{ old('location_address', $project->location_address ?? '') }}</textarea>
                    </div>
                    <div class="mt-3">
                        <label for="location_map_link" class="form-label">แนบลิงค์ Google map</label>
                        <input id="location_map_link" name="location_map_link" type="url" class="form-control" placeholder="https://maps.app.goo.gl/..." value="{{ old('location_map_link', $project->location_map_link ?? '') }}">
                    </div>

                    {{-- Subscribed --}}
                    <div class="form-check mt-3">
                        <input id="is_subscribed" name="is_subscribed" class="form-check-input" type="checkbox" value="1" {{ old('is_subscribed', $project->is_subscribed ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_subscribed">Subscribed (รับการแจ้งเตือนเมื่อมีการอัปเดต)</label>
                    </div>

                    {{-- ทีมงาน --}}
                    <div class="mt-3">
                        <label class="form-label">ทีมงาน</label>
                        <template x-for="(memberId, index) in teamMembers" :key="index">
                            <div class="flex items-center mt-2">
                                <select :name="'team_members[' + index + ']'" class="form-select" x-model="teamMembers[index]">
                                    <option value="">-- เลือกสมาชิกทีมงาน --</option>
                                    @foreach($teamUsers as $user)
                                        <option value="{{ $user->user_id }}">{{ $user->first_name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-danger ml-2" @click="teamMembers.splice(index, 1)" x-show="teamMembers.length > 1">ลบ</button>
                            </div>
                        </template>
                        <button type="button" class="btn btn-outline-secondary mt-2" @click="teamMembers.push('')">เพิ่มสมาชิก</button>
                    </div>

                    {{-- ชื่อลูกค้า --}}
                    <div class="mt-3">
                        <label class="form-label">ชื่อลูกค้า</label>
                        <template x-for="(contactId, index) in customerContacts" :key="index">
                             <div class="flex items-center mt-2">
                                <select :name="'customer_contacts[' + index + ']'" class="form-select" x-model="customerContacts[index]">
                                    <option value="">-- เลือกลูกค้า --</option>
                                    @foreach($customerUsers as $user)
                                        <option value="{{ $user->user_id }}">{{ $user->first_name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-danger ml-2" @click="customerContacts.splice(index, 1)" x-show="customerContacts.length > 1">ลบ</button>
                            </div>
                        </template>
                        <button type="button" class="btn btn-outline-secondary mt-2" @click="customerContacts.push('')">เพิ่มสมาชิก</button>
                    </div>

                    {{-- วันที่และ Progress --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                        <div>
                            <label for="start_date" class="form-label">วันที่เริ่มงาน</label>
                            <input id="start_date" name="start_date" type="date" class="form-control" value="{{ old('start_date', isset($project) ? optional($project->start_date)->format('Y-m-d') : '') }}">
                        </div>
                        <div>
                            <label for="end_date" class="form-label">วันที่สิ้นสุดงาน</label>
                            <input id="end_date" name="end_date" type="date" class="form-control" value="{{ old('end_date', isset($project) ? optional($project->end_date)->format('Y-m-d') : '') }}">
                        </div>
                        <div>
                            <label for="progress" class="form-label">Process ความก้าวหน้า (%)</label>
                            <div class="flex items-center">
                                <input id="progress" type="range" name="progress" class="form-range w-full" min="0" max="100" x-model="progress">
                                <span class="ml-3 w-12 text-center" x-text="progress + '%'"></span>
                            </div>
                        </div>
                    </div>

                     {{-- รายละเอียดงาน --}}
                    <div class="mt-3">
                        <label for="description" class="form-label">รายละเอียดงาน</label>
                        <textarea id="description" name="description" class="form-control" placeholder="กรอกรายละเอียด">{{ old('description', $project->description ?? '') }}</textarea>
                    </div>
                    
                    {{-- แนบไฟล์ --}}
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

                    {{-- คำอธิบายรูปภาพ --}}
                    <div class="mt-3">
                        <label for="image_description" class="form-label">คำอธิบายรูปภาพ</label>
                        <textarea id="image_description" name="image_description" class="form-control" placeholder="กรอกคำอธิบายรูปภาพ">{{ old('image_description', $project->image_description ?? '') }}</textarea>
                    </div>

                    <div class="text-right mt-5">
                        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary w-24 mr-1">ยกเลิก</a>
                        <button type="submit" class="btn btn-primary w-24">{{ isset($project) ? 'อัปเดต' : 'บันทึกรายการ' }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@once
    @push('scripts')
        <script src="//unpkg.com/alpinejs" defer></script>
    @endpush
@endonce
