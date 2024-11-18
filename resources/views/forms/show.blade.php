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
                    <table class="table table-striped table-bordered table-hover" id="questions-table">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Question</th>
                                <th>Type</th>
                                <th>Required</th>
                                <th>Option</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($form->questions as $question)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $question->question }}</td>
                                    <td>{{ $question->type->label }}</td>
                                    <td>{{ $question->is_required ? 'Ya' : 'Tidak' }}</td>
                                    <td>
                                        @if (isset($question->options))
                                            <ul>
                                                @foreach (json_decode($question->options, true) as $item)
                                                    <li>{{ $item }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-sm btn-warning edit" href="{{ route('questions.edit', $question->id) }}">Edit</a>
                                        <a class="btn btn-sm btn-danger delete" data-id="{{ $question->id }}">Delete</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">

                <h4 class="fw-bold mb-3">Contoh Form:</h4>
                <a href="{{ route('forms.preview', $form->unique_url) }}" class="btn btn-primary preview">Preview</a>
                {{-- <h4 class="fw-bold mb-3">Preview</h4> --}}
            </div>
            <hr class="pb-2">

            <div class="mb-4">
                <label class="form-label fw-bold">Nama</label>
                <input type="text" name="name" class="form-control form-control-lg" placeholder="Nama">
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Email</label>
                <input type="email" name="email" class="form-control form-control-lg" placeholder="Email">
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold">Phone Number</label>
                <input type="text" name="phone" class="form-control form-control-lg" placeholder="Phone">
            </div>

            <hr class="pb-2">
            @foreach ($form->questions as $question)
                <div class="mb-4">
                    <label class="form-label fw-bold">{{ $question->question }}</label>
                    @if (isset($question->catatan))
                        <p class="mb-1" style="font-size: 10pt; margin-bottom: 0;">*{{ $question->catatan }}</p>
                    @endif

                    @switch($question->question_type_id)
                        @case(1)
                            <input type="{{ $question->type->type }}" name="{{ 'form-' . $form->id . '[' . $question->id . ']' }}" class="form-control form-control-lg" placeholder="{{ $question->question }}">
                        @break

                        @case(2)
                            <textarea class="form-control form-control-lg" placeholder="{{ $question->question }}" name="{{ 'form-' . $form->id . '[' . $question->id . ']' }}" rows="5"></textarea>
                        @break

                        @case(3)
                            @foreach (json_decode($question->options, true) as $option)
                                <div class="form-check">
                                    <input class="form-check-input" type="{{ $question->type->type }}" name="{{ 'form-' . $form->id . '[' . $question->id . ']' }}">
                                    <label class="form-check-label">
                                        {{ $option }}
                                    </label>
                                </div>
                            @endforeach
                        @break

                        @case(4)
                            @foreach (json_decode($question->options, true) as $option)
                                <div class="form-check mt-2 form-check-inline">
                                    <input class="form-check-input" type="{{ $question->type->type }}" name="{{ 'form-' . $form->id . '[' . $question->id . ']' }}" value="" id="flexCheckDefault">
                                    <label class="form-check-label" for="flexCheckDefault">
                                        {{ $option }}
                                    </label>
                                </div>
                            @endforeach
                        @break

                        @case(5)
                            <select class="form-select" aria-label="Default select example" name="{{ 'form-' . $form->id . '[' . $question->id . ']' }}">
                                <option selected>Select ...</option>
                                @foreach (json_decode($question->options) as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                        @break

                        @default
                    @endswitch
                </div>
            @endforeach
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('#questions-table').DataTable();

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

            $('.delete').on('click', function() {
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
                                    location.reload(); // Reload halaman setelah penghapusan
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
