<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@lang('messages.title')</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    {{-- @if (app()->getLocale() === 'ar')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.rtl.min.css" integrity="sha384-gXt9imSW0VcJVHezoNQsP+TNrjYXoGcrqBZJpry9zJt8PCQjobwmhMGaDHTASo9N" crossorigin="anonymous">    
    @endif --}}
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" >
        <img src="{{ asset('images/logo.jpg') }}" alt="@lang('messages.university_logo_alt')">
        @lang('messages.university_name')
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="@lang('messages.toggle_navigation')">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            @auth
            <li class="nav-item active">
                <a class="nav-link"> <i class="fas fa-smile-beam"></i>



                    {{auth()->user()->name}} , {{auth()->user()->academic_number}} </a>
            </li>
            @endauth
            {{-- @if(Request::is('/'))
                <li class="nav-item active">
                    <a class="nav-link" href="#about"> <i class="fas fa-info-circle"></i> @lang('messages.about')</a>
                </li>
            @endif --}}


            
          
            <li class="nav-item dropdown active">
                <a class="nav-link dropdown-toggle" href="#" id="CommitteeDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-globe"></i> @lang('messages.language')
                </a>
                <div class="dropdown-menu" aria-labelledby="CommitteeDropdown">
                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('language-form-en').submit();">
                        @lang('messages.english')
                    </a>
                    <form id="language-form-en" action="{{ route('language.switch') }}" method="post" style="display: none;">
                        @csrf
                        <input type="hidden" name="language" value="en">
                    </form>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('language-form-ar').submit();">
                        @lang('messages.arabic')
                    </a>
                    <form id="language-form-ar" action="{{ route('language.switch') }}" method="post" style="display: none;">
                        @csrf
                        <input type="hidden" name="language" value="ar">
                    </form>
                </div>
            </li>

            @auth
            {{-- @php dd(auth()->user());  @endphp --}}


            @if (auth()->user()->roles->where('slug', 'committee-member')->isNotEmpty())


          
            <li class="nav-item dropdown active">
                <a class="nav-link dropdown-toggle" href="#" id="statusDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-excuse"></i> @lang('messages.excuses')
                </a>
                <div class="dropdown-menu" aria-labelledby="statusDropdown">
                    <a class="dropdown-item" href="/committees/home/Approved">@lang('messages.approved_excuses')</a>
                    <a class="dropdown-item" href="/committees/home/Rejected">@lang('messages.rejected_excuses')</a>

                </div>
            </li>

            @endif
            @endauth

            
            {{-- @auth
            <li class="nav-item dropdown active">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user-alt"></i> {{ auth()->user()->name }}
                </a>
                <div class="dropdown-menu" aria-labelledby="userDropdown">
                    @if (auth()->user()->hasRole('student') || auth()->user()->hasRole('faculty-member'))
                        <a class="dropdown-item" href="/user/dashboard">@lang('messages.dashboard')</a>
                        <div class="dropdown-divider"></div>
                    @endif

                    <a class="dropdown-item" href="/user/profile/edit">@lang('messages.edit_profile')</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="/logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        @lang('messages.log_out')
                    </a>
                    <form id="logout-form" action="/logout" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </li>  
            @else --}}
            @auth
            <li class="nav-item active">
                <a class="nav-link" href="/logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt" aria-hidden="true"></i>@lang('messages.log_out')
                </a>
                <form id="logout-form" action="/logout" method="POST" style="display: none;">
                    @csrf
                </form>            
            </li>
            @endauth
            

            {{-- <li class="nav-item dropdown active">
                <a class="nav-link dropdown-toggle" href="#" id="signInUpDropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-sign-in-alt"></i> @lang('messages.sign_in_up')
                </a>
                <div class="dropdown-menu" aria-labelledby="signInUpDropdown">
                    <a class="dropdown-item" href="/signin">@lang('messages.sign_in')</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="/signup">@lang('messages.sign_up')</a>
                </div>
            </li>   --}}
        </ul>
    </div>
</nav>

<main>
    @yield('content')
</main>

<footer class="text-white py-2">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-0">@lang('messages.contact_us'): <a href="mailto:contact@university.com" class="text-white">contact@university.com</a></p>
            </div>
            <div class="col-md-6 text-right">
                <a href="#" class="text-white mr-2"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="text-white mr-2"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-white mr-2"><i class="fab fa-instagram"></i></a>
                <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.js"></script>

<script>
    document.addEventListener("scroll", function() {
        let navbar = document.querySelector(".navbar");
        if (window.scrollY > 10) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    $(document).ready(function() {
        $('#dataTable1').DataTable();
    });
</script>
</body>
</html>
