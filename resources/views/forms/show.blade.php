@section('title', 'Detail Form')
@extends('layouts.admin')

@section('content')
    <div class="card mb-3">
        <div class="card-body card-dashboard">
            <div class="d-flex justify-content-between align-items-center">
                <div class="mb-3">
                    <label class="fw-bold">Nama Form :</label>
                    <p> {{ $form->title }}</p>
                </div>
                <div>
                    <span class="badge rounded-pill {{ $form->is_active ? 'text-bg-primary' : 'text-bg-danger' }}">
                        {{ $form->is_active ? 'Aktif' : 'Inactive' }}
                    </span>

                </div>
            </div>
            <div class="mb-5">
                <label class="fw-bold">Deskripsi :</label>
                <p>{!! $form->description !!}</p>
            </div>
            <div class="mb-3 d-flex justify-content-between gap-4 align-items-center">
                <div id="shareableLinkContainer" class="w-100">
                    {{-- style="display: none;" --}}
                    <h5>Shareable Link:</h5>
                    <input type="text" class="form-control form-control-lg" id="shareableLink" value="{{ route('forms.user', $form->unique_url) }}" readonly>
                    <button id="copyButton" class="btn btn-secondary mt-2">Copy Link</button>
                    <button id="shareButton" class="btn btn-success mt-2">Share Link</button>
                </div>

                <div>
                    <a class="btn btn-primary add-question" href="{{ route('questions.create', $form->id) }}">Add Pertanyaan</a>
                </div>
            </div>

            <div class="mb-3">
                <label class="fw-bold">Pertanyaan :</label>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="questions-table" data-id="{{ $form->id }}">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Question</th>
                                <th>Type</th>
                                <th>Required</th>
                                <th>Option</th>
                                <th>Order</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="fw-bold mb-3">Contoh Form </h4>
                <a href="{{ route('forms.preview', $form->unique_url) }}" class="btn btn-primary preview">Preview</a>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {

            const id = $('#questions-table').data('id');
            const url = "{{ route('forms.show', ':id') }}".replace(':id', id);

            let datatable = $('#questions-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: url,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'question',
                        name: 'question'
                    },
                    {
                        data: 'label',
                        name: 'label',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'required',
                        name: 'required',
                        orderable: false,
                        searchable: false
                    },
                    {
                        name: 'options',
                        orderable: false,
                        searchable: false,
                        data: function(row) {
                            if (typeof row.option_values != 'undefined' && row.option_values != null) {
                                let html = '<ul>';
                                row.option_values.forEach(function(option) {
                                    html += '<li>' + option + '</li>';
                                });
                                html += '</ul>';
                                return html;
                            }
                            return "-";
                        },
                    },
                    {
                        data: 'order',
                        name: 'order',
                        render: function(data, type, row) {
                            let options = '';
                            let length = datatable.data().length

                            for (let i = 1; i <= length; i++) { // Assuming max 10 orders, modify based on your use case
                                options += `<option value="${i}" ${data === i ? 'selected' : ''}>${i}</option>`;
                            }
                            return `<select class="order-select form-select w-100" data-id="${row.id}">${options}</select>`;
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#questions-table').on('change', '.order-select', function() {
                let newOrder = $(this).val();
                let questionId = $(this).data('id');

                let url = "{{ route('forms.update-order') }}"

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        id: questionId,
                        order: newOrder,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Order updated successfully!');
                            datatable.draw()
                        } else {
                            alert('Failed to update order.');
                        }
                    },
                    error: function() {
                        alert('Error while updating order.');
                    }
                });
            });

            $('#copyButton').on('click', function() {
                const link = $('#shareableLink');
                link.select();
                document.execCommand('copy');
                alert('Link copied to clipboard!');
            });

            $('#shareButton').on('click', function() {
                const url = $('#shareableLink').val();
                if (navigator.share) {
                    navigator.share({
                        title: 'Check out this link!',
                        url: url
                    }).then(() => {
                        console.log('Share successful');
                    }).catch((error) => {
                        console.error('Error sharing:', error);
                    });
                } else {
                    alert('Sharing not supported on this browser.');
                }
            });

            $(document).on('click', '.edit', function() {
                const id = $(this).data('id')
                const editUrl = `{{ route('questions.edit', ['id' => ':id']) }}`.replace(':id', id)
                window.location.href = editUrl
            })

            $(document).on('click', '.delete', function() {
                const id = $(this).data('id')
                const deleteUrl = `{{ route('questions.delete', ['id' => ':id']) }}`.replace(':id', id);

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    datatable.draw(); // Reload halaman setelah penghapusan
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: xhr.responseJSON.message || 'Terjadi kesalahan, coba lagi nanti.'
                                });
                            }
                        });
                    }
                });
            })
        })
    </script>
@endpush
