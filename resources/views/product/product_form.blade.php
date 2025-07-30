@extends('../layout/' . $layout)

@section('subhead')
    <title>สินค้าและบริการใหม่ - AMR AIR</title>
@endsection

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        @if(!empty($product))
            <h2 class="text-lg font-medium mr-auto">แก้ไขสินค้าและบริการ</h2>
        @else
            <h2 class="text-lg font-medium mr-auto">สร้างสินค้าและบริการใหม่</h2>
        @endif
    </div>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 lg:col-span-6">
            <form method="post" action="{{ url('save_product') }}" >
                @csrf
                <input type="hidden" name="product_id" value="{{ (!empty($product) ? $product->product_id : '') }}">
                <!-- BEGIN: Form Layout -->
                <div class="intro-y box p-5">
                <label for="crud-form-1" class="form-label" style="font-size: 18px; font-weight: bold;">ข้อมูลลูกค้า</label>
                    <div class="grid grid-cols-12 gap-2 mt-4">

                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-6 2xl:col-span-6">
                            <label>ประเภทสินค้า</label>
                            <div class="mt-2">
                            <input id="crud-form-1" type="text" class="form-control w-full" name="product_type" value="{{ (!empty($product) ? $product->product_type : '') }}" required list="product_type">
                            <datalist id="product_type">
                                @if(!empty($product_type))
                                    @foreach($product_type as $_type)
                                        <option value="{{ $_type->product_type }}">{{ $_type->product_type }}</option>
                                    @endforeach
                                @endif
                            </datalist>
                            </div>
                        </div>

                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-6 2xl:col-span-6">
                            <label>รหัสสินค้า</label>
                            <div class="mt-2">
                            <input id="crud-form-1" type="text" class="form-control w-full" name="product_code" value="{{ (!empty($product) ? $product->product_code : '') }}" required>
                            </div>
                        </div>

                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-6 2xl:col-span-6">
                            <label>ชื่อสินค้า</label>
                            <div class="mt-2">
                            <input id="crud-form-1" type="text" class="form-control w-full" name="product_name" value="{{ (!empty($product) ? $product->product_name : '') }}" required>
                            </div>
                        </div>

                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-6 2xl:col-span-6">
                            <label for="crud-form-1" class="form-label">ราคา</label>
                            <input id="crud-form-1" type="text" class="form-control w-full" name="product_price" value="{{ (!empty($product) ? $product->product_price : '') }}" required>
                        </div>

                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-6 2xl:col-span-6">
                            <label for="crud-form-1" class="form-label">หน่วยสินค้า</label>
                            <input id="crud-form-1" type="text" class="form-control w-full" name="product_unit" value="{{ (!empty($product) ? $product->product_unit : '') }}">
                        </div>

                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-6 2xl:col-span-6">
                            <label for="crud-form-1" class="form-label">สถานะ</label>
                            <select class="form-control w-full" name="product_active">
                                <option value="T" {{ (!empty($product) ? ($product->product_active == 'Y' ? 'selected' : '' ) : '') }} >เปิดใช้งาน</option>
                                <option value="F" {{ (!empty($product) ? ($product->product_active == 'F' ? 'selected' : '' ) : '') }} >ปิดใช้งาน</option>
                            </select>
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
