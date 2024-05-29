@extends("index")


@section("content")

    <!-- Sale & Revenue Start -->
    <div class="container-fluid pt-4 px-4">
        <div class="row g-4">
            <div class="col-12">
                <div class="bg-secondary rounded h-100 p-4">
                    <h6 class="mb-4">Instruments List</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Symbol</th>
                                <th scope="col">Name</th>
                                <th scope="col">Industry</th>
                                <th scope="col">Description</th>
                                <th scope="col">Operation</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($instruments as $instrument)
                                <tr>
                                    <th scope="row">{{$instrument->id}}</th>
                                    <td>{{$instrument->symbol}}</td>
                                    <td>{{$instrument->name}}</td>
                                    <td>{{$instrument->industry->name}}</td>
                                    <td>{{$instrument->description}}</td>
                                    <td>
                                        <a href="{{route("instrument.ratio",["instrument_id"=>$instrument->id])}}">
                                            <button type="button" class="btn btn-square btn-outline-info m-2"><i
                                                    class="fa fa-chart-area"></i></button>
                                        </a>
                                        <a href="{{route("instrument.update.history",["instrument_id"=>$instrument->id])}}">
                                            <button type="button" class="btn btn-square btn-outline-success m-2"><i
                                                    class="fa fa-map-pin"></i></button>
                                        </a>

                                        <a href="{{route("instrument.add.info",["instrument_id"=>$instrument->id])}}">
                                            <button type="button" class="btn btn-square btn-outline-warning m-2"><i
                                                    class="fa fa-address-card"></i></button>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Sale & Revenue End -->


@endsection
