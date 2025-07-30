@extends('../layout/' . $layout)

@section('subhead')
    @if($order->order_status != 1)
    <title>แก้นัดหมาย - AMR AIR</title>
    @else
    <title>นัดหมายใหม่ - AMR AIR</title>
    @endif
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
        .gj-icon {
            margin-top: 7px; 
            margin-right: 5px;
        }
    </style>
@endsection

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
    @if($order->order_status != 1)
        <h2 class="text-lg font-medium mr-auto">แก้ไขนัดหมาย</h2>
    @else
        <h2 class="text-lg font-medium mr-auto">สร้างนัดหมายใหม่</h2>
    @endif
    </div>
    <form method="post" action="{{ url('save_order') }}" autocomplete="off" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-12 lg:col-span-6">
                <!-- BEGIN: Form Layout -->
                <div class="intro-y box p-5">
                    <div class="grid grid-cols-12 gap-4 mt-4">
                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-6 2xl:col-span-6">
                            <label for="crud-form-1" class="form-label">ชื่อลูกค้า</label>
                            <input id="crud-form-1" type="text" class="form-control w-full" name="booking_name" value="{{ $customer->customer_firstname.' '.$customer->customer_lastname }}" readonly>
                        </div>
                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-6 2xl:col-span-6">
                            <label>สถานะใบงาน</label>
                            <div class="form-switch mt-2">
                                <select class="form form-control" name="order_status">
                                    <option {{ ($order->order_status == '1' ? 'selected' : '' ) }} value="1">{{ 'รอนัดหมาย' }}</option>
                                    <option {{ ($order->order_status == '2' ? 'selected' : '' ) }} value="2">{{ 'รอดำเนินการ' }}</option>
                                    <option {{ ($order->order_status == '3' ? 'selected' : '' ) }} value="3">{{ 'รอชำระเงิน' }}</option>
                                    <option {{ ($order->order_status == '4' ? 'selected' : '' ) }} value="4">{{ 'ดำเนินการเสร็จสิ้น' }}</option>
                                    <option {{ ($order->order_status == '5' ? 'selected' : '' ) }} value="5">{{ 'ยกเลิกออเดอร์' }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-12 2xl:col-span-12">
                            <label for="crud-form-1" class="form-label">ชื่อบริษัท</label>
                            <input type="text" class="form-control w-full" name="customer_company" id="customer_company" value="{{ (!empty($customer) ? $customer->customer_company : '') }}">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">รายละเอียดใบงาน</label>
                        @if(!empty($order_product))
                            @foreach($order_product as $_listproduct)
                            <div class="product-row grid grid-cols-12 gap-4 mt-4">
                                <div class="intro-y col-span-12 sm:col-span-12 md:col-span-6 2xl:col-span-6">
                                    <label for="crud-form-1" class="form-label">สินค้า</label>
                                    <select class="form-control service_product select2" name="service_product[]" disabled>
                                        @foreach ($product as $_product)
                                        <option value="{{ $_product->product_id }}" ref="{{ $_product->product_price }}" {{ ($_listproduct->orderproduct_product == $_product->product_id ? 'selected' : '') }}>{{ $_product->product_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="intro-y col-span-12 sm:col-span-12 md:col-span-2 2xl:col-span-2">
                                    <label for="crud-form-1" class="form-label">จำนวน</label>
                                    <input type="number" class="form-control w-full" value="{{ $_listproduct->orderproduct_qty }}" step="0.01" disabled>
                                </div>
                                <div class="intro-y col-span-12 sm:col-span-12 md:col-span-2 2xl:col-span-2">
                                    <label for="crud-form-1" class="form-label">ราคาต่อชิ้น</label>
                                    <input type="number" class="form-control w-full" value="{{ ($_listproduct->orderproduct_price / ($_listproduct->orderproduct_qty > 0 ? $_listproduct->orderproduct_qty : 1)) }}" step="0.01" disabled>
                                </div>
                                <div class="intro-y col-span-12 sm:col-span-12 md:col-span-2 2xl:col-span-2">
                                    <label for="crud-form-1" class="form-label">ราคา</label>
                                    <input type="number" class="form-control w-full product_price" value="{{ $_listproduct->orderproduct_price }}" step="0.01" disabled>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                    <div class="mt-3">
                        <label>หมายเหตุ</label>
                        <div class="mt-2">
                            <textarea class="editor" name="order_comment"><?php echo html_entity_decode(!empty($order) ? $order->order_comment : ''); ?></textarea>
                        </div>
                    </div>
                </div>
                <!-- END: Form Layout -->
            </div>

            <div class="intro-y col-span-12 lg:col-span-6">
                <!-- BEGIN: Form Layout -->
                <div class="intro-y box p-5">
                <label for="crud-form-1" class="form-label" style="font-size: 18px; font-weight: bold;">ข้อมูลบริการเพิ่มเติม</label>
                    <div id="product-container">
                        @if(count($order_product_addon) > 0)
                            @foreach($order_product_addon as $_listproduct)
                            <div class="product-row grid grid-cols-12 gap-4 mt-4">
                                <div class="intro-y col-span-12 sm:col-span-12 md:col-span-6 2xl:col-span-6">
                                    <label for="crud-form-1" class="form-label">สินค้า</label>
                                    <select class="form-control service_product select2" name="service_product[]" disabled>
                                        <option value="">- เลือกสินค้า -</option>
                                        @foreach ($product as $_product)
                                        <option value="{{ $_product->product_id }}" ref="{{ $_product->product_price }}" {{ ($_listproduct->orderproduct_product == $_product->product_id ? 'selected' : '') }}>{{ $_product->product_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="intro-y col-span-12 sm:col-span-12 md:col-span-2 2xl:col-span-2">
                                    <label for="crud-form-1" class="form-label">จำนวน</label>
                                    <input type="number" class="form-control w-full" name="service_qty[]" onkeyup="summary_item()" value="{{ $_listproduct->orderproduct_qty }}" step="0.01" disabled>
                                </div>
                                <div class="intro-y col-span-12 sm:col-span-12 md:col-span-2 2xl:col-span-2">
                                    <label for="crud-form-1" class="form-label">ราคาต่อชิ้น</label>
                                    <input type="number" class="form-control w-full" name="service_qty[]" onkeyup="summary_item()" value="{{ ($_listproduct->orderproduct_price / ($_listproduct->orderproduct_qty > 0 ? $_listproduct->orderproduct_qty : 1)) }}" step="0.01" disabled>
                                </div>
                                <div class="intro-y col-span-12 sm:col-span-12 md:col-span-2 2xl:col-span-2">
                                    <label for="crud-form-1" class="form-label">รวม</label>
                                    <input type="number" class="form-control w-full product_price" onkeyup="summary()" name="service_price[]" value="{{ $_listproduct->orderproduct_price }}" step="0.01" disabled>
                                </div>
                            </div>
                            @endforeach
                        @else
                        <div class="product-row grid grid-cols-12 gap-4 mt-4">
                            <div class="intro-y col-span-12 sm:col-span-12 md:col-span-12 2xl:col-span-12">
                                <label for="crud-form-1" class="form-label">สินค้า</label>
                                <select class="form-control service_product select2" name="service_product[]">
                                    <option value="">- เลือกสินค้า -</option>
                                    @foreach ($product as $_product)
                                    <option value="{{ $_product->product_id }}" ref="{{ $_product->product_price }}">{{ $_product->product_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="intro-y col-span-12 sm:col-span-12 md:col-span-4 2xl:col-span-4">
                                <label for="crud-form-1" class="form-label">จำนวน</label>
                                <input type="number" class="form-control w-full" name="service_qty[]" onkeyup="summary_item()" step="0.01">
                            </div>
                            <div class="intro-y col-span-12 sm:col-span-12 md:col-span-4 2xl:col-span-4">
                                <label for="crud-form-1" class="form-label">ราคาต่อชิ้น</label>
                                <input type="number" class="form-control w-full" name="service_price_qty[]" onkeyup="summary_item()" step="0.01">
                            </div>
                            <div class="intro-y col-span-12 sm:col-span-12 md:col-span-4 2xl:col-span-4">
                                <label for="crud-form-1" class="form-label">รวม</label>
                                <input type="number" class="form-control w-full product_price" onkeyup="summary()" name="service_price[]" step="0.01">
                            </div>
                        </div>

                        <div id="product-template" class="product-row grid grid-cols-12 gap-4 mt-4 hidden">
                            <div class="intro-y col-span-12 sm:col-span-12 md:col-span-12 2xl:col-span-12">
                                <label for="crud-form-1" class="form-label">สินค้า</label>
                                <select class="form-control service_product" name="service_product[]">
                                    <option value="">- เลือกสินค้า -</option>
                                    @foreach ($product as $_product)
                                    <option value="{{ $_product->product_id }}" ref="{{ $_product->product_price }}">{{ $_product->product_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="intro-y col-span-12 sm:col-span-12 md:col-span-4 2xl:col-span-4">
                                <label for="crud-form-1" class="form-label">จำนวน</label>
                                <input type="number" class="form-control w-full" name="service_qty[]" onkeyup="summary_item()" step="0.01">
                            </div>
                            <div class="intro-y col-span-12 sm:col-span-12 md:col-span-4 2xl:col-span-4">
                                <label for="crud-form-1" class="form-label">ราคาต่อชิ้น</label>
                                <input type="number" class="form-control w-full" name="service_price_qty[]" onkeyup="summary_item()" step="0.01">
                            </div>
                            <div class="intro-y col-span-12 sm:col-span-12 md:col-span-4 2xl:col-span-4">
                                <label for="crud-form-1" class="form-label">รวม</label>
                                <input type="number" class="form-control w-full product_price" onkeyup="summary()" name="service_price[]" step="0.01">
                            </div>
                        </div>

                        <div id="product-container">
                        </div>
                        @endif
                    </div>
                    <div class="intro-y col-span-12 sm:col-span-12 md:col-span-12 2xl:col-span-12 mt-4 text-right">
                        <button type="button" class="btn btn-success" onclick="add_product()">+ เพิ่มรายการสินค้า</button>
                    </div>

                    <div class="grid grid-cols-12 gap-2 mt-4">

                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-12 2xl:col-span-12">
                            <label>รวม</label>
                            <div class="mt-2">
                                <input type="number" class="form-control text-right w-full sumprice" name="order_sumprice" value="{{ (!empty($order) ? $order->order_sumprice : '0.00' ) }}" onkeyup="summary()" placeholder="" step="0.05">
                            </div>
                        </div>

                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-12 2xl:col-span-12">
                            <label>ส่วนลด</label>
                            <div class="mt-2">
                                <input type="number" class="form-control text-right w-full discount" name="order_discount" value="{{ (!empty($order) ? $order->order_discount : '0.00' ) }}" onkeyup="summary()" placeholder="" step="0.05">
                            </div>
                        </div>

                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-12 2xl:col-span-12">
                            <label>คงเหลือ</label>
                            <div class="mt-2">
                                <input type="number" class="form-control text-right w-full balance" name="order_balance" value="{{ (!empty($order) ? $order->order_balance : '0.00' ) }}" onkeyup="summary()" placeholder="" step="0.05">
                            </div>
                        </div>

                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-12 2xl:col-span-12">
                            <label>มัดจำ</label>
                            <div class="mt-2">
                                <input type="number" class="form-control text-right w-full deposit" name="order_deposit" value="{{ (!empty($order) ? $order->order_deposit : '0.00' ) }}" onkeyup="summary()" placeholder="" step="0.05">
                            </div>
                        </div>

                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-12 2xl:col-span-12">
                            <label>ยอดชำระจริง</label>
                            <div class="mt-2">
                                <input type="number" class="form-control text-right w-full total" name="order_total" value="{{ (!empty($order) ? $order->order_total : '0.00' ) }}" onkeyup="summary()" placeholder="" step="0.05">
                            </div>
                        </div>
                    </div>
                </div>
            <!-- END: Form Layout -->
            </div>

            <div class="intro-y col-span-12 lg:col-span-6">
                <!-- BEGIN: Form Layout -->
                <div class="intro-y box p-5">
                <label for="crud-form-1" class="form-label" style="font-size: 18px; font-weight: bold;">ข้อมูลช่างและหน้างาน</label>
                    <input type="hidden" name="order_customer" value="<?php echo (!empty($order) ? $order->order_customer : ''); ?>">
                    <input type="hidden" name="order_id" value="<?php echo (!empty($order) ? $order->order_id : ''); ?>">
                    <input type="hidden" name="work_id" value="<?php echo (!empty($work_order) ? $work_order->work_id : ''); ?>">
                    <div class="grid grid-cols-12 gap-2 mt-4">
                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-6 2xl:col-span-6">
                            <label for="crud-form-1" class="form-label">วันที่เริ่มงาน</label>
                            <input type="text" class="form-control w-full find_employee" placeholder="" id='order_datework' name="order_datework" value="<?php echo (!empty($work_order) ? date('d/m/Y', strtotime($work_order->work_date)) : date('d/m/Y') ); ?>">
                        </div>
                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-3 2xl:col-span-3">
                            <label for="crud-form-1" class="form-label">เวลาตั้งแต่</label>
                            <input type="text" class="form-control w-full find_employee" placeholder="" id='order_timestart' name="order_timestart" value="<?php echo (!empty($work_order) ? date('H:i', strtotime($work_order->work_timestart)) : date('H:i') ); ?>">
                        </div>
                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-3 2xl:col-span-3">
                            <label for="crud-form-1" class="form-label">ถึง</label>
                            <input type="text" class="form-control w-full find_employee" placeholder="" id='order_timeend' name="order_timeend" value="<?php echo (!empty($work_order) ? date('H:i', strtotime($work_order->work_timeend)) : date('H:i') ); ?>">
                        </div>
                        <?php 
                            $order_employee = array();
                            if(!empty($work)){
                                foreach ($work as $key => $_work) {
                                    array_push($order_employee, $_work->work_employee);
                                }
                            }else{
                                $order_employee = array();
                            }
                        ?>
                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-12 2xl:col-span-12">
                            <label for="crud-form-1" class="form-label">เลือกช่าง</label>
                            <select class="form-control service_employee select2" name="service_employee[]" multiple>
                                @if(!empty($employee))
                                    @foreach ($employee as $_employee)
                                    <option value="{{ $_employee->employee_id }}" {{ ( !empty($order_employee) ? (in_array($_employee->employee_id, $order_employee) ? 'selected' : '' ) : '') }}>{{ $_employee->employee_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-12 2xl:col-span-12 mt-3">
                            <label>ภาพหน้างาน</label>
                            <div class="mt-2">
                                <input type="file" class="form-control text-right w-full mt-3" name="order_work_front[]" value="" multiple accept="image/*">
                            </div>
                            <div class="mt-2">
                            @if(!empty($order_image_front))
                                @foreach($order_image_front as $_front)
                                    <a href="{{ Storage::url($_front->image_path) }}" data-lightbox="order-gallery">
                                        <img src="{{ Storage::url($_front->image_path) }}" alt="Order Image" width="150">
                                    </a>
                                @endforeach

                            @endif
                            </div>
                        </div>

                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-12 2xl:col-span-12 mt-3">
                            <label>ภาพปิดงาน</label>
                            <div class="mt-2">
                            @if(!empty($order_image_back))
                                @foreach($order_image_back as $_back)
                                    <a href="{{ Storage::url($_back->image_path) }}" data-lightbox="order-gallery">
                                        <img src="{{ Storage::url($_back->image_path) }}" alt="Order Image" width="150">
                                    </a>
                                @endforeach

                            @endif
                            </div>
                        </div>

                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-12 2xl:col-span-12 mt-3">
                            <label>ลายเซ็นต์ลูกค้า</label>
                            <div class="mt-2">
                            @if(!empty($order->order_sign))
                                <a href="{{ $order->order_sign }}" data-lightbox="order-gallery">
                                    <img src="{{ $order->order_sign }}" alt="Signature Image" width="150">
                                </a>
                            @endif
                            </div>
                        </div>

                        <div class="intro-y col-span-12 sm:col-span-12 md:col-span-12 2xl:col-span-12 mt-3">
                            <label>คะแนนความพึงพอใจจากลูกค้า : 
                                @if(!empty($order->order_sign_point))
                                    @if($order->order_sign_point == 1)
                                        น้อยมาก
                                    @elseif($order->order_sign_point == 2)
                                        น้อย
                                    @elseif($order->order_sign_point == 3)
                                        ปานกลาง
                                    @elseif($order->order_sign_point == 4)
                                        มาก
                                    @elseif($order->order_sign_point == 5)
                                        มากที่สุด
                                    @endif
                                @endif
                            </label>
                        </div>

                    </div>
                    <?php 
                        $user = Auth::user();
                        $position = $user ? $user->position : null;
                    ?>
                    @if($position == 1)
                    <div class="text-right mt-5">
                        <button type="reset" class="btn btn-outline-secondary w-24 mr-1">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary w-24">บันทึก</button>
                    </div>
                    @endif
                </div>
                <!-- END: Form Layout -->
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <script src="{{ mix('dist/js/ckeditor-classic.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.select2').select2();

            $('#order_datework').datepicker({ format: 'dd/mm/yyyy' });

            $('#order_timestart').timepicker({
                timeFormat: 'HH:mm', // ใช้ 24 ชั่วโมง
                interval: 15, // กำหนดช่วงเวลาต่อการเลือก (เช่น ทุกๆ 15 นาที)
                dynamic: false,
                dropdown: true,
                scrollbar: true
            });

            $('#order_timeend').timepicker({
                timeFormat: 'HH:mm', // ใช้ 24 ชั่วโมง
                interval: 15,
                dynamic: false,
                dropdown: true,
                scrollbar: true
            });


            $('.find_employee').change(function(){
                var order_datework = $('#order_datework').val();
                var order_timestart = $('#order_timestart').val();
                var order_timeend = $('#order_timeend').val();

                $.ajax({
                    dataType: 'json',
                    type: 'post',
                    url: "{{ url('check_employee') }}",
                    data: {
                        order_datework: order_datework,
                        order_timestart: order_timestart,
                        order_timeend: order_timeend,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (data) {
                        console.log(data);
                        
                        var $select = $(".service_employee");
                        $select.empty();

                        $.each(data, function(index, item) {
                            $select.append(
                                $('<option>', {
                                    value: item.employee_id,
                                    text: item.employee_name
                                })
                            );
                        });

                        $select.trigger('change');
                    }
                });
            });

        });

        function summary_item() {
            // ใช้กับทุกแถว product-row
            $('.product-row').each(function () {
                var row = $(this); // แถวปัจจุบัน

                var qty = parseFloat(row.find('input[name="service_qty[]"]').val()) || 0;
                var price_per_unit = parseFloat(row.find('input[name="service_price_qty[]"]').val()) || 0;

                var total = qty * price_per_unit;

                row.find('input[name="service_price[]"]').val(total.toFixed(2));
            });
            summary();
        }
        
        function summary(){
            var sumprice = 0;
            $('.product_price').each(function(){
                sumprice =  parseFloat(sumprice) + parseFloat($(this).val());
            });
            $('.sumprice').val(sumprice.toFixed(2));
            var discount = $('.discount').val();
            var balance = parseFloat(sumprice) - parseFloat(discount);
            $('.balance').val(balance.toFixed(2));
            var deposit = $('.deposit').val();
            var total = parseFloat(balance) - parseFloat(deposit);
            $('.total').val(total.toFixed(2));
        }

        function add_product() {
            var clonedRow = $('#product-template').clone().removeAttr('id').removeClass('hidden');
            $('#product-container').append(clonedRow);
            clonedRow.find('select').select2();
        }

        // Event listener สำหรับเปลี่ยน select และคำนวณราคา
        $(document).on('change', '.service_product', function () {
            var row = $(this).closest('.product-row'); // ค้นหาแถวที่เกี่ยวข้อง
            var product_price = parseFloat($(this).find(':selected').attr('ref')) || 0; // ดึงค่า ref (ราคา)
            console.log('Price : '+product_price);
            var qty = parseFloat(row.find('input[name="service_qty[]"]').val()) || 0; // ดึงจำนวน (qty)
            console.log('Qty : '+qty);
            var total_price = product_price * qty; // คำนวณราคา
            console.log('Total : '+total_price);

            // ใส่ผลลัพธ์ลงใน input[name="service_price[]"]
            row.find('input[name="service_price[]"]').val(total_price.toFixed(2));
            summary();
        });

        // Event listener สำหรับเปลี่ยนจำนวน (qty) และคำนวณราคาใหม่
        $(document).on('input', 'input[name="service_qty[]"]', function () {
            var row = $(this).closest('.product-row'); // ค้นหาแถวที่เกี่ยวข้อง
            console.log('Row:', row);

            // ดึงค่า `value` ของ select ที่เลือกในแถวปัจจุบัน
            var selectedOption = row.find('.service_product option:selected'); // ดึง <option> ที่เลือก
            var product_price = parseFloat(selectedOption.attr('ref')) || 0; // ดึง ref (ราคา)
            console.log('Selected Product Price:', product_price);

            // ดึงค่าจำนวน (qty)
            var qty = parseFloat($(this).val()) || 0; // ดึงค่าจาก input
            console.log('Qty:', qty);

            // คำนวณราคา
            var total_price = product_price * qty;
            console.log('Total:', total_price);

            // อัปเดตราคาผลลัพธ์
            row.find('input[name="service_price[]"]').val(total_price.toFixed(2));

            // เรียกใช้ summary()
            summary();
        });

    </script>
@endsection
