<?php

namespace Services;

use RuntimeException;

class CourseUploads
{
  private FirebaseStorage $storage;
  private array $uploaded = [];

  public function __construct()
  {
    $this->storage = new FirebaseStorage();
  }

  public function process(array &$niveles, array $files): void
  {
    $allowed = [
      'video' => ['video/mp4' => 'mp4', 'video/webm' => 'webm'],
      'pdf' => ['application/pdf' => 'pdf'],
      'imagen' => ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'],
      'archivo' => ['text/plain' => 'txt', 'application/zip' => 'zip', 'application/x-zip-compressed' => 'zip', 'audio/mpeg' => 'mp3', 'audio/wav' => 'wav', 'audio/x-wav' => 'wav', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx', 'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx'],
    ];
    foreach ($niveles as $index => &$nivel) {
      if (!is_array($nivel))
        throw new RuntimeException('Nivel inválido.');
      foreach (['video', 'pdf', 'imagen', 'archivo'] as $tipo) {
        $file = $files['niveles_archivos'] ?? null;
        $error = $file['error'][$index][$tipo] ?? UPLOAD_ERR_NO_FILE;
        if ($error === UPLOAD_ERR_NO_FILE)
          continue;
        if ($error !== UPLOAD_ERR_OK)
          throw new RuntimeException('No se pudo recibir el archivo.');
        $size = (int) ($file['size'][$index][$tipo] ?? 0);
        $max = $tipo === 'video' ? 100 * 1024 * 1024 : 15 * 1024 * 1024;
        $tmp = $file['tmp_name'][$index][$tipo] ?? '';
        if ($size < 1 || $size > $max || !is_uploaded_file($tmp))
          throw new RuntimeException('Archivo demasiado grande o inválido.');
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($tmp);
        $extension = $allowed[$tipo][$mime] ?? null;
        $original = (string) ($file['name'][$index][$tipo] ?? '');
        if ($tipo === 'archivo' && $mime === 'application/octet-stream' && strtolower(pathinfo($original, PATHINFO_EXTENSION)) === 'zip') {
          $extension = 'zip';
        }
        if (!$extension)
          throw new RuntimeException('Tipo de archivo no permitido.');
        $ruta = $this->storage->uploadResource($tmp, $extension, $mime);
        $this->uploaded[] = $ruta;
        if ($tipo === 'video') {
          $nivel['video_url'] = $ruta;
        } else {
          $nivel['recursos'][] = [
            'tipo' => $tipo,
            'titulo' => mb_substr(basename($original), 0, 200),
            'ruta' => $ruta
          ];
        }
      }
    }
    unset($nivel);
  }

  public function cleanup(): void
  {
    foreach ($this->uploaded as $ruta) {
      try {
        $this->storage->delete($ruta);
      } catch (\Throwable $e) {
        error_log($e->getMessage());
      }
    }
  }
}
