CREATE OR REPLACE FUNCTION fn_listar_usuarios()
RETURNS TABLE(
    id_usuario BIGINT,
    nombre VARCHAR,
    correo VARCHAR,
    id_rol BIGINT,
    estatus BOOLEAN,
    fecha_creacion TIMESTAMP
)
AS $$
BEGIN
    RETURN QUERY
    SELECT id_usuario, nombre, correo, id_rol, estatus, fecha_creacion
    FROM usuarios
    ORDER BY id_usuario;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fn_ver_usuario(p_id BIGINT)
RETURNS TABLE(
    id_usuario BIGINT,
    nombre VARCHAR,
    correo VARCHAR,
    id_rol BIGINT,
    estatus BOOLEAN,
    fecha_creacion TIMESTAMP
)
AS $$
BEGIN
    RETURN QUERY
    SELECT id_usuario, nombre, correo, id_rol, estatus, fecha_creacion
    FROM usuarios
    WHERE id_usuario = p_id;
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION fn_crear_usuario(
    p_nombre VARCHAR,
    p_correo VARCHAR,
    p_contrasena VARCHAR,
    p_id_rol BIGINT
)
RETURNS BIGINT
AS $$
DECLARE
    nuevo_id BIGINT;
BEGIN
    INSERT INTO usuarios (nombre, correo, contrasena, id_rol, estatus, fecha_creacion)
    VALUES (p_nombre, p_correo, p_contrasena, p_id_rol, TRUE, NOW())
    RETURNING id_usuario INTO nuevo_id;

    RETURN nuevo_id;
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION fn_actualizar_usuario(
    p_id BIGINT,
    p_nombre VARCHAR,
    p_correo VARCHAR,
    p_contrasena VARCHAR,
    p_id_rol BIGINT,
    p_estatus BOOLEAN
)
RETURNS VOID
AS $$
BEGIN
    UPDATE usuarios
    SET
        nombre = COALESCE(p_nombre, nombre),
        correo = COALESCE(p_correo, correo),
        contrasena = COALESCE(p_contrasena, contrasena),
        id_rol = COALESCE(p_id_rol, id_rol),
        estatus = COALESCE(p_estatus, estatus)
    WHERE id_usuario = p_id;
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION fn_eliminar_usuario(p_id BIGINT)
RETURNS VOID
AS $$
BEGIN
    UPDATE usuarios SET estatus = FALSE WHERE id_usuario = p_id;
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION fn_reactivar_usuario(p_id BIGINT)
RETURNS VOID
AS $$
BEGIN
    UPDATE usuarios SET estatus = TRUE WHERE id_usuario = p_id;
END;
$$ LANGUAGE plpgsql;
