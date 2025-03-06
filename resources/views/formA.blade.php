<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <form action="{{ route('convertImagesA') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @for ($i = 1; $i <= 5; $i++)
            <label for="photo{{ $i }}">Foto {{ $i }}</label>
            <input type="file" name="photos[]" id="photo{{ $i }}"><br>
        @endfor
        <button type="submit">Subir 5 Imágenes</button>
    </form>
</body>
</html>