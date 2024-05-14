@extends("index")


@section("content")

    <!-- add instrument -->
    <div class="container-fluid pt-4 px-4">
        <div class="row g-4 justify-content-center" id="row_container">

            <div class="col-sm-10">
                <div class="bg-secondary rounded h-100 p-4">
                    <h6 class="mb-4">Insert Instrument</h6>
                    <form id="add_instrument_form" method="post">
                        @csrf
                        <div class="mb-3">
                            <label for="instrument_name" class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required id="instrument_name"
                                   aria-describedby="emailHelp" value="{{old("name")}}">
                            <div id="emailHelp" class="form-text">please enter instrument full name
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="instrument_slug" class="form-label">Folder Name</label>
                            <input type="text" class="form-control" name="slug" id="instrument_slug"
                                   aria-describedby="emailHelp" value="{{old("slug")}}">
                            <div id="emailHelp" class="form-text">please enter instrument folder name
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="instrument_symbol" class="form-label">Symbol</label>
                            <input type="text" class="form-control" name="symbol" value="{{old("symbol")}}" required
                                   id="instrument_symbol">
                        </div>

                        <div class="mb-3">
                            <label for="instrument_financial_period" class="form-label">Financial Period</label>
                            <input type="text" class="form-control" name="financial_period"
                                   value="{{old("financial_period","1398-12-29")}}" required
                                   aria-describedby="instrument_financial_period" id="instrument_financial_period">
                            <div id="instrument_financial_period" class="form-text">financial period(1403-12-19)
                            </div>
                        </div>

                        <div class="form-floating mb-3">
                            <select class="form-select" id="group_id" name="group_id" aria-label="select group">
                                <option selected="">Select group</option>
                                @foreach($groups as $group)
                                    <option value="{{$group->id}}">{{$group->name}}</option>
                                @endforeach
                            </select>
                            <label for="group_id">Select instrument group</label>
                        </div>

                        <div class="form-floating mb-3">
                            <select class="form-select" name="industry_id" id="industry_id"
                                    aria-label="select industry">
                                <option selected="">Select industry</option>
                                @foreach($industries as $industry)
                                    <option value="{{$industry->id}}">{{$industry->name}}</option>
                                @endforeach
                            </select>
                            <label for="industry_id">Select instrument industry</label>
                        </div>

                        <div class="form-floating mb-3">
                                <textarea class="form-control" name="description" placeholder="Leave a comment here"
                                          id="instrument_description" style="height: 150px;"></textarea>
                            <label for="instrument_description">Comments</label>
                        </div>
                        <button type="submit" class="btn btn-primary" id="instrument_submit">Add</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- add instrument End -->

    @push("script")
        <script>
            $('#add_instrument_form').submit(function (e) {
                e.preventDefault();

                // Serialize the form data
                let form = $('#add_instrument_form')[0];
                let formData = new FormData(form);
                formData = {
                    name: formData.get("name"),
                    symbol: formData.get("symbol"),
                    slug: formData.get("slug"),
                    industry_id: formData.get("industry_id"),
                    group_id: formData.get("group_id"),
                    financial_period: formData.get("financial_period"),
                    _token: formData.get("_token"),
                    description: formData.get("description"),
                }
                console.log(formData)
                $.ajax({
                    type: 'POST',
                    url: '{!! route('instrument.store') !!}',
                    data: formData,
                    success: function (response) {
                        let message = '<div class="alert alert-success alert-dismissible fade show" role="alert"><i class="fa fa-exclamation-circle me-2"></i>' + response.message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
                        // Handle the response message
                        $("#row_container").prepend(message)
                    },
                    error: function (xhr, status, error) {
                        let message = '<div class="alert alert-primary alert-dismissible fade show" role="alert"><i class="fa fa-exclamation-circle me-2"></i>' + xhr.responseText + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
                        // Handle errors if needed
                        if (xhr.status === 422) {
                            var errors = $.parseJSON(xhr.responseText);
                            $.each(errors, function (key, val) {
                                // $("#" + key + "_error").text(val[0]);
                                let message = '<div class="alert alert-primary alert-dismissible fade show" role="alert"><i class="fa fa-exclamation-circle me-2"></i>' + val[0] + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'

                            });
                        }
                        $("#row_container").prepend(message)
                        // console.error("error", xhr.responseText);
                    }
                });
            });

        </script>
    @endpush
@endsection
