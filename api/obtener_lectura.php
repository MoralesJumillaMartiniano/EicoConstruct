<?php
header("Content-Type: application/json; charset=UTF-8");
require_once 'config.php';

try {
    $query = "SELECT temperatura_real, humedad, setpoint_potenciometro, calefaccion_activa, fecha_registro 
              FROM lecturas_temperatura 
              ORDER BY id DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $data = $stmt->fetch();

    if ($data) {
        echo json_encode(["status" => "success", "data" => $data]);
    } else {
        echo json_encode(["status" => "empty", "message" => "Sin datos registrados aún"]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>