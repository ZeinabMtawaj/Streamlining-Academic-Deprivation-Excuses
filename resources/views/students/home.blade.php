@extends('layout')

@section('content')

<div class="container mt-3 pt-5">
    <div class="row">
        <div class="col-md-2">
            <img src="{{ asset('images/clock.jpg') }}" class="img-fluid" alt="Descriptive Alt Text">
        </div>
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="modern-title">@lang('messages.deprivations')</h3>
                <button id="exportButton" class="btn btn-primary" onclick="window.location.href='{{ route('export.deprivations') }}'">@lang('messages.export')</button>
            </div>
            {{-- <h3 class="modern-title">@lang('messages.deprivations')</h3> --}}
            <div class="card  shadow">
                <div class="card-body">
                    <table class="table" id="dataTable1">
                        <thead class="thead-light">
                            <tr>
                                {{-- <th scope="col" style="{{ auth()->check() && auth()->user()->hasRole('faculty-member') ? '' : 'display: none;' }}">@lang('messages.student')</th> --}}
                                    <th scope="col">@lang('messages.course')</th>
                                    <th scope="col">@lang('messages.initial_absence_percentage')</th>
                                    <th scope="col">@lang('messages.current_absence_percentage')</th> 
                                    <th scope="col">@lang('messages.excuse')</th> 
                                    <th scope="col">@lang('messages.status')</th> 


                                {{-- <th scope="col" style="{{ auth()->check() && auth()->user()->hasRole('faculty-member') ? '' : 'display: none;' }}">@lang('messages.action')</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deprivations as $deprivation)
                                <tr>
                                    {{-- <td style="{{ auth()->check() && auth()->user()->hasRole('faculty-member') ? '' : 'display: none;' }}">
                                        @if (auth()->check() && auth()->user()->hasRole('faculty-member'))
                                            {{ $absenceExcuse->student->name }}
                                        @endif
                                    </td> --}}
                                    <td>{{ $deprivation->course->course_name ?? __('messages.no_course') }}</td>
                                    <td>{{ ($deprivation->initial_absence_percentage)."%" ?? __('messages.no_initial_absence_percentage') }}</td>
                                    <td>{{ ($deprivation->current_absence_percentage)."%" ?? __('messages.no_current_absence_percentage') }}</td>
                                    
                                    
                                        @if($deprivation->excuse)
                                        <td>
                                            @php $name_of_file = basename($deprivation->excuse->excuse_file_path); @endphp
                                            @php $url = route('file.download', ['model' => 'excuse', 'folder' => 'excuses', 'id' => $deprivation->excuse->id, 'file' => $name_of_file]); @endphp
                                            <a href="{{ $url }}">{{ $name_of_file }}</a>
                                        </td>
                                        <td>
                                            @php
                                                $badgeClass = match($deprivation->status) {
                                                    'Pending' => 'badge-pending',
                                                    'Approved' => 'badge-approved',
                                                    'Rejected' => 'badge-rejected',
                                                    default => 'badge-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">
                                                @lang('messages.' . $deprivation->status)
                                            </span>
                                            @if ($deprivation->excuse->advisor_decision == 'Rejected' && $deprivation->excuse->rejection_reason_file_path)
                                            @php 
                                                $name_of_file = basename($deprivation->excuse->rejection_reason_file_path); 
                                                $url = route('file.download', ['model' => 'excuse', 'folder' => 'rejection reason files', 'id' => $deprivation->excuse->id, 'file' => $name_of_file]);
                                            @endphp
                                            <br>
                                            <a href="{{ $url }}">{{ $name_of_file }}</a>
                                        @endif
                                        </td>
                                        
                                        {{-- <td>@lang('messages.' . $deprivation->status)</td>  --}}
                                        @else
                                        <td>
                                            <i class="fa fa-file-upload  custom-icon custom-color-icon" onclick="enterFile({{ $deprivation->id }})" title="{{ __('messages.enter_excuse') }}"></i>

                                        </td>  
                                        <td>
                                            @lang('messages.no_excuse')
                                        </td>               
                                        @endif
                                
                                
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
    function enterFile(Id) {
      const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  
      Swal.fire({
        title: @json(__('messages.upload_your_excuse')),
        html: `
            <input type="file" id="excuseFile" style="display: none;" />
            <label for="excuseFile" class="custom-file-upload swal2-styled">
                Choose File
            </label>
        `,
        showCancelButton: true,
        confirmButtonColor: '#7c4a80',
        cancelButtonColor: '#d33',     
        confirmButtonText: @json(__('messages.submit_excuse')),
        cancelButtonText: @json(__('messages.cancel')),
        didOpen: () => {
            document.getElementById('excuseFile').onchange = function () {
                var fileName = this.files[0].name;
                document.querySelector('.custom-file-upload').textContent = fileName;
            };
        }
      }).then((result) => {
        if (result.isConfirmed) {
          var formData = new FormData();
          formData.append('id', Id);
          formData.append('file', document.getElementById('excuseFile').files[0]);
          formData.append('_token', token);
  
          axios.post('/Excuse/submit-excuse', formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          })
          .then(function (response) {
            Swal.fire({
              title: @json(__('messages.success')),
              text: @json(__('messages.excuse_submitted_successfully')),
              icon: 'success'
            }).then(() => {
              location.reload();
            });
          })
          .catch(function (error) {
            Swal.fire({
              title: @json(__('messages.error')),
              text: @json(__('messages.error_submitting_excuse')),
              icon: 'error'
            });
          });
        }
      });
    }
  </script>
  
  
@endsection
  

