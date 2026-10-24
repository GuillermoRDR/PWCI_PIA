<?php

namespace Services;

use Google\Cloud\Storage\StorageClient;
use RuntimeException;

class FirebaseStorage
{
  private $bucket;

  public function __construct()
  {
    $rutaJson = dirname(__DIR__)
      . '/secrets/pwci-pia-firebase-adminsdk-fbsvc-c3234f7603.json';

    if (!is_readable($rutaJson)) {
      throw new RuntimeException(
        'No se puede leer el JSON de Firebase: ' . $rutaJson
      );
    }

    $storage = new StorageClient([
      'projectId' => 'pwci-pia',
      'keyFilePath' => $rutaJson
    ]);

    $this->bucket = $storage->bucket(
      'pwci-pia.firebasestorage.app'
    );
  }


  public function uploadAvatar(string $temporaryPath, string $extension, string $mime): string
  {
    $nombre = 'usuarios/avatars/'
      . bin2hex(random_bytes(16))
      . '.' . $extension;

    $archivo = fopen($temporaryPath, 'rb');

    if ($archivo === false) {
      throw new RuntimeException(
        'No se pudo abrir la imagen.'
      );
    }

    try {
      $this->bucket->upload($archivo, [
        'name' => $nombre,
        'metadata' => [
          'contentType' => $mime
        ]
      ]);
    } finally {
      // El SDK puede haber cerrado el recurso.
      // Solo lo cerramos si continúa abierto.
      if (is_resource($archivo)) {
        fclose($archivo);
      }
    }

    return $nombre;
  }

  public function delete(string $nombre): void
  {
    $objeto = $this->bucket->object($nombre);

    if ($objeto->exists()) {
      $objeto->delete();
    }
  }
}
