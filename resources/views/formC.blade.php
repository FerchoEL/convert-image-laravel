<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
   
       
        <h2>Imágenes Subidas en el Formulario </h2>
        @foreach ($formAImagesB as $image2)
            <img src="{{ $image2 }}" alt="Imagen del Formulario B" style="width: 100px; height: auto;">
        @endforeach
   
</body>
</html>