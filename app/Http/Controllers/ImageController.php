<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{

    public function showForm()
    {
        return view('image-upload');

       // phpinfo();

    }
   
    public function convertImageDBackup(Request $request)
    {
        try {
            // Validar el archivo recibido
           /*  $request->validate([
                'image' => 'required|file|mimes:jpeg,png,jpg,gif,webp,heic|max:10240', // Hasta 10MB
            ]); */

            
            
            // Comprobar si se recibió un archivo
            if ($request->hasFile('photo')) {
                // Obtener el archivo
                $file = $request->file('photo');
                $filename = $file->getClientOriginalName(); // Nombre original
                $extension = $file->getClientOriginalExtension(); // Extensión

                // Log para verificar el archivo
                Log::info('Archivo recibido', ['filename' => $filename, 'extension' => $extension]);
            } else {
                // Si no se recibió archivo
                Log::error('No se recibió un archivo en la solicitud');
                return response()->json(['error' => 'No se recibió ningún archivo'], 400);
            }

            //dd($request->hasFile('photo'),$file,$filename,$extension );
            // Obtener la ruta temporal del archivo
            $inputImagePath = $request->file('photo')->getPathname();
            //$inputImagePath = $request->file('image')->getRealPath();

            Log::info('Ruta temporal del archivo', ['path' => $inputImagePath]);

            // Verificar si el archivo existe en la ruta temporal
            if (!file_exists($inputImagePath)) {
                Log::error('El archivo no existe en la ruta especificada', ['path' => $inputImagePath]);
                return response()->json(['error' => 'El archivo no existe en: ' . $inputImagePath], 404);
            }

            // Intentar cargar la imagen usando Intervention Image
            try {
                $image = Image::read($inputImagePath);
            } catch (\Exception $e) {
                // Si falla al cargar la imagen
                Log::error('Error al cargar la imagen', ['error' => $e->getMessage(), 'path' => $inputImagePath]);
                return response()->json(['error' => 'Error al cargar la imagen: ' . $e->getMessage()], 500);
            }

            // Redimensionar la imagen según la orientación (horizontal o vertical)
            $isHorizontal = $image->width() >= $image->height();
            $width = $isHorizontal ? 1280 : 720;
            $height = $isHorizontal ? 720 : 1280;

            // Redimensionar la imagen
            $image->resize($width, $height, function ($constraint) {
                $constraint->upsize(); // Evitar escalar hacia arriba si la imagen es más pequeña
            });

            // Guardar la imagen convertida a WebP
            $outputImageWebPPath = public_path('storage/converted/kplogistics33.webp');
            try {
                $image->save($outputImageWebPPath, 60, 'webp');
            } catch (\Exception $e) {
                Log::error('Error al guardar la imagen WebP', ['error' => $e->getMessage()]);
                return response()->json(['error' => 'Error al guardar la imagen WebP: ' . $e->getMessage()], 500);
            }

            // Guardar la imagen convertida a JPG
            $outputImageJPGPath = public_path('storage/converted/kplogistics33.jpg');
            try {
                $image->save($outputImageJPGPath, 90, 'jpg');
            } catch (\Exception $e) {
                Log::error('Error al guardar la imagen JPG', ['error' => $e->getMessage()]);
                return response()->json(['error' => 'Error al guardar la imagen JPG: ' . $e->getMessage()], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Imagen convertida exitosamente.',
                'path_webp' => asset('storage/converted/kplogistics33.webp'),
                'path_jpg' => asset('storage/converted/kplogistics33.jpg'),
            ]);
        } catch (\Exception $e) {
            // Capturar cualquier otro error general
            Log::error('Error general en el proceso de conversión de imagen', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error general: ' . $e->getMessage()], 500);
        }
    }
    public function convertImage(Request $request)
    {
        try {
            // Validar los archivos recibidos
            $request->validate([
                'photos' => 'required|array|max:5', // Debe ser un arreglo con máximo 5 archivos
                'photos.*' => 'file|mimes:jpeg,png,jpg,gif,webp,heic|max:10240', // Cada archivo hasta 10MB
            ]);

            // Comprobar si se recibieron archivos
            if (!$request->hasFile('photos')) {
                Log::error('No se recibieron archivos en la solicitud');
                return response()->json(['error' => 'No se recibieron archivos'], 400);
            }

            $convertedImages = [];
            foreach ($request->file('photos') as $index => $file) {
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();

                Log::info('Archivo recibido', ['filename' => $filename, 'extension' => $extension]);

                // Obtener la ruta temporal del archivo
                $inputImagePath = $file->getPathname();

                // Verificar si el archivo existe en la ruta temporal
                if (!file_exists($inputImagePath)) {
                    Log::error('El archivo no existe en la ruta especificada', ['path' => $inputImagePath]);
                    continue; // Pasar al siguiente archivo
                }

                // Intentar cargar la imagen usando Intervention Image
                try {
                    $image = Image::read($inputImagePath);
                } catch (\Exception $e) {
                    Log::error('Error al cargar la imagen', ['error' => $e->getMessage(), 'path' => $inputImagePath]);
                    continue; // Pasar al siguiente archivo
                }

                // Redimensionar la imagen según la orientación (horizontal o vertical)
                $isHorizontal = $image->width() >= $image->height();
                $width = $isHorizontal ? 1280 : 720;
                $height = $isHorizontal ? 720 : 1280;

                // Redimensionar la imagen
                $image->resize($width, $height, function ($constraint) {
                    $constraint->upsize(); // Evitar escalar hacia arriba si la imagen es más pequeña
                });

                // Guardar la imagen convertida a WebP
                $webpFilename = "converted/{$filename}_{$index}.webp";
                $outputImageWebPPath = public_path("storage/{$webpFilename}");
                try {
                    $image->save($outputImageWebPPath, 60, 'webp');
                } catch (\Exception $e) {
                    Log::error('Error al guardar la imagen WebP', ['error' => $e->getMessage()]);
                    continue; // Pasar al siguiente archivo
                }

                // Guardar la imagen convertida a JPG
                $jpgFilename = "converted/{$filename}_{$index}.jpg";
                $outputImageJPGPath = public_path("storage/{$jpgFilename}");
                try {
                    $image->save($outputImageJPGPath, 90, 'jpg');
                } catch (\Exception $e) {
                    Log::error('Error al guardar la imagen JPG', ['error' => $e->getMessage()]);
                    continue; // Pasar al siguiente archivo
                }

                // Agregar las rutas al arreglo de imágenes convertidas
                $convertedImages[] = [
                    'path_webp' => asset("storage/{$webpFilename}"),
                    'path_jpg' => asset("storage/{$jpgFilename}"),
                ];
            }

            if (empty($convertedImages)) {
                return response()->json(['error' => 'No se pudo procesar ninguna imagen'], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Imágenes convertidas exitosamente.',
                'converted_images' => $convertedImages,
            ]);
        } catch (\Exception $e) {
            // Capturar cualquier otro error general
            Log::error('Error general en el proceso de conversión de imágenes', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error general: ' . $e->getMessage()], 500);
        }
    }

    public function showFormA()
    {
        // Recuperar imágenes subidas en el formulario A
       
        return view('formA');
    }
    public function convertImagesA(Request $request)
    {
        try {
            // Validar los archivos recibidos
            $request->validate([
                'photos' => 'required|array|max:5',
                'photos.*' => 'file|mimes:jpeg,png,jpg,gif,webp,heic|max:10240',
            ]);

            $convertedImages = [];
            foreach ($request->file('photos') as $index => $file) {
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $webpFilename = "converted/{$filename}_formA_{$index}.webp";
                $outputImageWebPPath = public_path("storage/{$webpFilename}");

                $image = Image::read($file->getPathname());
                $image->resize(1280, 900, function ($constraint) {
                    $constraint->upsize();
                });
                $image->save($outputImageWebPPath, 60, 'webp');

                $convertedImages[] = asset("storage/{$webpFilename}");
            }

            // Guardar las rutas en la sesión
            session(['formA_images' => $convertedImages]);

            // Redirigir al formulario B
            return redirect()->route('formB');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function showFormB()
    {
        // Recuperar imágenes subidas en el formulario A
        $formAImages = session('formA_images', []);
        return view('formB', compact('formAImages'));
    }

    public function convertImagesB(Request $request)
    {
        try {
            // Validar los archivos recibidos
            $request->validate([
                'photos' => 'required|array|max:6',
                'photos.*' => 'file|mimes:jpeg,png,jpg,gif,webp,heic|max:10240',
            ]);

            $formAImages = session('formA_images', []);
            $convertedImages = $formAImages;

            foreach ($request->file('photos') as $index => $file) {
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $webpFilename = "converted/{$filename}_formB_{$index}.webp";
                $outputImageWebPPath = public_path("storage/{$webpFilename}");

                $image = Image::read($file->getPathname());
                $image->resize(1280, 900, function ($constraint) {
                    $constraint->upsize();
                });
                $image->save($outputImageWebPPath, 60, 'webp');

                $convertedImages[] = asset("storage/{$webpFilename}");
            }
             // Guardar las rutas en la sesión
            session(['formB_images' => $convertedImages]);

             
            $formAImagesB = session('formB_images', []);
            return view('formC', compact('formAImagesB'));

            // Procesar o guardar las 11 imágenes
            /* return response()->json([
                'success' => true,
                'message' => 'Imágenes procesadas exitosamente.',
                'all_images' => $convertedImages,
            ]); */
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }


}
