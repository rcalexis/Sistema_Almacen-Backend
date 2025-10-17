-- admin y almacenista
CREATE OR REPLACE FUNCTION fn_listar_productos(p_estatus BOOLEAN DEFAULT NULL)
RETURNS TABLE(
    id_producto BIGINT,
    nombre VARCHAR,
    descripcion VARCHAR,
    cantidad_actual INTEGER,
    estatus BOOLEAN,
    fecha_creacion TIMESTAMP,
    usuario_creacion_id BIGINT,
    nombre_creador VARCHAR
)
AS $$
BEGIN
    RETURN QUERY
    SELECT 
        p.id_producto,
        p.nombre,
        p.descripcion,
        p.cantidad_actual,
        p.estatus,
        p.fecha_creacion,
        p.usuario_creacion_id,
        u.nombre as nombre_creador
    FROM productos p
    JOIN usuarios u ON p.usuario_creacion_id = u.id_usuario
    WHERE (p_estatus IS NULL OR p.estatus = p_estatus)
    ORDER BY p.id_producto;
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION fn_ver_producto(p_id BIGINT)
RETURNS TABLE(
    id_producto BIGINT,
    nombre VARCHAR,
    descripcion VARCHAR,
    cantidad_actual INTEGER,
    estatus BOOLEAN,
    fecha_creacion TIMESTAMP,
    usuario_creacion_id BIGINT,
    nombre_creador VARCHAR
)
AS $$
BEGIN
    RETURN QUERY
    SELECT 
        p.id_producto,
        p.nombre,
        p.descripcion,
        p.cantidad_actual,
        p.estatus,
        p.fecha_creacion,
        p.usuario_creacion_id,
        u.nombre as nombre_creador
    FROM productos p
    JOIN usuarios u ON p.usuario_creacion_id = u.id_usuario
    WHERE p.id_producto = p_id;
END;
$$ LANGUAGE plpgsql;

-- solo admin
CREATE OR REPLACE FUNCTION fn_crear_producto(
    p_nombre VARCHAR,
    p_descripcion VARCHAR,
    p_usuario_creacion_id BIGINT
)
RETURNS BIGINT
AS $$
DECLARE
    es_admin BOOLEAN;
BEGIN
  
    SELECT EXISTS(
        SELECT 1 FROM usuarios u 
        JOIN roles r ON u.id_rol = r.id 
        WHERE u.id_usuario = p_usuario_creacion_id 
        AND r.nombre = 'Administrador'
    ) INTO es_admin;
    
    IF NOT es_admin THEN
        RAISE EXCEPTION 'Solo los administradores pueden crear productos';
    END IF;

    INSERT INTO productos (nombre, descripcion, cantidad_actual, estatus, fecha_creacion, usuario_creacion_id)
    VALUES (p_nombre, p_descripcion, 0, TRUE, NOW(), p_usuario_creacion_id)
    RETURNING id_producto INTO nuevo_id;

    RETURN nuevo_id;
END;
$$ LANGUAGE plpgsql;

-- solo admin
CREATE OR REPLACE FUNCTION fn_aumentar_inventario(
    p_producto_id BIGINT,
    p_cantidad INTEGER,
    p_usuario_id BIGINT
)
RETURNS VOID
AS $$
DECLARE
    es_admin BOOLEAN;
    producto_activo BOOLEAN;
BEGIN

    SELECT EXISTS(
        SELECT 1 FROM usuarios u 
        JOIN roles r ON u.id_rol = r.id 
        WHERE u.id_usuario = p_usuario_id 
        AND r.nombre = 'Administrador'
    ) INTO es_admin;
    
    IF NOT es_admin THEN
        RAISE EXCEPTION 'Solo los administradores pueden agregar';
    END IF;

   
    SELECT estatus INTO producto_activo FROM productos WHERE id_producto = p_producto_id;
    
    IF NOT producto_activo THEN
        RAISE EXCEPTION 'No se puede aumentar inventario de un producto inactivo';
    END IF;

    IF p_cantidad <= 0 THEN
        RAISE EXCEPTION 'La cantidad debe ser mayor a 0';
    END IF;

    UPDATE productos 
    SET cantidad_actual = cantidad_actual + p_cantidad 
    WHERE id_producto = p_producto_id;

    INSERT INTO movimientos (producto_id, usuario_id, tipo_movimiento, cantidad, fecha_movimiento)
    VALUES (p_producto_id, p_usuario_id, 'entrada', p_cantidad, NOW());
END;
$$ LANGUAGE plpgsql;

-- solo almacenista
CREATE OR REPLACE FUNCTION fn_sacar_inventario(
    p_producto_id BIGINT,
    p_cantidad INTEGER,
    p_usuario_id BIGINT
)
RETURNS VOID
AS $$
DECLARE
    es_almacenista BOOLEAN;
    producto_activo BOOLEAN;
    cantidad_disponible INTEGER;
BEGIN
 
    SELECT EXISTS(
        SELECT 1 FROM usuarios u 
        JOIN roles r ON u.id_rol = r.id 
        WHERE u.id_usuario = p_usuario_id 
        AND r.nombre = 'Almacenista'
    ) INTO es_almacenista;
    
    IF NOT es_almacenista THEN
        RAISE EXCEPTION 'Solo los almacenistas pueden sacar inventario';
    END IF;

 
    SELECT estatus, cantidad_actual 
    INTO producto_activo, cantidad_disponible 
    FROM productos 
    WHERE id_producto = p_producto_id;
    
    IF NOT producto_activo THEN
        RAISE EXCEPTION 'No se puede sacar inventario de un producto inactivo';
    END IF;

    IF p_cantidad > cantidad_disponible THEN
        RAISE EXCEPTION 'No hay suficiente inventario', cantidad_disponible;
    END IF;

    IF p_cantidad <= 0 THEN
        RAISE EXCEPTION 'La cantidad debe ser mayor a 0';
    END IF;

    UPDATE productos 
    SET cantidad_actual = cantidad_actual - p_cantidad 
    WHERE id_producto = p_producto_id;

    INSERT INTO movimientos (producto_id, usuario_id, tipo_movimiento, cantidad, fecha_movimiento)
    VALUES (p_producto_id, p_usuario_id, 'salida', p_cantidad, NOW());
END;
$$ LANGUAGE plpgsql;

-- solo admin
CREATE OR REPLACE FUNCTION fn_dar_baja_producto(p_id BIGINT, p_usuario_id BIGINT)
RETURNS VOID
AS $$
DECLARE
    es_admin BOOLEAN;
BEGIN
  
    SELECT EXISTS(
        SELECT 1 FROM usuarios u 
        JOIN roles r ON u.id_rol = r.id 
        WHERE u.id_usuario = p_usuario_id 
        AND r.nombre = 'Administrador'
    ) INTO es_admin;
    
    IF NOT es_admin THEN
        RAISE EXCEPTION 'Solo los administradores pueden dar de baja productos';
    END IF;

    UPDATE productos SET estatus = FALSE WHERE id_producto = p_id;
END;
$$ LANGUAGE plpgsql;

-- solo admin
CREATE OR REPLACE FUNCTION fn_reactivar_producto(p_id BIGINT, p_usuario_id BIGINT)
RETURNS VOID
AS $$
DECLARE
    es_admin BOOLEAN;
BEGIN
    SELECT EXISTS(
        SELECT 1 FROM usuarios u 
        JOIN roles r ON u.id_rol = r.id 
        WHERE u.id_usuario = p_usuario_id 
        AND r.nombre = 'Administrador'
    ) INTO es_admin;
    
    IF NOT es_admin THEN
        RAISE EXCEPTION 'Solo los administradores pueden reactivar productos';
    END IF;

    UPDATE productos SET estatus = TRUE WHERE id_producto = p_id;
END;
$$ LANGUAGE plpgsql;
