@extends('../layout/' . $layout)

@section('subhead')
    <title>พนักงานใหม่ - AMR AIR</title>
@endsection

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        @if(!empty($employee))
        <h2 class="text-lg font-medium mr-auto">แก้ไขข้อมูลพนักงาน</h2>
        @else
        <h2 class="text-lg font-medium mr-auto">สร้างพนักงานใหม่</h2>
        @endif
    </div>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 lg:col-span-6">
            <form method="post" action="{{ url('save_employee') }}" autocomplete="off">
                @csrf
                <input type="hidden" name="employee_id" value="{{ (!empty($employee) ? $employee->employee_id : '') }}">
                <!-- BEGIN: Form Layout -->
                <div class="intro-y box p-5">
                <label for="crud-form-1" class="form-label" style="font-size: 18px; font-weight: bold;">ข้อมูลพนักงาน</label>
                    <div class="grid grid-cols-12 gap-2 mt-4">

                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-6 2xl:col-span-6">
                            <label>ตำแหน่ง</label>
                            <div class="mt-2">
                            <input id="crud-form-1" type="text" class="form-control w-full" name="employee_position" value="{{ (!empty($employee) ? $employee->employee_position : '') }}" required list="employee_position">
                            <datalist id="employee_position">
                                @if(!empty($employee_position))
                                    @foreach($employee_position as $_position)
                                        <option value="{{ $_position->position_name }}">{{ $_position->position_name }}</option>
                                    @endforeach
                                @endif
                            </datalist>
                            </div>
                        </div>

                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-6 2xl:col-span-6">
                            <label>ชื่อพนักงาน</label>
                            <div class="mt-2">
                            <input id="crud-form-1" type="text" class="form-control w-full" name="employee_name" value="{{ (!empty($employee) ? $employee->employee_name : '') }}" required>
                            </div>
                        </div>

                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-6 2xl:col-span-6">
                            <label for="crud-form-1" class="form-label">เบอร์โทร</label>
                            <input id="crud-form-1" type="text" class="form-control w-full" name="employee_tel" value="{{ (!empty($employee) ? $employee->employee_tel : '') }}" maxlength="10" required>
                        </div>

                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-6 2xl:col-span-6">
                            <label for="crud-form-1" class="form-label">สถานะ</label>
                            <select class="form-control w-full" name="employee_active">
                                <option value="T" {{ (!empty($employee) ? ($employee->employee_active == 'T' ? 'selected' : '' ) : '') }} >เปิดใช้งาน</option>
                                <option value="F" {{ (!empty($employee) ? ($employee->employee_active == 'F' ? 'selected' : '' ) : '') }} >ปิดใช้งาน</option>
                            </select>
                        </div>

                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-6 2xl:col-span-6">
                            <label>รหัสพนักงาน</label>
                            <div class="mt-2">
                            <input id="crud-form-1" type="text" class="form-control w-full" name="employee_code" value="{{ (!empty($employee) ? $employee->employee_code : '') }}" required autocomplete="off">
                            </div>
                        </div>

                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-6 2xl:col-span-6">
                            <label>รหัสผ่าน</label>
                            <div class="mt-2">
                            @if(!empty($employee))
                            <input id="crud-form-1" type="password" class="form-control w-full" name="employee_password" autocomplete="off">
                            @else
                            <input id="crud-form-1" type="password" class="form-control w-full" name="employee_password" required autocomplete="off">
                            @endif
                            </div>
                        </div>

                    </div>
                    <div class="text-right mt-5">
                        <button type="reset" class="btn btn-outline-secondary w-24 mr-1">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary w-24">บันทึก</button>
                    </div>
                </div>
            <!-- END: Form Layout -->
            </form>
        </div>

        
    </div>
@endsection

@section('script')
    <script src="{{ mix('dist/js/ckeditor-classic.js') }}"></script>
    <script src="https://code.jquery.com/jquery-1.12.4.js" integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU=" crossorigin="anonymous"></script>
    <script type="text/javascript">
            
    </script>
@endsection
