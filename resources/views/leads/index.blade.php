<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lead details') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <table id="example" class="display" style="width:100%">
                        <thead>
                            <tr>
                                @if (auth()->user()->type == 'admin')
                                    <th>User</th>
                                @endif
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($allLeads as $lead)
                                <tr>
                                    @if (auth()->user()->type == 'admin')
                                        <td>{{ optional($lead->user)->name }}</td>
                                    @endif
                                    <td>{{ $lead->name }}</td>
                                    <td>{{ $lead->email }}</td>
                                    <td>{{ $lead->phone }}</td>
                                    <td>
                                        <select class="form-select" id="status-{{ $lead->id }}" name="status"
                                            onchange="changeStatus({{ $lead->id }})">
                                            @foreach (App\Models\Lead::statuses as $status)
                                                <option {{ $lead->status == $status ? 'selected' : '' }}
                                                    value="{{ $status }}">
                                                    {{ $status }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="actions">
                                        <!-- Trigger Button -->
                                        <button type="button" class="btn-edit" data-bs-toggle="modal"
                                            data-bs-target="#editModal" data-name="{{ $lead->name }}"
                                            data-email="{{ $lead->email }}" data-phone="{{ $lead->phone }}"
                                            data-status="{{ $lead->status }}" data-id="{{ $lead->id }}">
                                            Edit
                                        </button>
                                        <button onclick="deleteLead({{ $lead->id }})"
                                            class="btn-delete">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery (optional, only if you still need it) -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <!-- Bootstrap JS (Make sure it is loaded after everything else) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.10/dist/sweetalert2.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            // Initialize DataTable
            $('#example').DataTable();

            // Optional: If you want to do something specific when the modal is shown
            $(document).on('click', '.btn-edit', function() {
                var name = $(this).data('name');
                var email = $(this).data('email');
                var phone = $(this).data('phone');
                var id = $(this).data('id');

                // Populate the modal form fields with the lead's data
                $('#name').val(name);
                $('#email').val(email);
                $('#phone').val(phone);
                $('#id').val(id);
            });
        });

        // update lead details
        $("#updateLeads").on("submit", function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            $.ajax({
                url: "{{ route('lead.update') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    var color = response.success ? "green" : 'red'
                    $('#response').html('<p style="color:' + color + ';">' + response.message +
                        '</p>');

                    setTimeout(function() {
                        $('#editModal').modal('hide');
                    }, 3000);


                },
                error: function(xhr) {
                    $('#response').html('<p style="color:red;">' + xhr.responseJSON
                        .errors + '</p>');
                }
            })
        });

        // status change
        function changeStatus(id) {
            $.ajax({
                url: "{{ route('lead.status.change') }}",
                type: "POST",
                data: {
                    id: id,
                    status: $('#status-' + id).val()
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    console.log("error while status change", xhr);
                }
            })
        }

        // delete
        function deleteLead(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('lead.delete') }}",
                        type: "POST",
                        data: {
                            id: id,
                        },
                        success: function(response) {
                            Swal.fire(
                                'Deleted!',
                                'The lead has been deleted.',
                                'success'
                            ).then(() => {
                                location.reload(); // Refresh the page after successful deletion
                            });
                        },
                        error: function(xhr) {
                            console.log("error while status change", xhr);
                        }
                    })
                }
            })
        }
    </script>
</x-app-layout>
