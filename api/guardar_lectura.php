<?php
header("Content-Type: application/json; charset=UTF-8");
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $temp = isset($_POST['temperatura']) ? floatval($_POST['temperatura']) : null;
    $hum = isset($_POST['humedad']) ? floatval($_POST['humedad']) : null;
    $setpoint = isset($_POST['setpoint']) ? floatval($_POST['setpoint']) : null;
    $calefaccion = isset($_POST['calefaccion']) ? intval($_POST['calefaccion']) : null;

    if ($temp !== null && $hum !== null && $setpoint !== null && $calefaccion !== null) {
        try {
            $query = "INSERT INTO lecturas_temperatura (temperatura_real, humedad, setpoint_potenciometro, calefaccion_activa) 
                      VALUES (:temp, :hum, :setpoint, :calefaccion)";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':temp' => $temp,
                ':hum' => $hum,
                ':setpoint' => $setpoint,
                ':calefaccion' => $calefaccion
            ]);

            echo json_encode(["status" => "success", "message" => "Lectura guardada exitosamente"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Parámetros incompletos"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Método no permitido"]);
}
?>