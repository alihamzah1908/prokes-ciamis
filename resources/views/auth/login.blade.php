<!DOCTYPE html>
<html lang="en">

<head>
    <title>Administrator Aplikasi Prokes (Protokol Kesehatan)</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/mask.jpg') }}" />  
    <style>
        /*
        * Specific styles of signin component
        */
        /*
        * General styles
        */
        body, html {
            /* height: 100%; */
            max-height: 100%;
            max-width: 100%;
            /* background-repeat: no-repeat; */
            /* background-image: linear-gradient(rgb(104, 145, 162), rgb(12, 97, 33)); */
            background-image: url('https://image.freepik.com/free-vector/crowd-people-wearing-face-masks_52683-39841.jpg');
        }

        .card-container.card {
            max-width: 350px;
            padding: 40px 40px;
        }

        .btn {
            font-weight: 700;
            height: 36px;
            -moz-user-select: none;
            -webkit-user-select: none;
            user-select: none;
            cursor: default;
        }

        /*
        * Card component
        */
        .card {
            /* background-color: #F7F7F7; */
            background-color: #FFF;
            /* just in case there no content*/
            padding: 20px 25px 30px;
            margin: 0 auto 25px;
            margin-top: 50px;
            /* shadows and rounded borders */
            -moz-border-radius: 2px;
            -webkit-border-radius: 2px;
            /* border-radius: 2px; */
            border-radius: 15px;
            -moz-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
            -webkit-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
            box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
        }

        .profile-img-card {
            width: 96px;
            height: 96px;
            margin: 0 auto 10px;
            display: block;
            -moz-border-radius: 50%;
            -webkit-border-radius: 50%;
            border-radius: 50%;
        }

        /*
        * Form styles
        */
        .profile-name-card {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 10px 0 0;
            min-height: 1em;
        }

        .reauth-email {
            display: block;
            color: #404040;
            line-height: 2;
            margin-bottom: 10px;
            font-size: 14px;
            text-align: center;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
        }

        .form-signin #inputEmail,
        .form-signin #inputPassword {
            direction: ltr;
            height: 44px;
            font-size: 16px;
        }

        .form-signin input[type=email],
        .form-signin input[type=password],
        .form-signin input[type=text],
        .form-signin button {
            width: 100%;
            display: block;
            margin-bottom: 10px;
            z-index: 1;
            position: relative;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            border-radius: 25px;
        }

        .form-signin .form-control:focus {
            border-color: rgb(104, 145, 162);
            outline: 0;
            -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgb(104, 145, 162);
            box-shadow: inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgb(104, 145, 162);
        }

        .btn.btn-signin {
            /*background-color: #4d90fe; */
            /* background-color: rgb(104, 145, 162); */
            background-color: rgb(12, 97, 33);
            /* background-color: linear-gradient(rgb(104, 145, 162), rgb(12, 97, 33));*/
            padding: 0px;
            font-weight: 700;
            font-size: 14px;
            height: 36px;
            -moz-border-radius: 3px;
            -webkit-border-radius: 3px;
            border-radius: 25px;
            border: none;
            -o-transition: all 0.218s;
            -moz-transition: all 0.218s;
            -webkit-transition: all 0.218s;
            transition: all 0.218s;
        }

        .btn.btn-signin:hover,
        .btn.btn-signin:active,
        .btn.btn-signin:focus {
            background-color: rgb(12, 97, 33);
        }

        .forgot-password {
            color: rgb(104, 145, 162);
        }

        .forgot-password:hover,
        .forgot-password:active,
        .forgot-password:focus{
            color: rgb(12, 97, 33);
        }
    </style>
    @stack('stylesheets')
</head>

<body>
<div class="container">
    <div class="card card-container">
        <!-- <img class="profile-img-card" src="//lh3.googleusercontent.com/-6V8xOA6M7BA/AAAAAAAAAAI/AAAAAAAAAAA/rzlHcD0KYwo/photo.jpg?sz=120" alt="" /> -->
        <img id="profile-img" class="profile-img-card" src="{{ asset('images/healty-page.jpg') }}" />
        <p id="profile-name" class="profile-name-card"></p>
        <form class="form-signin" method="post" action="{{ route('prosess.login') }}">
        @csrf
            <h5 class="ml-2 p-1">Aplikasi Protokol Kesehatan</h5>
            <label class="ml-2">Harap Masukan Email dan Password</label>
            <h4 id="reauth-email" class="reauth-email"></h4>
            <input type="email" id="inputEmail" class="form-control" name="email" placeholder="email" required autofocus>
            <input type="password" name="password" id="inputPassword" class="form-control" password="password" placeholder="password" required>
            <div id="remember" class="checkbox">
                <label>
                    <!-- <input type="checkbox" value="remember-me"> Remember me -->
                </label>
            </div>
            <button class="btn btn-lg btn-primary btn-block btn-signin" type="submit">Masuk</button>
        </form><!-- /form -->
        <a href="#" class="forgot-password">
            <!-- Forgot the password? -->
        </a>
    </div><!-- /card-container -->
</div>
<script src="{{ asset('assets/js/jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/propper.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
</body>