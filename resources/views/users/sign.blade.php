@extends('layout')

@section('content')

<div class="container mt-5 pt-5">
    <div class="row">
        <div class="col-12 col-sm-7 col-md-5 m-auto">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <!-- Centered SVG using Bootstrap's mx-auto class -->
                    <div class="text-center">
                        <svg class="mx-auto my-3" xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                        </svg>
                    </div>
                    <form method="post" action="/users/authenticate">
                        @csrf
                        <div class="form-group">
                            <input type="number" class="form-control" id="signinAcademicNumber" name="academic_number" value="{{ old('academic_number') }}" placeholder="{{ __('messages.academic_number') }}">
                            @error('academic_number')
                                <p class="text-danger small mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" id="signinPassword" name="password" value="{{ old('password') }}" placeholder="{{ __('messages.password') }}">
                            @error('password')
                                <p class="text-danger small mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            @error('invalidCred')
                                <p class="text-danger small mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="text-center mt-3">
                            <button class="btn custom-button-color">{{ __('messages.sign_in') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
