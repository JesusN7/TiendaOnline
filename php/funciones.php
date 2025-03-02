<?php
require_once 'conexion_db.php';

// Función para validar el dni (id)
function validarDni($id)
{
    $letras = "TRWAGMYFPDXBNJZSQVHLCKE";
    if (!preg_match("/^\d{8}[A-Za-z]$/", $id)) return false;
    return strtoupper($id[-1]) === $letras[(int)substr($id, 0, 8) % 23];
}

// Validar teléfono
function validarTelefono($telefono)
{
    return preg_match("/^[0-9]{9}$/", $telefono);
}

// Validar email
function validarEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}


//Validar que los campos no excedan el tamaño de los campos en la base de datos
function validarCampos($nombre, $direccion, $localidad, $provincia, $email)
{
    return (
        strlen($nombre) > 100 ||
        strlen($direccion) > 255 ||
        strlen($localidad) > 100 ||
        strlen($provincia) > 100 ||
        strlen($email) > 100
    ) ? false : true;
}


// Validar la longitud de la contraseña
function longPassword($password, $minLength = 8)
{
    if (strlen($password) < $minLength) {
        return false;
    }
    return true;
}


// Función paginación
function paginacion($conexion, $paginaActual, $porPagina, $sqlPrincipal, $parametros = [])
{
    try {
        // 1. Construir consulta COUNT
        $countSql = preg_replace('/SELECT .* FROM/i', 'SELECT COUNT(*) FROM', $sqlPrincipal, 1);
        $stmt = $conexion->prepare($countSql);

        // Vincular parámetros
        if (!empty($parametros)) {
            foreach ($parametros as $clave => $valor) {
                $stmt->bindValue($clave, $valor);
            }
        }

        $stmt->execute();
        $totalRegistros = (int)$stmt->fetchColumn();

        // 2. Calcular el total de páginas
        $totalPaginas = max(1, ceil($totalRegistros / $porPagina));

        // 3. Validar la página actual
        $paginaActual = max(1, min($paginaActual, $totalPaginas));

        // 4. Calcular el OFFSET
        $offset = ($paginaActual - 1) * $porPagina;

        // 5. Construir la consulta con LIMIT y OFFSET
        $sql = "$sqlPrincipal LIMIT :limite OFFSET :offset";
        $stmt = $conexion->prepare($sql);

        // 6. Vincular parámetros adicionales
        if (!empty($parametros)) {
            foreach ($parametros as $clave => $valor) {
                $stmt->bindValue($clave, $valor);
            }
        }

        // 7. Vincular parámetros de paginación
        $stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        // 8. Ejecutar la consulta
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 9. Retornar los resultados y datos de paginación
        return [
            'resultados' => $resultados,
            'totalRegistros' => $totalRegistros,
            'totalPaginas' => $totalPaginas,
            'paginaActual' => $paginaActual
        ];
    } catch (PDOException $e) {
        throw new Exception("Error en la consulta de paginación: " . $e->getMessage());
    }
}

// Validar código producto
function validarCodigo($codigo)
{
    return preg_match("/^[A-Za-z]{3}[0-9]{1,3}$/", $codigo);
}


// Validar descripción del producto
function validarDescripcion($descripcion)
{
    return !empty($descripcion) && strlen($descripcion) <= 500;
}


// Validar precio del producto
function validarPrecio($precio)
{
    return is_numeric($precio) && $precio > 0;
}


// Validar descuento del producto
function validarDescuento($descuento)
{
    return is_numeric($descuento) && $descuento >= 0 && $descuento <= 100;
}


// Validar stock del producto
function validarStock($stock)
{
    return is_numeric($stock) && $stock >= 0;
}


// Validar imagen del producto
function validarImagen($nombreArchivo)
{
    $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
    return in_array($extension, ['jpg', 'jpeg', 'png']);
}


// Función para obtener los productos
function obtenerProductos($conexion)
{
    try {
        $sql = "SELECT * FROM productos";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error al obtener productos: " . $e->getMessage());
    }
}

// Función para obtener una tabla de usuarios que más tarde se podra reutilizar
function mostrarTablaUsuarios($usuarios, $esAdmin, $rolFiltrado)
{
    echo '<table border="1">';
    echo '<tr>
            <th>Nombre</th>
            <th>DNI</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Dirección</th>
            <th>Localidad</th>
            <th>Provincia</th>
            <th>Estado</th>
            <th>Rol</th>';

    // Mostrar columna de "Acciones" solo si es administrador
    if ($esAdmin) {
        echo '<th>Acciones</th>';
    }

    echo '</tr>';

    foreach ($usuarios as $usuario) {
        echo '<tr>
                <td>' . htmlspecialchars($usuario['nombre']) . '</td>
                <td>' . htmlspecialchars($usuario['id']) . '</td>
                <td>' . htmlspecialchars($usuario['email']) . '</td>
                <td>' . htmlspecialchars($usuario['telefono'] ?? 'No especificado') . '</td>
                <td>' . htmlspecialchars($usuario['direccion'] ?? 'No especificado') . '</td>
                <td>' . htmlspecialchars($usuario['localidad'] ?? 'No especificado') . '</td>
                <td>' . htmlspecialchars($usuario['provincia'] ?? 'No especificado') . '</td>
                <td>' . ($usuario['activo'] ? 'Activo' : 'Inactivo') . '</td>
                <td>' . htmlspecialchars($usuario['rol']) . '</td>';

        // Mostrar acciones solo si es administrador
        if ($esAdmin) {
            echo '<td>
                    <a href="form_modificar_usuario.php?id=' . $usuario['id'] . '">Modificar</a> |
                    <a href="eliminar_usuario.php?id=' . $usuario['id'] . '">Activar/Desactivar</a>
                  </td>';
        }

        echo '</tr>';
    }

    echo '</table>';
}

// Función para obtener las categorías activas
function obtenerCategorias($conexion)
{
    // Obtener categorías principales
    $sql = "SELECT id, nombre FROM categorias WHERE activo = 1 AND categoriaPadre IS NULL ORDER BY nombre";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener todas las subcategorías
    $sql = "SELECT id, nombre, categoriaPadre FROM categorias WHERE activo = 1 AND categoriaPadre IS NOT NULL ORDER BY nombre";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $subcategorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Asociar subcategorías con su categoría padre
    $subCatAgrupadas = [];
    foreach ($subcategorias as $sub) {
        $subCatAgrupadas[$sub['categoriaPadre']][] = $sub;
    }

    // Insertar subcategorías en las categorías principales
    foreach ($categorias as &$categoria) {
        $categoria['subcategorias'] = $subCatAgrupadas[$categoria['id']] ?? [];
    }

    return $categorias;
}

// Función para obtener el nombre de las categorías
function obtenerCategoriaPorId($conexion, $categoria_id)
{
    $sql = "SELECT nombre FROM categorias WHERE id = :categoria_id";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


function obtenerColorEstado($estado)
{
    switch ($estado) {
        case 'pendiente':
            return 'warning';
        case 'enviado':
            return 'primary';
        case 'entregado':
            return 'success';
        case 'cancelado':
            return 'danger';
        default:
            return 'secondary';
    }
}

function obtenerUsuarioPorId($id)
{
    global $conexion;
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}











// Funciones para reemplazar los triggers
function insertarLineaPedido($numPedido, $numLinea, $codigo_producto, $cantidad, $precio, $descuento)
{
    $conexion = conectar();

    try {
        // Iniciar transacción
        $conexion->beginTransaction();

        // 1. Insertar en la tabla `lineapedido`
        $sql = "INSERT INTO lineapedido (numPedido, numLinea, codigo_producto, cantidad, precio, descuento)
                VALUES (:numPedido, :numLinea, :codigo_producto, :cantidad, :precio, :descuento)";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':numPedido', $numPedido, PDO::PARAM_INT);
        $stmt->bindParam(':numLinea', $numLinea, PDO::PARAM_INT);
        $stmt->bindParam(':codigo_producto', $codigo_producto, PDO::PARAM_STR);
        $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
        $stmt->bindParam(':precio', $precio, PDO::PARAM_STR);
        $stmt->bindParam(':descuento', $descuento, PDO::PARAM_STR);
        $stmt->execute();

        // 2. Actualizar stock y estado en la tabla `productos`
        $sql = "UPDATE productos
                SET stock = GREATEST(stock - :cantidad, 0),
                    activo = IF(stock - :cantidad <= 0, 0, activo)
                WHERE codigo = :codigo_producto";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':codigo_producto', $codigo_producto, PDO::PARAM_STR);
        $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
        $stmt->execute();

        // Confirmar transacción
        $conexion->commit();
        echo "Línea de pedido insertada y stock actualizado correctamente.";
    } catch (PDOException $e) {
        // Revertir transacción en caso de error
        $conexion->rollBack();
        die("Error: " . $e->getMessage());
    }
}


function validarLineaPedido($codigo_producto, $cantidad)
{
    $conexion = conectar();

    try {
        // Obtener stock y estado del producto
        $sql = "SELECT stock, activo FROM productos WHERE codigo = :codigo_producto";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':codigo_producto', $codigo_producto, PDO::PARAM_STR);
        $stmt->execute();
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        // Validar stock
        if ($producto['stock'] < $cantidad) {
            throw new Exception("No hay suficiente stock para este producto.");
        }

        // Validar estado del producto
        if ($producto['activo'] == 0) {
            throw new Exception("El producto no está disponible.");
        }

        return true;
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
