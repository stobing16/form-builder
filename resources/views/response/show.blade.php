@section('title', 'Detail Response')
@extends('layouts.admin')
@section('content')
    <div class="card mb-3">
        <div class="card-body card-dashboard">
            <div class="d-flex justify-content-between align-items-center">
                <p class="fs-4">
                    {{ count($responses) }}
                    Response
                </p>
                <button type="button" class="btn btn-lg btn-success export-excel d-flex gap-2">
                    <i class="bi bi-filetype-xls"></i>
                    Export Excel
                </button>
            </div>

            <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
                <!-- Tab Links -->
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Response</a>
                </li>
                {{-- <li class="nav-item" role="presentation">
                    <a class="nav-link" id="link-tab" data-bs-toggle="tab" href="#link" role="tab" aria-controls="link" aria-selected="false">Ringkasan</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="another-tab" data-bs-toggle="tab" href="#another" role="tab" aria-controls="another" aria-selected="false">Link</a>
                </li> --}}
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="myTabContent">
                <!-- Tab 1 - Active Content -->
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="mb-4">
                        <label for="" class="form-label">Select Response</label>
                        <select class="form-select" aria-label="Default select example" id="form-select">
                            <option selected>Select Response</option>
                            @foreach ($responses as $response)
                                <option value="{{ $response->id }}">{{ $response->id }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="content-response">
                    </div>

                </div>

                <!-- Tab 2 - Link Content -->
                <div class="tab-pane fade" id="link" role="tabpanel" aria-labelledby="link-tab">
                    <h4>Link Tab Content</h4>
                    <p>This is the content for the "Link" tab. You can customize it as needed.</p>
                </div>

                <!-- Tab 3 - Another Link Content -->
                <div class="tab-pane fade" id="another" role="tabpanel" aria-labelledby="another-tab">
                    <h4>Another Link Tab Content</h4>
                    <p>This is the content for another "Link" tab. Add any content you want here.</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('.export-excel').on('click', function() {

                // Tampilkan spinner dan disable tombol
                $('#loading').show();
                $(this).prop('disabled', true);

                $.ajax({
                    url: "{{ route('response.export-excel', $id) }}",
                    type: 'GET',
                    xhrFields: {
                        responseType: 'blob'
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response, status, xhr) {
                        var disposition = xhr.getResponseHeader('Content-Disposition');

                        var matches = /"([^"]*)"/.exec(disposition);
                        var filename = (matches != null && matches[1] ? matches[1] : 'export.xlsx');

                        // Membuat URL Object untuk Blob response
                        var blob = response;
                        var link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);
                        link.download = filename;
                        link.click();

                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: "File Berhasil Di Download",
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr, status) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON?.errors || 'No Data Responses';
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                html: errors,
                                confirmButtonText: 'Okay'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'An error occurred!',
                                text: 'Please try again.',
                                confirmButtonText: 'Okay'
                            });
                        }
                    },
                    complete: function() {
                        $('#loading').hide();
                        $('.export-excel').removeAttr('disabled');
                    }
                });
            })

            $('#form-select').on('change', function() {
                const val = $(this).val();

                $.ajax({
                    url: "{{ route('response.detail', ['id' => '__id__']) }}".replace('__id__', val),
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {

                        if (Array.isArray(res.response) && res.response.length > 0) {
                            let contentHTML = '';
                            let options;

                            contentHTML += `
                                <p for="pertanyaan" class="fw-bold fs-5">Response</p>
                                <hr class="mb-4">`;

                            $.each(res.response, function(index, item) {
                                contentHTML += '<div class="mb-4">'
                                contentHTML += `<p class="fw-bold fs-6">${item.question}</p>`

                                switch (item.type) {
                                    case 'text':
                                        contentHTML += `<input type="${item.type}" class="form-control form-control-lg" value="${item.answer}" disabled>`
                                        break;

                                    case 'longtext':
                                        contentHTML += `<textarea class="form-control form-control-lg" rows="5" disabled>${item.answer}</textarea>`
                                        break;

                                    case 'radio':
                                        options = JSON.parse(item.options)
                                        $.each(options, function(indexOption, option) {
                                            contentHTML += `<div class="form-check form-check-inline mt-2">
                                                <input class="form-check-input"
                                                    type="${item.type}" value="${option}" ${item.answer === option ? 'checked' : 'disabled'} />
                                                <label class="form-check-label">
                                                    ${option}
                                                </label>
                                            </div>`
                                        })

                                        break;

                                    case 'checkbox':
                                        console.log(item)
                                        options = JSON.parse(item.options)
                                        $.each(options, function(indexOption, option) {
                                            // const answer = JSON.parse(item.answer)
                                            contentHTML += `<div class="form-check form-check-inline mt-2">
                                                <input class="form-check-input"
                                                    type="${item.type}" value="${option}" ${item.answer.includes(option) ? 'checked' : 'disabled'} />
                                                <label class="form-check-label">
                                                    ${option}
                                                </label>
                                            </div>`
                                        });

                                        if (item.additional_answer) {
                                            contentHTML += `<div id="additional_answer_${item.id}" class="mt-2" >
                                                <label for="additional_answer_${item.id}" class="form-label">Lainnya :</label>
                                                <input type="text" id="additional_answer_${item.id}" class="form-control form-control-lg" value="${item.additional_answer}" disabled>
                                            </div>`
                                        }

                                        $(document).on('click', '.form-check-input:checked', function() {
                                            if (!this.hasAttribute('disabled')) {
                                                $(this).prop('disabled', true);
                                            }
                                        });

                                        break;

                                    case 'select':
                                        options = JSON.parse(item.options)
                                        contentHTML += `<select class="form-select form-select-lg" disabled>`;

                                        $.each(options, function(indexOption, option) {
                                            contentHTML += `<option value="${option}" ${item.answer === option ? 'selected' : ''}>${option}</option>`;
                                        })

                                        contentHTML += `</select>`;
                                        break;

                                    default:
                                        contentHTML += `<p>${item.answer}</p>`
                                        break;
                                }

                                contentHTML += '</div>'
                            });

                            $('#content-response').html(contentHTML);
                        }
                    },
                    error: function(xhr, status) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.error;
                            let errorMessages = Object.values(errors).flat();
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                html: errorMessages.join('<br>'),
                                confirmButtonText: 'Okay'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'An error occurred!',
                                text: 'Please try again.',
                                confirmButtonText: 'Okay'
                            });
                        }
                    },
                });


            })
        })
    </script>
@endpush
