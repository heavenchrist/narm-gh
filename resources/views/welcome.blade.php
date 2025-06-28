<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <title>NARM GHANA</title>
</head>
<body>
    <div class="jumbotron text-center text-white" style="background-color: #116493">
        <h2><font color="white">NARM GHANA PORTAL</h2>
        <h3>
        @if (date('H') < 12)
            Good Morning
        @elseif (date('H') >= 12 && date('H') < 17)
        Good Afternoon
        @else
           Good Evening
        @endif
        </h3></font>
        <p class="text-center" style=" margin:10px;">
            <a class="btn btn-info btn-sm"  href="{{ url('/portal') }}"><span style="font-size:20px">&#128101;</span>Member</a>
            <a class="btn btn-success btn-sm"  href="{{ url('/region') }}"><span style="font-size:20px">&#127971;</span>Region</a>
            <a class="btn btn-warning btn-sm"  href="{{ url('/admin') }}"><span style="font-size:20px">&#128110;</span> Admin</a>
        </p>
      </div>{{--  --}}

      <div class="container">

        <div id="myCarousel" class="carousel slide" data-ride="carousel" style="width:50%;margin:auto;">
          <!-- Indicators -->
          <ol class="carousel-indicators">
            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#myCarousel" data-slide-to="1"></li>
            <li data-target="#myCarousel" data-slide-to="2"></li>
          </ol>

          <!-- Wrapper for slides -->
          <div class="carousel-inner margin-top:5px;">
            <div class="item active">
              <img src="{{ asset('images/logo.png') }}" alt="img 1" style="width:100%;">
            </div>

            <div class="item">
              <img src="{{ asset('images/logo.png') }}" alt="img 2" style="width:100%;">
            </div>

            <div class="item">
              <img src="{{ asset('images/logo.png') }}" alt="img 3" style="width:100%;">
            </div>
          </div>

          <!-- Left and right controls -->
          <a class="left carousel-control" href="#myCarousel" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left"></span>
            <span class="sr-only">Previous</span>
          </a>
          <a class="right carousel-control" href="#myCarousel" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right"></span>
            <span class="sr-only">Next</span>
          </a>
        </div>
      </div>
</body>
</html>
