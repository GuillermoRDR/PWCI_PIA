<?php

class UserModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function registrar($data, $files) {
        $query = "CALL sp_Usuario_Gestion(
            'REGISTRAR',
            NULL,
            :nombre,
            :apellido_paterno,
            :apellido_materno,
            :fecha_nacimiento,
            :genero,
            :foto,
            :correo,
            :contrasenia,
            :alias,
            :id_rol
        )";

        $stmt = $this->conn->prepare($query);
        if(!$this->conn) {
            die("No se ha establecido una conexión a la base de datos.");
        }

        //Manejo de foto
        $foto = null;
        if (
            isset($files['foto']) &&
            $files['foto']['error'] === 0 &&
            is_uploaded_file($files['foto']['tmp_name'])
        ) {
            $tipo = $files['foto']['type'];

            if (!str_starts_with($tipo, 'image/')) {
                die("El archivo no es una imagen");
            }

            if ($files['foto']['size'] > 2 * 1024 * 1024) {
                die("La imagen es demasiado grande");
            }

            $foto = file_get_contents($files['foto']['tmp_name']);
        }

        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':apellido_paterno', $data['apellidoPaterno']);
        $stmt->bindParam(':apellido_materno', $data['apellidoMaterno']);
        $stmt->bindParam(':fecha_nacimiento', $data['fechaNacimiento']);
        $stmt->bindParam(':genero', $data['genero']);
        $stmt->bindParam(':foto',$foto, PDO::PARAM_LOB);
        $stmt->bindParam(':correo', $data['correo']);
        $stmt->bindParam(':contrasenia', $data['contrasenia']);
        $stmt->bindParam(':alias', $data['alias']);
        $stmt->bindParam(':id_rol', $data['rol']);

        //echo "Ejecutando consulta de registro...\n\n\n\n";
        //echo "Datos: " . json_encode($data) . "\n\n\n\n";

        return $stmt->execute();
    }

    public function login($email) {
        $query = "CALL sp_Usuario_Gestion(
        'LOGIN', 
        NULL, 
        NULL, 
        NULL, 
        NULL, 
        NULL, 
        NULL,
        NULL,
        :correo, 
        NULL,
        NULL, 
        NULL)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':correo', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarUsuario($data, $files, $id_credencial) {
        $query = "CALL sp_Usuario_Gestion(
            'ACTUALIZAR',
            :id_usuario,
            :nombre,
            :apellido_paterno,
            :apellido_materno,
            :fecha_nacimiento,
            :genero,
            :foto,
            :correo,
            :contrasenia,
            :alias,
            :id_rol
        )";

        $stmt = $this->conn->prepare($query);
        if(!$this->conn) {
            die("No se ha establecido una conexión a la base de datos.");
        }

        //Manejo de foto
        $foto = null;
        if (
            isset($files['foto']) &&
            $files['foto']['error'] === 0 &&
            is_uploaded_file($files['foto']['tmp_name'])
        ) {
            $tipo = $files['foto']['type'];

            if (!str_starts_with($tipo, 'image/')) {
                die("El archivo no es una imagen");
            }

            if ($files['foto']['size'] > 2 * 1024 * 1024) {
                die("La imagen es demasiado grande");
            }

            $foto = file_get_contents($files['foto']['tmp_name']);
        }

        $stmt->bindParam(':id_usuario', $id_credencial);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':apellido_paterno', $data['apellidoPaterno']);
        $stmt->bindParam(':apellido_materno', $data['apellidoMaterno']);
        $stmt->bindParam(':fecha_nacimiento', $data['fechaNacimiento']);
        $stmt->bindParam(':genero', $data['genero']);
        $stmt->bindParam(':foto',$foto, PDO::PARAM_LOB);
        $stmt->bindParam(':correo', $data['correo']);
        $stmt->bindParam(':contrasenia', $data['contrasenia']);
        $stmt->bindParam(':alias', $data['alias']);
        $stmt->bindParam(':id_rol', $data['rol']);
        //echo "Ejecutando consulta de registro...\n\n\n\n";
        //echo "Datos: " . json_encode($data) . "\n\n\n\n";

        return $stmt->execute();
    }

    public function obtenerUsuarioIdCredencial($id_credencial) {
        $query = "CALL sp_Usuario_Gestion(
        'OBTENER',
        :id_credencial, 
        NULL, 
        NULL, 
        NULL, 
        NULL, 
        NULL,
        NULL,
        NULL, 
        NULL,
        NULL, 
        NULL)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_credencial', $id_credencial);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


}