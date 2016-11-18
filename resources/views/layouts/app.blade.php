<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @section('stylesheets')
    <!-- Styles -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/app.css"/>
    @show
    
    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ Auth::check() ? url('/home') : url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    &nbsp;
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ url('/login') }}">Login</a></li>
                        <li><a href="{{ url('/register') }}">Register</a></li>
                    @else
                        <li><a href="{{ route('recipes.index') }}">List Recipes</a></li>
                        <li><a href="{{ route('recipes.create') }}">Create Recipe</a></li>
                        
                        
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="{{ url('/logout') }}"
                                        onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endif
                </ul>
                <div class="col-sm-3 col-md-3 pull-right">
                    <form class="navbar-form" role="search" action="{{ route('search') }}">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search" name="q" id="srch-term">
                            <div class="input-group-btn">
                                <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    
    @if(session()->has('flash.success'))
    <div class="alert alert-success" role="alert">
        {{ session()->get('flash.success') }}
    </div>
    @endif
    
    @if(session()->has('flash.info'))
    <div class="alert alert-info" role="alert">
        {{ session()->get('flash.info') }}
    </div>
    @endif
    
    @if(session()->has('flash.warning'))
    <div class="alert alert-warning" role="alert">
        {{ session()->get('flash.warning') }}
    </div>
    @endif
    
    @if(session()->has('flash.error'))
    <div class="alert alert-danger" role="alert">
        {{ session()->get('flash.error') }}
    </div>
    @endif

    @yield('content')

    @section('javascript')
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="/bower_components/bootbox.js/bootbox.js"></script>
    <script>
        /*
        <a href="posts/2" data-method="delete"> <---- We want to send an HTTP DELETE request
    
        - Or, request confirmation in the process -
    
        <a href="posts/2" data-method="delete" data-confirm="Are you sure?">
    
        (credit: https://gist.githubusercontent.com/JeffreyWay/5112282/raw/454bc5f65a8190818b4d8e4cc7797523fe096546/laravel.js)
    
        */
    
        (function() {
    
          var laravel = {
            initialize: function() {
              this.methodLinks = $('a[data-method]');
    
              this.registerEvents();
            },
    
            registerEvents: function() {
              this.methodLinks.on('click', this.handleMethod);
            },
    
            handleMethod: function(e) {
              var link = $(this);
              var httpMethod = link.data('method').toUpperCase();
              var form;
    
              // If the data-method attribute is not PUT or DELETE,
              // then we don't know what to do. Just ignore.
              if ( $.inArray(httpMethod, ['PUT', 'DELETE']) === - 1 ) {
                return;
              }
    
              // Allow user to optionally provide data-confirm="Are you sure?"
              if ( link.data('confirm') ) {
                if ( ! laravel.verifyConfirm(link) ) {
                  return false;
                }
              }
    
              form = laravel.createForm(link);
              form.submit();
    
              e.preventDefault();
            },
    
            verifyConfirm: function(link) {
              return confirm(link.data('confirm'));
            },
    
            createForm: function(link) {
              var form = 
              $('<form>', {
                'method': 'POST',
                'action': link.attr('href')
              });
    
              var token =  $('{{ csrf_field() }}');
    
              var hiddenInput =
              $('<input>', {
                'name': '_method',
                'type': 'hidden',
                'value': link.data('method')
              });
    
              return form.append(token, hiddenInput)
                         .appendTo('body');
            }
          };
    
          laravel.initialize();
    
        })();
    </script>
    @show
</body>
</html>
