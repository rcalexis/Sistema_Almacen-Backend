CREATE OR REPLACE FUNCTION fn_listar_movimientos(p_usuario_id BIGINT, p_tipo_movimiento VARCHAR DEFAULT NULL)
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
    v_rol VARCHAR;
BEGIN
   
    SELECT r.nombre INTO v_rol
    FROM usuarios u
    JOIN roles r ON u.id_rol = r.id
    WHERE u.id_usuario = p_usuario_id;


    IF v_rol = 'Administrador' THEN
      
        RETURN QUERY
        SELECT m.id_movimiento, m.producto_id, p.nombre as nombre_producto, m.usuario_id, u.nombre as nombre_usuario,
               m.tipo_movimiento, m.cantidad, m.fecha_movimiento
        FROM movimientos m
        JOIN productos p ON m.producto_id = p.id_producto
        JOIN usuarios u ON m.usuario_id = u.id_usuario
        WHERE (p_tipo_movimiento IS NULL OR m.tipo_movimiento = p_tipo_movimiento)
        ORDER BY m.fecha_movimiento DESC;

    ELSIF v_rol = 'Almacenista' THEN
       
        RETURN QUERY
        SELECT m.id_movimiento, m.producto_id, p.nombre as nombre_producto, m.usuario_id, u.nombre as nombre_usuario,
               m.tipo_movimiento, m.cantidad, m.fecha_movimiento
        FROM movimientos m
        JOIN productos p ON m.producto_id = p.id_producto
        JOIN usuarios u ON m.usuario_id = u.id_usuario
        WHERE m.tipo_movimiento = 'salida'
        ORDER BY m.fecha_movimiento DESC;

    ELSE
      
        RAISE EXCEPTION 'No tiene permisos para ver el historial de movimientos.';
    END IF;
END;
$$ LANGUAGE plpgsql;