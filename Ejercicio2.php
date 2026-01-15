<?php
$archivo_registros = 'registros.txt';
$archivo_log = 'errores.log';
// Funcion para guardar los errores que salen en el archivo errores.log

function logError($mensaje, $archivo = 'ej30.php', $linea = null) {
    global $archivo_log;
    $fecha = date('Y-m-d H:i:s');
    $linea = $linea ?? debug_backtrace()[0]['line'];
    $log = "$fecha | EJ30 | $mensaje | $archivo | $linea\n";
    file_put_contents($archivo_log, $log, FILE_APPEND | LOCK_EX);
}
$registros = [];
$error = false;
// Comprobamos si hay algun problema con el archivo del registro
try {
    if (!file_exists($archivo_registros)) {
        throw new Exception("El registros.txt no se encuentra");
    }
    $lineas = file($archivo_registros, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lineas === false) {
        throw new Exception("No se pudo leer la linea");
    }
    foreach ($lineas as $num_linea => $linea) {
        $partes = array_map('trim', explode('|', $linea));
        if (count($partes) !== 5) {
            $msg = "Esta linea esta mal: '$linea'";
            logError($msg, 'ej30.php', __LINE__);
        }
        $registros[] = $partes;
    }
} catch (Exception $e) {
    $error = true;
    logError($e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registros</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <!-- Utilizamos parte php para poner en los apartados que no puede haber informacion en el registro para poner un mensaje -->
    <h2>Registros guardados</h2>
    <?php if ($error): ?>
        <p>No hay nada en los registros XD</p>
    <?php elseif (empty($registros)): ?>
        <p>No hay nada en los registros XD</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Fecha y hora</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Edad</th>
                    <th>Comentario</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($registros as $fila): ?>
                    <tr>
                        <td><?= htmlspecialchars($fila[0]) ?></td>
                        <td><?= htmlspecialchars($fila[1]) ?></td>
                        <td><?= htmlspecialchars($fila[2]) ?></td>
                        <td><?= htmlspecialchars($fila[3]) ?></td>
                        <td><?= htmlspecialchars($fila[4]) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
