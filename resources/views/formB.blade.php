<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <form action="{{ route('convertImagesB') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <h2>Imágenes Subidas en el Formulario A</h2>
        @foreach ($formAImages as $image)
            <img src="{{ $image }}" alt="Imagen del Formulario A" style="width: 100px; height: auto;">
        @endforeach
    
        <h2>Subir 6 Imágenes Adicionales</h2>
        @for ($i = 1; $i <= 6; $i++)
            <label for="photo{{ $i }}">Foto {{ $i }}</label>
            <input type="file" name="photos[]" id="photo{{ $i }}"><br>
        @endfor
        <button type="submit">Subir 6 Imágenes</button>
    </form>
</body>
</html>