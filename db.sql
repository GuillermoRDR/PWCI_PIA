
CREATE DATABASE IF NOT EXISTS cursos
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_0900_ai_ci;

USE cursos;

CREATE TABLE roles (
    rol_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL UNIQUE,
    descripcion VARCHAR(255)
) ENGINE=InnoDB;

INSERT INTO roles (nombre, descripcion) VALUES
('administrador', 'Administra usuarios, categorías y moderación'),
('profesor', 'Publica y administra cursos'),
('alumno', 'Consume cursos y registra su avance');

CREATE TABLE usuarios (
    usuario_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rol_id TINYINT UNSIGNED NOT NULL,
    nombre_completo VARCHAR(150) NOT NULL,
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
    correo VARCHAR(254) NOT NULL UNIQUE,
    genero VARCHAR(30) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    foto_url VARCHAR(2048),
    password_hash VARCHAR(255) NOT NULL,
    estado ENUM('activo', 'bloqueado', 'inactivo')
        NOT NULL DEFAULT 'activo',
    intentos_fallidos TINYINT UNSIGNED NOT NULL DEFAULT 0,
    bloqueado_en DATETIME NULL,
    motivo_bloqueo VARCHAR(255) NULL,
    bloqueado_por BIGINT UNSIGNED NULL,
    registrado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    ultimo_acceso_en DATETIME NULL,
    CONSTRAINT fk_usuarios_rol
        FOREIGN KEY (rol_id) REFERENCES roles(rol_id),
    CONSTRAINT fk_usuarios_bloqueado_por
        FOREIGN KEY (bloqueado_por) REFERENCES usuarios(usuario_id),
    CONSTRAINT chk_intentos_fallidos
        CHECK (intentos_fallidos <= 3),
    INDEX idx_usuarios_estado_rol (estado, rol_id),
    INDEX idx_usuarios_registro (registrado_en)
) ENGINE=InnoDB;

CREATE TABLE categorias (
    categoria_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(500),
    estado ENUM('activa', 'inactiva') NOT NULL DEFAULT 'activa',
    creada_por BIGINT UNSIGNED NOT NULL,
    creada_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizada_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_categorias_admin
        FOREIGN KEY (creada_por) REFERENCES usuarios(usuario_id)
) ENGINE=InnoDB;

CREATE TABLE cursos (
    curso_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    profesor_id BIGINT UNSIGNED NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT NOT NULL,
    imagen_url VARCHAR(2048),
    modalidad_precio ENUM('curso_completo', 'por_nivel')
        NOT NULL,
    precio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    moneda CHAR(3) NOT NULL DEFAULT 'MXN',
    estado ENUM('borrador', 'activo', 'inactivo', 'eliminado')
        NOT NULL DEFAULT 'borrador',
    publicado_en DATETIME NULL,
    creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    eliminado_en DATETIME NULL,
    motivo_baja VARCHAR(500) NULL,
    CONSTRAINT fk_cursos_profesor
        FOREIGN KEY (profesor_id) REFERENCES usuarios(usuario_id),
    CONSTRAINT chk_cursos_precio CHECK (precio >= 0),
    INDEX idx_cursos_estado_fecha (estado, publicado_en),
    INDEX idx_cursos_profesor (profesor_id),
    INDEX idx_cursos_titulo (titulo)
) ENGINE=InnoDB;

CREATE TABLE curso_categorias (
    curso_id BIGINT UNSIGNED NOT NULL,
    categoria_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (curso_id, categoria_id),
    CONSTRAINT fk_cc_curso
        FOREIGN KEY (curso_id) REFERENCES cursos(curso_id),
    CONSTRAINT fk_cc_categoria
        FOREIGN KEY (categoria_id) REFERENCES categorias(categoria_id)
) ENGINE=InnoDB;

CREATE TABLE niveles (
    nivel_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    curso_id BIGINT UNSIGNED NOT NULL,
    numero_orden SMALLINT UNSIGNED NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT,
    es_gratuito BOOLEAN NOT NULL DEFAULT FALSE,
    precio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    estado ENUM('borrador', 'activo', 'inactivo')
        NOT NULL DEFAULT 'borrador',
    creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_niveles_curso
        FOREIGN KEY (curso_id) REFERENCES cursos(curso_id),
    CONSTRAINT uq_nivel_orden UNIQUE (curso_id, numero_orden),
    CONSTRAINT chk_nivel_precio CHECK (precio >= 0),
    INDEX idx_niveles_curso_estado (curso_id, estado)
) ENGINE=InnoDB;

CREATE TABLE recursos_nivel (
    recurso_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nivel_id BIGINT UNSIGNED NOT NULL,
    tipo ENUM('texto', 'pdf', 'enlace', 'imagen', 'video', 'archivo')
        NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT,
    contenido_texto LONGTEXT NULL,
    recurso_url VARCHAR(2048) NULL,
    orden SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_recursos_nivel
        FOREIGN KEY (nivel_id) REFERENCES niveles(nivel_id),
    CONSTRAINT uq_recurso_orden UNIQUE (nivel_id, orden),
    INDEX idx_recursos_tipo_nivel (nivel_id, tipo)
) ENGINE=InnoDB;

CREATE TABLE metodos_pago (
    metodo_pago_id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(60) NOT NULL UNIQUE,
    activo BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB;

INSERT INTO metodos_pago (nombre) VALUES
('tarjeta'), ('transferencia'), ('paypal'), ('otro');

CREATE TABLE pedidos (
    pedido_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    alumno_id BIGINT UNSIGNED NOT NULL,
    estado ENUM(
        'pendiente', 'pagado', 'fallido', 'cancelado', 'reembolsado'
    ) NOT NULL DEFAULT 'pendiente',
    moneda CHAR(3) NOT NULL DEFAULT 'MXN',
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pedidos_alumno
        FOREIGN KEY (alumno_id) REFERENCES usuarios(usuario_id),
    CONSTRAINT chk_pedido_importes
        CHECK (subtotal >= 0 AND total >= 0),
    INDEX idx_pedidos_alumno_fecha (alumno_id, creado_en),
    INDEX idx_pedidos_estado_fecha (estado, creado_en)
) ENGINE=InnoDB;

CREATE TABLE detalles_pedido (
    detalle_pedido_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id BIGINT UNSIGNED NOT NULL,
    tipo_item ENUM('curso', 'nivel') NOT NULL,
    curso_id BIGINT UNSIGNED NULL,
    nivel_id BIGINT UNSIGNED NULL,
    descripcion_historica VARCHAR(255) NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    cantidad SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    importe DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_detalles_pedido
        FOREIGN KEY (pedido_id) REFERENCES pedidos(pedido_id),
    CONSTRAINT fk_detalles_curso
        FOREIGN KEY (curso_id) REFERENCES cursos(curso_id),
    CONSTRAINT fk_detalles_nivel
        FOREIGN KEY (nivel_id) REFERENCES niveles(nivel_id),
    CONSTRAINT chk_detalle_tipo CHECK (
        (tipo_item = 'curso' AND curso_id IS NOT NULL
            AND nivel_id IS NULL)
        OR
        (tipo_item = 'nivel' AND nivel_id IS NOT NULL
            AND curso_id IS NULL)
    ),
    CONSTRAINT chk_detalle_importes CHECK (
        precio_unitario >= 0 AND cantidad > 0 AND importe >= 0
    ),
    INDEX idx_detalles_curso (curso_id),
    INDEX idx_detalles_nivel (nivel_id)
) ENGINE=InnoDB;

CREATE TABLE pagos (
    pago_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id BIGINT UNSIGNED NOT NULL,
    metodo_pago_id SMALLINT UNSIGNED NOT NULL,
    referencia_externa VARCHAR(255) NULL,
    estado ENUM(
        'iniciado', 'pendiente', 'aprobado', 'rechazado',
        'cancelado', 'reembolsado'
    ) NOT NULL DEFAULT 'iniciado',
    importe DECIMAL(10,2) NOT NULL,
    moneda CHAR(3) NOT NULL DEFAULT 'MXN',
    creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pagos_pedido
        FOREIGN KEY (pedido_id) REFERENCES pedidos(pedido_id),
    CONSTRAINT fk_pagos_metodo
        FOREIGN KEY (metodo_pago_id) REFERENCES metodos_pago(metodo_pago_id),
    CONSTRAINT chk_pago_importe CHECK (importe >= 0),
    UNIQUE KEY uq_pago_referencia (referencia_externa),
    INDEX idx_pagos_pedido_estado (pedido_id, estado)
) ENGINE=InnoDB;

CREATE TABLE inscripciones (
    inscripcion_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    alumno_id BIGINT UNSIGNED NOT NULL,
    curso_id BIGINT UNSIGNED NOT NULL,
    origen ENUM(
        'curso_gratuito', 'nivel_gratuito',
        'compra_curso', 'compra_nivel'
    ) NOT NULL,
    inscrito_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('activa', 'completada', 'cancelada')
        NOT NULL DEFAULT 'activa',
    completada_en DATETIME NULL,
    CONSTRAINT fk_inscripciones_alumno
        FOREIGN KEY (alumno_id) REFERENCES usuarios(usuario_id),
    CONSTRAINT fk_inscripciones_curso
        FOREIGN KEY (curso_id) REFERENCES cursos(curso_id),
    CONSTRAINT uq_inscripcion_alumno_curso
        UNIQUE (alumno_id, curso_id),
    INDEX idx_inscripciones_curso_estado (curso_id, estado),
    INDEX idx_inscripciones_alumno_fecha (alumno_id, inscrito_en)
) ENGINE=InnoDB;

CREATE TABLE accesos_nivel (
    acceso_nivel_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    inscripcion_id BIGINT UNSIGNED NOT NULL,
    nivel_id BIGINT UNSIGNED NOT NULL,
    origen ENUM('gratuito', 'curso_completo', 'nivel_individual')
        NOT NULL,
    otorgado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_accesos_inscripcion
        FOREIGN KEY (inscripcion_id)
        REFERENCES inscripciones(inscripcion_id),
    CONSTRAINT fk_accesos_nivel
        FOREIGN KEY (nivel_id) REFERENCES niveles(nivel_id),
    CONSTRAINT uq_acceso_inscripcion_nivel
        UNIQUE (inscripcion_id, nivel_id)
) ENGINE=InnoDB;

CREATE TABLE progreso_niveles (
    progreso_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    inscripcion_id BIGINT UNSIGNED NOT NULL,
    nivel_id BIGINT UNSIGNED NOT NULL,
    estado ENUM('no_iniciado', 'en_progreso', 'completado')
        NOT NULL DEFAULT 'no_iniciado',
    iniciado_en DATETIME NULL,
    completado_en DATETIME NULL,
    porcentaje DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_progreso_inscripcion
        FOREIGN KEY (inscripcion_id)
        REFERENCES inscripciones(inscripcion_id),
    CONSTRAINT fk_progreso_nivel
        FOREIGN KEY (nivel_id) REFERENCES niveles(nivel_id),
    CONSTRAINT uq_progreso_inscripcion_nivel
        UNIQUE (inscripcion_id, nivel_id),
    CONSTRAINT chk_progreso_porcentaje
        CHECK (porcentaje >= 0 AND porcentaje <= 100),
    INDEX idx_progreso_estado (estado)
) ENGINE=InnoDB;

CREATE TABLE certificados (
    certificado_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    inscripcion_id BIGINT UNSIGNED NOT NULL UNIQUE,
    folio VARCHAR(80) NOT NULL UNIQUE,
    nombre_alumno_historico VARCHAR(150) NOT NULL,
    nombre_curso_historico VARCHAR(200) NOT NULL,
    nombre_certificador_historico VARCHAR(150) NOT NULL,
    archivo_url VARCHAR(2048) NULL,
    emitido_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_certificados_inscripcion
        FOREIGN KEY (inscripcion_id)
        REFERENCES inscripciones(inscripcion_id)
) ENGINE=InnoDB;

CREATE TABLE comentarios (
    comentario_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    alumno_id BIGINT UNSIGNED NOT NULL,
    curso_id BIGINT UNSIGNED NOT NULL,
    calificacion TINYINT UNSIGNED NOT NULL,
    comentario TEXT NOT NULL,
    estado ENUM('visible', 'oculto') NOT NULL DEFAULT 'visible',
    creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    moderado_en DATETIME NULL,
    moderado_por BIGINT UNSIGNED NULL,
    motivo_moderacion VARCHAR(500) NULL,
    CONSTRAINT fk_comentarios_alumno
        FOREIGN KEY (alumno_id) REFERENCES usuarios(usuario_id),
    CONSTRAINT fk_comentarios_curso
        FOREIGN KEY (curso_id) REFERENCES cursos(curso_id),
    CONSTRAINT fk_comentarios_moderador
        FOREIGN KEY (moderado_por) REFERENCES usuarios(usuario_id),
    CONSTRAINT uq_comentario_alumno_curso
        UNIQUE (alumno_id, curso_id),
    CONSTRAINT chk_calificacion
        CHECK (calificacion BETWEEN 1 AND 5),
    INDEX idx_comentarios_curso_estado (curso_id, estado)
) ENGINE=InnoDB;

CREATE TABLE conversaciones (
    conversacion_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    participante_1_id BIGINT UNSIGNED NOT NULL,
    participante_2_id BIGINT UNSIGNED NOT NULL,
    curso_id BIGINT UNSIGNED NULL,
    creada_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_conversacion_participante_1
        FOREIGN KEY (participante_1_id) REFERENCES usuarios(usuario_id),
    CONSTRAINT fk_conversacion_participante_2
        FOREIGN KEY (participante_2_id) REFERENCES usuarios(usuario_id),
    CONSTRAINT fk_conversacion_curso
        FOREIGN KEY (curso_id) REFERENCES cursos(curso_id),
    CONSTRAINT chk_conversacion_participantes
        CHECK (participante_1_id <> participante_2_id),
    INDEX idx_conversacion_participantes
        (participante_1_id, participante_2_id)
) ENGINE=InnoDB;

CREATE TABLE mensajes (
    mensaje_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conversacion_id BIGINT UNSIGNED NOT NULL,
    remitente_id BIGINT UNSIGNED NOT NULL,
    contenido TEXT NOT NULL,
    enviado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    leido_en DATETIME NULL,
    CONSTRAINT fk_mensajes_conversacion
        FOREIGN KEY (conversacion_id)
        REFERENCES conversaciones(conversacion_id),
    CONSTRAINT fk_mensajes_remitente
        FOREIGN KEY (remitente_id) REFERENCES usuarios(usuario_id),
    INDEX idx_mensajes_conversacion_fecha
        (conversacion_id, enviado_en)
) ENGINE=InnoDB;

CREATE TABLE auditoria_administrativa (
    auditoria_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    administrador_id BIGINT UNSIGNED NOT NULL,
    usuario_afectado_id BIGINT UNSIGNED NULL,
    accion VARCHAR(80) NOT NULL,
    entidad VARCHAR(80) NOT NULL,
    entidad_id BIGINT UNSIGNED NULL,
    motivo VARCHAR(500) NULL,
    detalles JSON NULL,
    creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_auditoria_admin
        FOREIGN KEY (administrador_id) REFERENCES usuarios(usuario_id),
    CONSTRAINT fk_auditoria_usuario_afectado
        FOREIGN KEY (usuario_afectado_id) REFERENCES usuarios(usuario_id),
    INDEX idx_auditoria_fecha (creado_en),
    INDEX idx_auditoria_accion (accion)
) ENGINE=InnoDB;
