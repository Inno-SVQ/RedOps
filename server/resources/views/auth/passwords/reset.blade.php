<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Gentellela Alela! | </title>
    
    <!-- Bootstrap -->
    <link href="{{ asset("css/bootstrap.min.css") }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ asset("css/font-awesome.min.css") }}" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="{{ asset("css/gentelella.min.css") }}" rel="stylesheet">

</head>

<body class="login">
<div>
    <a class="hiddenanchor" id="signup"></a>
    <a class="hiddenanchor" id="signin"></a>
    
    <div class="login_wrapper">
        <div class="animate form login_form">
            <section class="login_content">
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
					{!! BootForm::open(['url' => url('/password/reset'), 'method' => 'post']) !!}
                    <h1>Reset Password</h1>
                    
                    {!! BootForm::hidden('token', $token) !!}
	
					{!! BootForm::email('email', 'Email', old('email'), ['placeholder' => 'Email']) !!}
	
					{!! BootForm::password('password', 'Password', ['placeholder' => 'Password']) !!}
	
					{!! BootForm::password('password_confirmation', 'Password confirmation', ['placeholder' => 'Confirmation']) !!}
	
					{!! BootForm::submit('Send Password Reset Link', ['class' => 'btn btn-default col-md-9']) !!}
	
					<div class="clearfix"></div>
					
                    <div class="separator">
                        <p class="change_link">You have a password ?
                            <a href="{{ url('/login') }}" class="to_register"> Log in </a>
                        </p>
                        
                        <div class="clearfix"></div>
                        <br />
                        
                        <div>
                            <h1><i class="fa fa-paw"></i> Gentelella Alela!</h1>
                            <p>©2016 All Rights Reserved. Gentelella Alela! is a Bootstrap 3 template. Privacy and Terms</p>
                        </div>
                    </div>
                {!! BootForm::close() !!}
            </section>
        </div>
    </div>
</div>
</body>
</html>