@extends('layout')

@section('content')

<div class="container mt-3 pt-5">
    <div class="row">
        <div class="col-md-2">
            <img src="{{ asset('images/document.jpg') }}" class="img-fluid" alt="Descriptive Alt Text">
        </div>
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="modern-title">@lang('messages.excuses')</h3>
                <button id="exportButton" class="btn btn-primary" onclick="window.location.href='{{ route('export.excuses') }}'">@lang('messages.export')</button>
            </div>
            {{-- <div class="row">
                <div class="col text-center">
                    <h3 class="modern-title">@lang('messages.excuses')</h3>
                </div>
            </div>
            <div class="row justify-content-end">
                <div class="col-auto">
                    <button id="exportButton" class="btn btn-primary">Export</button>
                </div>
            </div> --}}
            {{-- <h3 class="modern-title">@lang('messages.excuses')</h3> --}}
            <div class="card  shadow">
                <div class="card-body">
                    <table class="table" id="dataTable1">
                        <thead class="thead-light">
                            <tr>
                                {{-- <th scope="col" style="{{ auth()->check() && auth()->user()->hasRole('faculty-member') ? '' : 'display: none;' }}">@lang('messages.student')</th> --}}
                                    <th scope="col">@lang('messages.student_name')</th>
                                    <th scope="col">@lang('messages.student_number')</th>
                                    <th scope="col">@lang('messages.course')</th> 
                                    <th scope="col">@lang('messages.initial_absence_percentage')</th>
                                    <th scope="col">@lang('messages.excuse')</th> 
                                    <th scope="col">@lang('messages.action')</th> 
                                    <th scope="col">@lang('messages.status')</th> 


                                {{-- <th scope="col" style="{{ auth()->check() && auth()->user()->hasRole('faculty-member') ? '' : 'display: none;' }}">@lang('messages.action')</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($excuses as $excuse)
                                <tr>
                                    {{-- <td style="{{ auth()->check() && auth()->user()->hasRole('faculty-member') ? '' : 'display: none;' }}">
                                        @if (auth()->check() && auth()->user()->hasRole('faculty-member'))
                                            {{ $absenceExcuse->student->name }}
                                        @endif
                                    </td> --}}
                                    <td>{{ $excuse->deprivation->student->name ?? __('messages.no_course') }}</td>
                                    <td>{{ $excuse->deprivation->student->academic_number ?? __('messages.no_course') }}</td>

                                    <td>{{ $excuse->deprivation->course->course_name ?? __('messages.no_course') }}</td>
                                    <td>{{ ($excuse->deprivation->initial_absence_percentage)."%" ?? __('messages.no_initial_absence_percentage') }}</td>
                                    {{-- <td>{{ (($excuse->$deprivation->current_absence_percentage)."%" ?? __('messages.no_current_absence_percentage') }}</td> --}}
                                    
                                    
                                        {{-- @if($excuse->excuse_file_path) --}}
                                        <td>
                                            @php $name_of_file = basename($excuse->excuse_file_path); @endphp
                                            @php $url = route('file.download', ['model' => 'excuse', 'folder' => 'excuses', 'id' => $excuse->id, 'file' => $name_of_file]); @endphp
                                            <a href="{{ $url }}">{{ $name_of_file }}</a>
                                        </td>
                                        <td>
                                            <i class="fa fa-check custom-icon text-success" onclick="updateStatus({{ $excuse->id }}, true)" title="{{ __('messages.approve_it') }}"></i> &nbsp;&nbsp;
                                            <i class="fa fa-times custom-icon text-danger" onclick="updateStatus({{ $excuse->id }}, false)" title="{{ __('messages.reject_it') }}"></i>
                                        </td>
                                        <td>
                                            @php
                                                $badgeClass = match($excuse->advisor_decision) {
                                                    'Pending' => 'badge-pending',
                                                    'Approved' => 'badge-approved',
                                                    'Rejected' => 'badge-rejected',
                                                    default => 'badge-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">
                                                @lang('messages.' . $excuse->advisor_decision)
                                            </span>

                                            @if ($excuse->advisor_decision == 'Rejected' && $excuse->rejection_reason_file_path)
                                                @php 
                                                    $name_of_file = basename($excuse->rejection_reason_file_path); 
                                                    $url = route('file.download', ['model' => 'excuse', 'folder' => 'rejection reason files', 'id' => $excuse->id, 'file' => $name_of_file]);
                                                @endphp
                                                <br>
                                                <a href="{{ $url }}">{{ $name_of_file }}</a>
                                            @endif
                                        </td>
                                        
                                        {{-- <td>@lang('messages.' . $deprivation->status)</td>  --}}
                                        {{-- @else --}}
             
                                        {{-- @endif --}}
                                
                                
                                    <!-- Assume there are translation keys for each status -->
                                    {{-- <td style="{{ auth()->check() && auth()->user()->hasRole('faculty-member') ? '' : 'display: none;' }}">
                                        @if (auth()->check() && auth()->user()->hasRole('faculty-member'))
                                            <i class="fa fa-check text-success" onclick="updateStatus({{ $absenceExcuse->id }}, true)"></i> &nbsp;&nbsp;
                                            <i class="fa fa-times text-danger" onclick="updateStatus({{ $absenceExcuse->id }}, false)"></i>
                                        @endif
                                    </td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<!-- ... existing script ... -->
    
<script>
    function updateStatus(Id, status) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Configure messages and inputs based on the status
        let title, htmlContent, confirmButtonText;
        if (status) {
            title = @json(__('messages.are_you_sure_approve'));
            htmlContent = '';
            confirmButtonText = @json(__('messages.yes_approve_it'));
        } else {
            title = @json(__('messages.upload_your_rejection'));
            htmlContent = `
                <input type="file" id="rejectionFile" style="display: none;" />
                <label for="rejectionFile" class="custom-file-upload swal2-styled">
                    Choose File
                </label>
            `;
            confirmButtonText = @json(__('messages.submit_rejection'));
        }

        Swal.fire({
            title: title,
            html: htmlContent,
            showCancelButton: true,
            confirmButtonColor: '#7c4a80',
            cancelButtonColor: '#d33',     
            confirmButtonText: confirmButtonText,
            cancelButtonText: @json(__('messages.cancel')),
            didOpen: () => {
                if (!status) {
                    document.getElementById('rejectionFile').onchange = function () {
                        var fileName = this.files[0].name;
                        document.querySelector('.custom-file-upload').textContent = fileName;
                    };
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append('id', Id);
                formData.append('status', status);
                formData.append('_token', token);

                if (!status) {
                    formData.append('file', document.getElementById('rejectionFile').files[0]);

                }

                axios.post('/excuses/update-excuse', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(function (response) {
                    Swal.fire({
                        title: @json(__('messages.success')),
                        text: status ? @json(__('messages.approved_successfully')) : @json(__('messages.rejection_submitted_successfully')),
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                })
                .catch(function (error) {
                    Swal.fire({
                        title: @json(__('messages.error')),
                        text: @json(__('messages.error_processing_request')),
                        icon: 'error'
                    });
                });
            }
        });
    }
</script>

  
  
@endsection
  

