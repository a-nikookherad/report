@extends("index")


@section("content")

    <div class="container-fluid pt-4 px-4">
        <div class="row g-4">
            <div class="col-sm-12 col-xl-6">
                <div class="bg-secondary text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Worldwide Sales</h6>
                        <a href="">Show All</a>
                    </div>
                    <canvas id="worldwide-sales" width="871" height="435" style="display: block; box-sizing: border-box; height: 435px; width: 871px;"></canvas>
                </div>
            </div>
            <div class="col-sm-12 col-xl-6">
                <div class="bg-secondary text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Salse &amp; Revenue</h6>
                        <a href="">Show All</a>
                    </div>
                    <canvas id="salse-revenue" width="871" height="435" style="display: block; box-sizing: border-box; height: 435px; width: 871px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push("script")
        <script>
            // Worldwide Sales Chart
            var ctx1 = $("#worldwide-sales").get(0).getContext("2d");
            var myChart1 = new Chart(ctx1, {
                type: "bar",
                data: {
                    labels: ["2016", "2017", "2018", "2019", "2020", "2021", "2022"],
                    datasets: [{
                        label: "USA",
                        data: [15, 30, 55, 65, 60, 80, 95],
                        backgroundColor: "rgba(235, 22, 22, .7)"
                    },
                        {
                            label: "UK",
                            data: [8, 35, 40, 60, 70, 55, 75],
                            backgroundColor: "rgba(235, 22, 22, .5)"
                        },
                        {
                            label: "AU",
                            data: [12, 25, 45, 55, 65, 70, 60],
                            backgroundColor: "rgba(235, 22, 22, .3)"
                        }
                    ]
                },
                options: {
                    responsive: true
                }
            });
        </script>
    @endpush
@endsection
