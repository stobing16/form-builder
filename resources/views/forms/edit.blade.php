@extends('layouts.admin')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Edit Forms</h3>
    </div>
    <div class="alert alert-danger d-none" id="error-messages"></div> <!-- Div for error messages -->
    <div class="card">
        <div class="card-body">
            <form id="edit-form" action="{{ route('forms.update', $form->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label"><small>Nama Form</small></label>
                    <input type="text" name="form_name" class="form-control form-control-lg" value="{{ old('form_name', $form->title) }}" placeholder="Nama Form">
                </div>
                <div class="mb-3">
                    <label class="form-label"><small>Description</small></label>
                    <textarea id="editor" class="form-control form-control-lg" name="description" rows="5">{{ old('description', $form->description) }}</textarea>
                </div>
                <div class="mb-3 d-flex justify-content-end">
                    <button type="submit" class="btn btn-lg btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('#editor').summernote();
            $("#edit-form").on('submit', function(e) {
                e.preventDefault()

                var formData = $(this).serialize();

                $.ajax({
                    url: $(this).attr('action'), // URL action form
                    type: 'POST',
                    data: formData + '&_method=PUT',
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = '{{ route('forms') }}';
                        });
                    },
                    error: function(xhr) {
                        // Tangani error
                        var errors = xhr.responseJSON.errors;
                        var errorMessage = '<ul>';
                        $.each(errors, function(key, value) {
                            errorMessage += '<li>' + value[0] + '</li>'; // Ambil pesan error pertama
                        });
                        errorMessage += '</ul>';
                        $('#error-messages').html(errorMessage).removeClass('d-none');
                    }
                });
            })
        })
    </script>
@endpush
