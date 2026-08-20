<?php

class SiniestroModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function buscarAsegurado($texto){
        $query = "CALL sp_buscar_asegurado(:texto)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':texto', $texto);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerFotoUsuario($idUsuario){

        $query = "
            SELECT foto
            FROM usuario
            WHERE id_usuario = :id
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $idUsuario);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['foto'] ?? null;
    }

    public function registrarSiniestro($data, $files, $idUsuario){

        try{

            // ##################################### Validar unidad #####################################

            if(empty($data['id_Unidad'])){
                throw new Exception("Debe seleccionar un asegurado válido");
            }

            // ##################################### Validar al menos una foto #####################################

            if(!isset($files['fotos']) || empty($files['fotos']['name'][0])){
                throw new Exception("Debe subir al menos una fotografía");
            }

            // ##################################### Configuración #####################################

            $maxSize = 20 * 1024 * 1024;

            $permitidosFotos = [
                'image/jpeg',
                'image/png',
                'image/webp'
            ];

            $permitidosVideos = [
                'video/mp4',
                'video/webm',
                'video/quicktime'
            ];

            // ##################################### Validar fotos #####################################

            if(isset($files['fotos'])){

                for($i = 0; $i < count($files['fotos']['name']); $i++){

                    if($files['fotos']['error'][$i] !== 0){
                        continue;
                    }

                    if($files['fotos']['size'][$i] > $maxSize){
                        throw new Exception("Una fotografía excede el tamaño permitido");
                    }

                    $mime = mime_content_type($files['fotos']['tmp_name'][$i]);

                    if(!in_array($mime, $permitidosFotos)){
                        throw new Exception("Formato de fotografía no permitido");
                    }
                }
            }

            // ##################################### Validar videos #####################################

            if(isset($files['videos']) && !empty($files['videos']['name'][0])){

                for($i = 0; $i < count($files['videos']['name']); $i++){

                    if($files['videos']['error'][$i] !== 0){
                        continue;
                    }

                    if($files['videos']['size'][$i] > $maxSize){
                        throw new Exception("Un video excede el tamaño permitido");
                    }

                    $mime = mime_content_type($files['videos']['tmp_name'][$i]);

                    if(!in_array($mime, $permitidosVideos)){
                        throw new Exception("Formato de video no permitido");
                    }
                }
            }

            // ##################################### Registrar siniestro #####################################

            $query = "
                CALL sp_registrar_siniestro(
                    :id_unidad,
                    :id_usuario,
                    :fecha,
                    :ubicacion,
                    :descripcion,
                    :hubo_unidades
                )
            ";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':id_unidad', $data['id_Unidad']);
            $stmt->bindParam(':id_usuario', $idUsuario);
            $stmt->bindParam(':fecha', $data['fecha']);
            $stmt->bindParam(':ubicacion', $data['ubicacion']);
            $stmt->bindParam(':descripcion', $data['descripcion']);
            $stmt->bindParam(':hubo_unidades', $data['hubo_unidades_involucradas']);

            $stmt->execute();

            // ##################################### Obtener ID #####################################

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $idSiniestro = $resultado['id_siniestro'];

            $stmt->closeCursor();

            // ##################################### Guardar fotos #####################################

            if(isset($files['fotos'])){
                $this->guardarArchivos($files['fotos'], $idSiniestro, 'foto');
            }

            // ##################################### Guardar videos #####################################

            if(isset($files['videos']) && !empty($files['videos']['name'][0])){
                $this->guardarArchivos($files['videos'], $idSiniestro, 'video');
            }

            return true;

        }catch(PDOException $e){

            throw new Exception(
                "Error al registrar siniestro: " . $e->getMessage()
            );
        }
    }

    private function guardarArchivos($archivos, $idSiniestro, $tipo){

        for(
            $i = 0;
            $i < count($archivos['name']);
            $i++
        ){
            if($archivos['error'][$i] === 0){
                $nombre =
                    $archivos['name'][$i];
                $mime =
                    mime_content_type(
                        $archivos['tmp_name'][$i]
                    );
                $blob =
                    file_get_contents(
                        $archivos['tmp_name'][$i]
                    );
                $query = "
                    INSERT INTO multimedia(
                        id_siniestro_m,
                        tipo,
                        mime_type,
                        nombre_archivo,
                        archivo
                    )
                    VALUES(
                        :id_siniestro,
                        :tipo,
                        :mime,
                        :nombre,
                        :archivo
                    )
                ";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(
                    ':id_siniestro',
                    $idSiniestro
                );
                $stmt->bindParam(
                    ':tipo',
                    $tipo
                );
                $stmt->bindParam(
                    ':mime',
                    $mime
                );
                $stmt->bindParam(
                    ':nombre',
                    $nombre
                );
                $stmt->bindParam(
                    ':archivo',
                    $blob,
                    PDO::PARAM_LOB
                );
                $stmt->execute();
            }
        }
    }

    public function listarSiniestros($idUsuario, $rol, $busqueda, $fechaInicio, $fechaFin){

        $sql = "CALL sp_listar_siniestros(
            :id_usuario,
            :rol,
            :busqueda,
            :fecha_inicio,
            :fecha_fin
        )";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(
            ':id_usuario',
            $idUsuario
        );

        $stmt->bindParam(
            ':rol',
            $rol
        );

        $stmt->bindParam(
            ':busqueda',
            $busqueda
        );

        $stmt->bindParam(
            ':fecha_inicio',
            $fechaInicio
        );

        $stmt->bindParam(
            ':fecha_fin',
            $fechaFin
        );
    
        if($fechaInicio && !strtotime($fechaInicio)){
            throw new Exception("Fecha inicial inválida");
        }

        if($fechaFin && !strtotime($fechaFin)){
            throw new Exception("Fecha final inválida");
        }
        
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerDetalleSiniestro($idSiniestro){

        $stmt = $this->conn->prepare(
            "CALL sp_obtener_detalle_siniestro(:id)"
        );

        $stmt->bindParam(':id', $idSiniestro);

        $stmt->execute();

        // ##################################### Primer resultset #####################################

        $detalle = $stmt->fetch(PDO::FETCH_ASSOC);

        // ##################################### Segundo resultset #####################################

        $stmt->nextRowset();

        $multimedia = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $detalle['multimedia'] = $multimedia;

        return $detalle;
    }

    public function obtenerArchivo($idMultimedia){

        $stmt = $this->conn->prepare(

            "SELECT mime_type, archivo
            FROM multimedia
            WHERE id_multimedia = :id"

        );

        $stmt->execute([
            ':id' => $idMultimedia
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerSeguimiento($idSiniestro){

        $stmt = $this->conn->prepare(
            "CALL sp_ObtenerSeguimientoSiniestro(:id)"
        );

        $stmt->bindParam(
            ':id',
            $idSiniestro,
            PDO::PARAM_INT
        );

        $stmt->execute();

        // Primer resultset
        $siniestro = $stmt->fetch(PDO::FETCH_ASSOC);

        // Segundo resultset
        $stmt->nextRowset();

        $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [

            'siniestro' => $siniestro,
            'comentarios' => $comentarios

        ];
    }

    public function registrarComentario($idSiniestro, $idUsuario, $comentario){

        $stmt = $this->conn->prepare(
            "CALL sp_RegistrarComentario(
                :idSiniestro,
                :idUsuario,
                :comentario
            )"
        );

        $stmt->bindParam(
            ':idSiniestro',
            $idSiniestro,
            PDO::PARAM_INT
        );

        $stmt->bindParam(
            ':idUsuario',
            $idUsuario,
            PDO::PARAM_INT
        );

        $stmt->bindParam(
            ':comentario',
            $comentario
        );

        return $stmt->execute();
    }

}