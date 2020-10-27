<html>
  <head>
    <title>{{ config('backpack.base.project_name') }} Error 404</title>

    <link href='//fonts.googleapis.com/css?family=Lato:100' rel='stylesheet' type='text/css'>

    <style>
    @keyframes floating {
      0% {
        transform: translate3d(0, 0, 0);
      }
      45% {
        transform: translate3d(0, -10%, 0);
      }
      55% {
        transform: translate3d(0, -10%, 0);
      }
      100% {
        transform: translate3d(0, 0, 0);
      }
    }
    @keyframes floatingShadow {
      0% {
        transform: scale(1);
      }
      45% {
        transform: scale(0.85);
      }
      55% {
        transform: scale(0.85);
      }
      100% {
        transform: scale(1);
      }
    }
    body {
      background-color: #191818;
    }

    .container {
      font-family: 'Varela Round', sans-serif;
      color: #fff;
      position: relative;
      height: 100vh;
      text-align: center;
      font-size: 16px;
    }
    .container h1 {
      font-size: 32px;
      margin-top: 32px;
    }
    .container p {
      margin-bottom: 35px;
    }

    .container .return {
      margin-top: 30px;
      padding: 10px;
      border: 2px solid #fff;
      border-radius: 5px;
      color: #FFF;
      text-decoration: none;
      -moz-transition: all .3s ease 0s;
      -webkit-transition: all .3s ease 0s;
      -o-transition: all .3s ease 0s;
      transition: all .3s ease 0s;
    }

    .container .return:hover, .container .return:active, .container .return:focus {
      background-color: rgba(255,255,255,0.2);
    }

    .boo-wrapper {
      width: 100%;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      paddig-top: 64px;
      paddig-bottom: 64px;
    }

    .boo {
      width: 160px;
      height: 184px;
      background-color: #191818;
      margin-left: auto;
      margin-right: auto;
      border: 3.39394px solid #fff;
      border-bottom: 0;
      overflow: hidden;
      border-radius: 80px 80px 0 0;
      box-shadow: -16px 0 0 2px rgba(12, 12, 12, 0.5) inset;
      position: relative;
      padding-bottom: 32px;
      animation: floating 3s ease-in-out infinite;
    }
    .boo::after {
      content: '';
      display: block;
      position: absolute;
      left: -18.82353px;
      bottom: -8.31169px;
      width: calc(100% + 32px);
      height: 32px;
      background-repeat: repeat-x;
      background-size: 32px 32px;
      background-position: left bottom;
      background-image: linear-gradient(-45deg, #191818 16px, transparent 0), linear-gradient(45deg, #191818 16px, transparent 0), linear-gradient(-45deg, #fff 18.82353px, transparent 0), linear-gradient(45deg, #fff 18.82353px, transparent 0);
    }
    .boo .face {
      width: 24px;
      height: 3.2px;
      border-radius: 5px;
      background-color: #fff;
      position: absolute;
      left: 50%;
      bottom: 56px;
      transform: translateX(-50%);
    }
    .boo .face::before, .boo .face::after {
      content: '';
      display: block;
      width: 6px;
      height: 6px;
      background-color: #fff;
      border-radius: 50%;
      position: absolute;
      bottom: 40px;
    }
    .boo .face::before {
      left: -24px;
    }
    .boo .face::after {
      right: -24px;
    }

    .shadow {
      width: 128px;
      height: 16px;
      background-color: rgba(12, 12, 12, 0.75);
      margin-top: 40px;
      margin-right: auto;
      margin-left: auto;
      border-radius: 50%;
      animation: floatingShadow 3s ease-in-out infinite;
    }

    </style>
  </head>
  <body>
    <div class="container">
      <div class="container">
        <div class="boo-wrapper">
          <div class="boo">
            <div class="face"></div>
          </div>
          <div class="shadow"></div>

          <h1>{{ trans('general.404.whops') }}</h1>
          <p>
            {!! trans('general.404.couldnt_find') !!}
          </p>
          <a href="{{url('')}}" class="return"> {{ trans('general.404.return') }} </a>
        </div>
      </div>
    </div>
  </body>
</html>
