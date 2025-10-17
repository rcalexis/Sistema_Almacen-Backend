CREATE OR REPLACE FUNCTION fn_listar_movimientos(p_tipo_movimiento VARCHAR DEFAULT NULL, p_usuario_id BIGINT)
RETURNS TABLE(
    id_movimiento BIGINT,
    producto_id BIGINT,
    nombre_producto VARCHAR,
    usuario_id BIGINT,
    nombre_usuario VARCHAR,
    tipo_movimiento VARCHAR,
    cantidad INTEGER,
    fecha_movimiento TIMESTAMP
) AS $$
DECLARE
    es_admin BOOLEAN;
BEGIN
    SELECT EXISTS(
        SELECT 1
        FROM usuarios u
        JOIN roles r ON u.id_rol = r.id
        WHERE u.id_usuario = p_usuario_id AND r.nombre = 'Administrador'
    ) INTO es_admin;

    IF NOT es_admin THEN
        RAISE EXCEPTION 'Solo los administradores pueden ver el historial';
    END IF;

    RETURN QUERY
    SELECT m.id_movimiento, m.producto_id, p.nombre as nombre_producto, m.usuario_id, u.nombre as nombre_usuario,
           m.tipo_movimiento, m.cantidad, m.fecha_movimiento
    FROM movimientos m
    JOIN productos p ON m.producto_id = p.id_producto
    JOIN usuarios u ON m.usuario_id = u.id_usuario
    WHERE (p_tipo_movimiento IS NULL OR m.tipo_movimiento = p_tipo_movimiento)
    ORDER BY m.fecha_movimiento DESC;
END;
$$ LANGUAGE plpgsql;
