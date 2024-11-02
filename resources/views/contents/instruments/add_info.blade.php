@extends("index")


@section("content")

    <!-- add instrument -->
    <div class="container-fluid pt-4 px-4">
        <div class="row g-4 justify-content-center" id="row_container">

            <div class="col-sm-8">
                <div class="bg-secondary rounded h-100 p-4">
                    <h6 class="mb-4">Add Info For Instrument</h6>
                    <form id="add_instrument_info_form" method="post">
                        @csrf

                        <input type="text" name="instrument_id" hidden value="{{request("instrument_id")}}">

                        <div class="form-floating mb-3">
                            <select class="form-select" id="financial_report_type" name="financial_report_type"
                                    aria-label="select type">
                                <option value="activity" selected>فعالیت</option>
                                <option value="balance_sheet">صورت وضعیت مالی</option>
                                <option value="income_statement">صورت سود و زیان</option>
                                <option value="cash_flow">صورت جریان های نقدی</option>
                                <option value="changes_in_property_rights">صورت حقوق مالکانه</option>
                            </select>
                            <label for="financial_report_type">Select financial report type</label>
                        </div>

                        <div class="mb-3">
                            <label for="instrument_url" class="form-label">Instrument URL</label>
                            <input type="text" class="form-control" name="instrument_url" id="instrument_url"
                                   aria-describedby="instrument_url" value="{{old("instrument_url")}}">
                            <div id="instrument_url" class="form-text">please enter instrument web url
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="instrument_file" class="form-label">Insert instrument xlsx file</label>
                            <input class="form-control bg-dark" type="file" id="instrument_file">
                        </div>

                        <div class="form-floating mb-3">
                                <textarea class="form-control" name="instrument_json"
                                          placeholder="Insert instrument json variable"
                                          id="instrument_json" style="height: 150px;"></textarea>
                            <label for="instrument_json">Json Variable</label>
                        </div>
                        <button type="submit" class="btn btn-primary" id="instrument_submit">Insert</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- add instrument End -->

    @push("script")
        <script>
            $('#add_instrument_info_form').submit(function (e) {
                e.preventDefault();

                // Serialize the form data
                let form = $('#add_instrument_info_form')[0];
                let formData = new FormData(form);
                eval(formData.get("instrument_json"));
                formData = {
                    instrument_id: formData.get("instrument_id"),
                    financial_report_type: formData.get("financial_report_type"),
                    instrument_url: formData.get("instrument_url"),
                    instrument_json: JSON.stringify(datasource),
                    _token: formData.get("_token"),
                }
                console.log(formData)
                $.ajax({
                    type: 'POST',
                    url: '{!! route('instrument.add.info.store') !!}',
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
                // $('#instrument_json').val("");
            });

        </script>
    @endpush
@endsection
