<?php

// Función para validar el dni (id)
function validarDni($id)
{
    // Definir el conjunto de letras según las reglas de asignación.
    $letras = "TRWAGMYFPDXBNJZSQVHLCKE";

    // Verificar que el formato sea correcto (8 dígitos seguidos de una letra).
    if (!preg_match("/^[0-9]{8}[A-Za-z]$/", $id)) {
        return false; // Si el formato no es válido, retornar false directamente.
    }

    // Extraer los primeros 8 caracteres (número).
    $numero = (int) substr($id, 0, 8);

    // Extraer la última letra y convertirla a mayúsculas.
    $letra = strtoupper(substr($id, -1));

    // Calcular el resto de la división entre el número y 23.
    $resto = $numero % 23;

    // Comparar la letra calculada con la proporcionada y devolver el resultado directamente.
    return $letras[$resto] === $letra;
}



// Validar teléfono.
function validarTelefono($telefono)
{
    return preg_match("/^[0-9]{9}$/", $telefono);
}



// Validar email.
function validarEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}



//Validar que los campos no excedan el tamaño de los campos en la base de datos.
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


// Validar la longitud de la contraseña.
function longPassword($password, $minLength = 8)
{
    if (strlen($password) < $minLength) {
        return false;
    }
    return true;
}

// Función paginación.
function paginacion($conexion, $paginaActual, $porPagina, $sqlPrincipal, $parámetros = [])
{
    try {
        // Calcular el total de registros
        $countSql = "SELECT COUNT(*) FROM ($sqlPrincipal) AS total";
        $stmt = $conexion->prepare($countSql);
        $stmt->execute($parámetros);
        $totalRegistros = $stmt->fetchColumn();

        // Calcular el total de páginas
        $totalPaginas = ceil($totalRegistros / $porPagina);

        // Validar la página actual
        if ($paginaActual < 1) $paginaActual = 1;
        if ($paginaActual > $totalPaginas) $paginaActual = $totalPaginas;

        // Calcular el OFFSET
        $offset = ($paginaActual - 1) * $porPagina;

        // Consulta principal con paginación
        $sql = "$sqlPrincipal LIMIT :limite OFFSET :offset";

        // Añadir los parámetros
        $stmt = $conexion->prepare($sql);
        foreach ($parámetros as $clave => $valor) {
            $stmt->bindValue($clave, $valor); // Parámetros adicionales
        }
        $stmt->bindValue(':limite', (int)$porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Retornar los resultados y datos de paginación
        return [
            'resultados' => $resultados,
            'totalRegistros' => $totalRegistros,
            'totalPaginas' => $totalPaginas,
            'paginaActual' => $paginaActual
        ];
    } catch (PDOException $e) {
        die("Error en la consulta de paginación: " . $e->getMessage());
    }
}
