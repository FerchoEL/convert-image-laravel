<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir y Convertir Imagen</title>
</head>
<body>
    {{-- <form action="{{ route('image.convert') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="photo">Selecciona una imagen:</label>
        <input type="file" name="photo" id="photo">
        
        
        <button type="submit">Convertir a WebP</button>
    </form> --}}
    <h1>Subir hasta 5 fotos para convertir</h1>
    @if ($errors->any())
        <div>
            <strong>Error:</strong> {{ $errors->first() }}
        </div>
    @endif
    <form action="{{ route('image.convert') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @for ($i = 1; $i <= 5; $i++)
            <label for="photo{{ $i }}">Foto {{ $i }}</label>
            <input type="file" name="photos[]" id="photo{{ $i }}"><br>
        @endfor
        <button type="submit">Convertir Imágenes</button>
    </form>
</body>
</html>
