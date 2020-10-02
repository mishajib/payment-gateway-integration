<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Image Compression</title>
</head>
<body>
@if(Session::has('success'))
    {{ Session::get('success') }}
@endif
<form action="{{ route('image.compress') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="image"> <br>
    <button type="submit">Submit</button>
</form>
</body>
</html>