<?php
$archivo_registros = 'registros.txt';
$archivo_log = 'errores.log';
// Funcion para guardar los errores que salen en el archivo errores.log

function logError($mensaje, $archivo = 'ej29.php', $linea = null) {
    global $archivo_log;
    $fecha = date('Y-m-d H:i:s');
    $linea = $linea ?? debug_backtrace()[0]['line'];
    $log = "$fecha | EJ29 | $mensaje | $archivo | $linea\n";
    file_put_contents($archivo_log, $log, FILE_APPEND | LOCK_EX);
}
function validarFormulario($datos) {
    $errores = [];
    // Funciones para el nombre

    if (empty(trim($datos['nombre']))) {
        $errores[] = "No lo puedes dejar en blanco";
    } elseif (strlen(trim($datos['nombre'])) < 3) {
        $errores[] = "El nombre no puede ser menor que 3 letras , ponte otro nombre XD";
    }
    // Funciones para el email

    $email = trim($datos['email']);
    if (empty($email)) {
        $errores[] = "No lo puedes dejar en blanco";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Ese correo no sirve sorry T.T";
    }
    // Funciones para la edad

    $edad = trim($datos['edad']);
    if (empty($edad)) {
        $errores[] = "No lo puedes dejar en blanco";
    } elseif (!is_numeric($edad) || intval($edad) != $edad || $edad < 0 || $edad > 120) {
        $errores[] = "Ese numero no vale , no te sabes tu edad o eres muy viejo , decidete";
    }
    // Funciones para poner un comentario en el formulario

    $comentario = trim($datos['comentario']);
    if (strlen($comentario) > 200) {
        $errores[] = "No me cuentes tu vida menos de 200 caracteres por favor XD";
    }

    if (!empty($errores)) {
        throw new Exception(implode(" ", $errores));
    }
}
// Funcion en la que te permite gusrdar la informacion que pones en la parte del formulario
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        validarFormulario($_POST);
        $nombre = htmlspecialchars(trim($_POST['nombre']), ENT_QUOTES, 'UTF-8');
        $email = htmlspecialchars(trim($_POST['email']), ENT_QUOTES, 'UTF-8');
        $edad = intval($_POST['edad']);
        $comentario = htmlspecialchars(trim($_POST['comentario']), ENT_QUOTES, 'UTF-8');
        $fecha = date('Y-m-d H:i:s');
        $registro = "$fecha | $nombre | $email | $edad | $comentario\n";
        if (!file_put_contents($archivo_registros, $registro, FILE_APPEND | LOCK_EX)) {
            throw new Exception("No escribas en este archivo pls :(");
        }
        $mensaje = "<p style='color:green;'>Lo hemos guardado :D</p>";
    } catch (Exception $e) {
        $mensaje = "<p style='color:red;'>Ta mal: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
        logError($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario</title>
</head>
<body>
    <h2>Formulario de Registro</h2>
    <?= $mensaje ?>
    <form method="POST" action="">
        <label>Nombre:<br>
            <input type="text" name="nombre" required>
        </label><br><br>
        <label>Email:<br>
            <input type="email" name="email" required>
        </label><br><br>
        <label>Edad:<br>
            <input type="number" name="edad" min="0" max="120" required>
        </label><br><br>
        <label>Comentario:<br>
            <textarea name="comentario" maxlength="200"></textarea>
        </label><br><br>
        <button type="submit">Enviar</button>
    </form>
</body>
</html>
