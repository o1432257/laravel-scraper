<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <style>
        .wrapper > .card:nth-child(3n) .card-header{
            background-color: #F0D493;
        }
        .wrapper > .card:nth-child(3n-1) .card-header{
            background-color: #FFF0E6;
        }
        .wrapper > .card:nth-child(3n-2) .card-header{
            background-color: #D7E7E6;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-6 offset-md-3 mt-5 wrapper">
            @foreach($data as $key => $value)
                <div class="card text-center">
                    <h5 class="card-header">{{$key}}</h5>
                    <div class="card-body">
                        <p class="card-text">{{$value}} (USD)</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
</body>
</html>
