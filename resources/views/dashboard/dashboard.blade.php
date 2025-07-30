@extends('../layout/' . $layout)

@section('subhead')
    <title>Dashboard - Midone - Tailwind HTML Admin Template</title>
@endsection

@section('subcontent')
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 2xl:col-span-9">
            <div class="grid grid-cols-12 gap-6">
                <!-- BEGIN: General Report -->
                <div class="col-span-12 mt-8">
                    <div class="intro-y flex items-center h-10">
                        <h2 class="text-lg font-medium truncate mr-5">รายงานออเดอร์ภายในอาทิตย์</h2>
                        <a href="" class="ml-auto flex items-center text-primary">
                            <i data-lucide="refresh-ccw" class="w-4 h-4 mr-3"></i> Reload Data
                        </a>
                    </div>
                    <div class="grid grid-cols-12 gap-6 mt-5">
                        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                            <div class="report-box zoom-in">
                                <div class="box p-5">
                                    <div class="flex">
                                        <i data-lucide="shopping-cart" class="report-box__icon text-primary"></i>
                                        <!-- <div class="ml-auto">
                                            <div class="report-box__indicator bg-success tooltip cursor-pointer" title="33% Higher than last month">
                                                33% <i data-lucide="chevron-up" class="w-4 h-4 ml-0.5"></i>
                                            </div>
                                        </div> -->
                                    </div>
                                    <div class="text-3xl font-medium leading-8 mt-6">{{ number_format($order_total,0,'.',',') }} ฿</div>
                                    <div class="text-base text-slate-500 mt-1">ยอดเปิดบิล (หักส่วนลด)</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                            <div class="report-box zoom-in">
                                <div class="box p-5">
                                    <div class="flex">
                                        <i data-lucide="credit-card" class="report-box__icon text-pending"></i>
                                        <!-- <div class="ml-auto">
                                            <div class="report-box__indicator bg-danger tooltip cursor-pointer" title="2% Lower than last month">
                                                2% <i data-lucide="chevron-down" class="w-4 h-4 ml-0.5"></i>
                                            </div>
                                        </div> -->
                                    </div>
                                    <div class="text-3xl font-medium leading-8 mt-6">{{ $order_count }}</div>
                                    <div class="text-base text-slate-500 mt-1">จำนวนออเดอร์</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                            <div class="report-box zoom-in">
                                <div class="box p-5">
                                    <div class="flex">
                                        <i data-lucide="monitor" class="report-box__icon text-warning"></i>
                                        <!-- <div class="ml-auto">
                                            <div class="report-box__indicator bg-success tooltip cursor-pointer" title="12% Higher than last month">
                                                12% <i data-lucide="chevron-up" class="w-4 h-4 ml-0.5"></i>
                                            </div>
                                        </div> -->
                                    </div>
                                    <div class="text-3xl font-medium leading-8 mt-6">{{ number_format($order_summery_product,0,'.',',') }} ฿</div>
                                    <div class="text-base text-slate-500 mt-1">ยอดขายสินค้า</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                            <div class="report-box zoom-in">
                                <div class="box p-5">
                                    <div class="flex">
                                        <i data-lucide="user" class="report-box__icon text-success"></i>
                                        <!-- <div class="ml-auto">
                                            <div class="report-box__indicator bg-success tooltip cursor-pointer" title="22% Higher than last month">
                                                22% <i data-lucide="chevron-up" class="w-4 h-4 ml-0.5"></i>
                                            </div>
                                        </div> -->
                                    </div>
                                    <div class="text-3xl font-medium leading-8 mt-6">{{ number_format($order_summery_service,0,'.',',') }} ฿</div>
                                    <div class="text-base text-slate-500 mt-1">ยอดรวมบริการ</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END: General Report -->
                <!-- BEGIN: Sales Report -->
                <div class="col-span-12 lg:col-span-6 mt-8">
                    <div class="intro-y block sm:flex items-center h-10">
                        <h2 class="text-lg font-medium truncate mr-5">รายงานออเดอร์ประจำเดือน</h2>
                    </div>
                    <div class="intro-y box p-5 mt-12 sm:mt-5">
                        <div class="flex flex-col md:flex-row md:items-center">
                            <div class="flex">
                                <div>
                                    <div class="text-primary dark:text-slate-300 text-lg xl:text-xl font-medium">{{ number_format($order_total,0,'.',',') }} ฿</div>
                                    <div class="mt-0.5 text-slate-500">This Week</div>
                                </div>
                                <div class="w-px h-12 border border-r border-dashed border-slate-200 dark:border-darkmode-300 mx-4 xl:mx-5"></div>
                                <div>
                                    <div class="text-slate-500 text-lg xl:text-xl font-medium">{{ number_format($order_total_month,0,'.',',') }} ฿</div>
                                    <div class="mt-0.5 text-slate-500">This Month</div>
                                </div>
                            </div>
                        </div>
                        <div class="report-chart">
                            <div class="h-[275px]">
                                <canvas class="mt-6 -mb-6 report_line_chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END: Sales Report -->
                <!-- BEGIN: Weekly Top Seller -->
                <div class="col-span-12 sm:col-span-6 lg:col-span-3 mt-8">
                    <div class="intro-y flex items-center h-10">
                        <h2 class="text-lg font-medium truncate mr-5">Top 10 สินค้าขายดี</h2>
                    </div>
                    <div class="intro-y box p-5 mt-5">
                        <div class="mt-3">
                            <canvas id="report_pie_chart"></canvas>
                        </div>
                        <div class="w-52 sm:w-auto mx-auto mt-8" id="list_product"></div>
                    </div>
                </div>
                <!-- END: Weekly Top Seller -->
                <!-- BEGIN: Sales Report -->
                <div class="col-span-12 sm:col-span-6 lg:col-span-3 mt-8">
                    <div class="intro-y flex items-center h-10">
                        <h2 class="text-lg font-medium truncate mr-5">Top 10 ยอดขายพนักงาน</h2>
                    </div>
                    <div class="intro-y box p-5 mt-5">
                        <div class="mt-3">
                            <canvas class="report_sale_chart"></canvas>
                        </div>
                        <div class="w-52 sm:w-auto mx-auto mt-8" id="list_sale"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-12 2xl:col-span-3">
            <div class="2xl:border-l -mb-10 pb-10">
                <div class="2xl:pl-6 grid grid-cols-12 gap-x-6 2xl:gap-x-0 gap-y-6">
                    <!-- BEGIN: Transactions -->
                    <div class="col-span-12 md:col-span-6 xl:col-span-4 2xl:col-span-12 mt-3 2xl:mt-8">
                        <div class="intro-x flex items-center h-10">
                            <h2 class="text-lg font-medium truncate mr-5">แผนงานอาทิตย์นี้</h2>
                        </div>
                        <div class="mt-5">
                        @if(!empty($order))
                            @foreach ($order as $_order)
                                <?php $employee_work = DB::table('employee_work')->where('work_order',$_order->order_id)->first(); ?>
                                @if(!empty($employee_work))
                                <div class="intro-x">
                                    <div class="box px-5 py-3 mb-3 flex items-center zoom-in">
                                        <div class="mr-auto">
                                            <div class="font-medium">{{ $_order->order_no }}</div>
                                            <div class="text-slate-500 text-xs mt-0.5">นัดหมาย วันที่ {{ $employee_work->work_date}} <br> เวลา {{ $employee_work->work_timestart.' ถึง '.$employee_work->work_timeend }}</div>
                                        </div>
                                        <div class="'text-success'">{{ number_format($_order->order_total,0,'.',',').' ฿' }}</div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script src="https://code.jquery.com/jquery-1.12.4.js" integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){

        $.ajax({
            url: "{{ url('daily-summary') }}",
            method: "GET",
            success: function (res) {
                let ctx = $(".report_line_chart")[0].getContext("2d");

                const colors = {
                    primary: (opacity = 1) => `rgba(255, 102, 0, ${opacity})`,
                    slate: {
                        "500": (opacity = 1) => `rgba(100, 116, 139, ${opacity})`,
                        "300": () => `rgba(203, 213, 225, 1)`,
                    },
                };

                let myChart = new Chart(ctx, {
                    type: "line",
                    data: {
                        labels: res.labels,
                        datasets: [
                            {
                                label: "ยอดขายต่อวัน",
                                data: res.data,
                                borderWidth: 2,
                                borderColor: colors.primary(0.8),
                                backgroundColor: "transparent",
                                pointBorderColor: "transparent",
                                tension: 0.4,
                            },
                        ],
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                            },
                        },
                        scales: {
                            x: {
                                ticks: {
                                    font: {
                                        size: 12,
                                    },
                                    color: colors.slate["500"](0.8),
                                },
                                grid: {
                                    display: false,
                                    drawBorder: false,
                                },
                            },
                            y: {
                                ticks: {
                                    font: {
                                        size: 12,
                                    },
                                    color: colors.slate["500"](0.8),
                                    callback: function (value, index, values) {
                                        return "฿" + value;
                                    },
                                },
                                grid: {
                                    color: $("html").hasClass("dark")
                                        ? colors.slate["500"](0.3)
                                        : colors.slate["300"](),
                                    borderDash: [2, 2],
                                    drawBorder: false,
                                },
                            },
                        },
                    },
                });
            },
            error: function (err) {
                console.error("Error loading daily summary data:", err);
            },
        });

        const pieLabels = {!! json_encode($top_products) !!};
        const pieData = {!! json_encode($top_amounts) !!};
        const pieColors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
            '#FF9F40', '#E7E9ED', '#8B0000', '#008080', '#FFD700'
        ];

        console.log(pieLabels, pieData); // Debug

        if (pieLabels.length > 0) {
            let ctxPie = document.getElementById('report_pie_chart').getContext('2d');
            let pieChart = new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: pieLabels,
                    datasets: [{
                        data: pieData,
                        backgroundColor: pieColors,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // Custom legend
            let legendHtml = '';
            pieLabels.forEach(function(label, i){
                let percent = ((pieData[i] / pieData.reduce((a,b)=>a+b,0)) * 100).toFixed(1);
                legendHtml += `
                    <div class="flex items-center mt-2">
                        <div class="w-2 h-2 rounded-full mr-3" style="background:${pieColors[i]}"></div>
                        <span class="truncate">${label}</span>
                        <span class="font-medium ml-auto">${percent}%</span>
                    </div>
                `;
            });
            document.getElementById('list_product').innerHTML = legendHtml;
        }

        const donutLabels = {!! json_encode($top_employees) !!};
        const donutData = {!! json_encode($top_employees_amounts) !!};
        const donutColors = [
            '#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0', '#9966FF',
            '#FF9F40', '#E7E9ED', '#8B0000', '#008080', '#FFD700'
        ];

        if (donutLabels.length > 0) {
            let ctxDonut = $('.report_sale_chart')[0].getContext('2d');
            let donutChart = new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: donutLabels,
                    datasets: [{
                        data: donutData,
                        backgroundColor: donutColors,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false // We'll make a custom legend below
                        }
                    }
                }
            });

            // Custom legend
            let legendHtml = '';
            donutLabels.forEach(function(label, i){
                let percent = ((donutData[i] / donutData.reduce((a,b)=>a+b,0)) * 100).toFixed(1);
                legendHtml += `
                    <div class="flex items-center mt-2">
                        <div class="w-2 h-2 rounded-full mr-3" style="background:${donutColors[i]}"></div>
                        <span class="truncate">${label}</span>
                        <span class="font-medium ml-auto">${percent}%</span>
                    </div>
                `;
            });
            document.getElementById('list_sale').innerHTML = legendHtml;
        }
    });
</script>
