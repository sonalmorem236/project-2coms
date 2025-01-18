<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Lead') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form id="createLeads" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="name">Import Leads</label>
                            <input type="file" id="lead_excel_file" name="lead_excel_file">
                        </div>
                        <div class="form-group">
                            <button type="submit">Upload</button>
                        </div>
                    </form>
                    <div id="response">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#createLeads").on("submit", function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                $.ajax({
                    url: "{{ route('lead.import') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        console.log("success", response);

                        $('#response').html('<p style="color: green">' + response.message +
                            '</p>');

                        var redirectUrl = "{{ route('lead.index') }}";
                        setTimeout(function() {
                            window.location.href = redirectUrl
                        }, 3000);

                    },
                    error: function(xhr) {

                        console.log("xhr", xhr.responseJSON.errors, isObject(xhr.responseJSON
                            .errors), Array.isArray(xhr.responseJSON.errors.errors));
                        $('#response').html('');
                        if (isObject(xhr.responseJSON
                                .errors) && Array.isArray(xhr.responseJSON.errors.errors)) {


                            var errorHtml = '';

                            errorHtml += `
                                <div class="error-box">
                                    <h2>Error Details</h2>
                                    <div class="error-content">`;

                            if (xhr.responseJSON.errors.errors.length > 0) {

                                errorHtml +=
                                    `<p style="color: red"><strong>Errors:</strong></p><ul>`;

                                $.each(xhr.responseJSON.errors.errors, function(index, values) {
                                    errorHtml += `<li> ${values} </li>`;
                                })
                                errorHtml += `</ul>`;
                            }

                            if (xhr.responseJSON.errors.success_records.length > 0) {
                                errorHtml +=
                                    `<p style="color: red"><strong>Success Records:</strong></p><ul>`;

                                $.each(xhr.responseJSON.errors.success_records, function(index,
                                    successValues) {
                                    errorHtml += `<li> ${successValues} </li>`;
                                })
                                errorHtml += `</ul>`;
                            };

                            if (xhr.responseJSON.errors.failed_records.length > 0) {
                                errorHtml +=
                                    `<p style="color: red"><strong>Failed Records:</strong></p><ul>`;

                                $.each(xhr.responseJSON.errors.failed_records, function(index,
                                    failedValues) {
                                    errorHtml += `<li> ${failedValues} </li>`;
                                })
                                errorHtml += `</ul>`;
                            };

                            errorHtml += `<p style="color: red"><strong>Success Records Count:</strong> ${xhr.responseJSON.errors.success_records_count}</p>
                                        <p style="color: red"><strong>Failed Records Count:</strong> ${xhr.responseJSON.errors.failed_records_count}</p>
                                    </div>
                                </div>`;

                            $('#response').html(errorHtml)
                        } else {
                            $('#response').html('<p style="color:red;">' + xhr.responseJSON
                                .errors + '</p>');
                        }

                    }
                });
            });
        });

        function isObject(value) {
            return typeof value === 'object' && value !== null && !Array.isArray(value);
        }
    </script>
</x-app-layout>
