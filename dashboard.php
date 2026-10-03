<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Control Térmico - ConstruTec</title>
  <style>
    :root {
      --bg-color: #0f172a;
      --card-bg: #1e293b;
      --primary: #38bdf8;
      --accent-warm: #f97316;
      --accent-cold: #06b6d4;
      --text-main: #f8fafc;
      --text-muted: #94a3b8;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    body { background-color: var(--bg-color); color: var(--text-main); display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
    
    .container {
      background-color: var(--card-bg);
      width: 100%;
      max-width: 420px;
      border-radius: 24px;
      padding: 28px;
      box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5);
      border: 1px solid rgba(255,255,255,0.05);
    }

    .user-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      font-size: 0.85rem;
      color: var(--text-muted);
      border-bottom: 1px solid rgba(255,255,255,0.05);
      padding-bottom: 12px;
    }

    .user-bar b { color: var(--primary); }

    .btn-logout {
      color: #f87171;
      text-decoration: none;
      font-weight: 600;
      padding: 4px 10px;
      border-radius: 6px;
      background: rgba(248, 113, 113, 0.1);
    }

    header { text-align: center; margin-bottom: 24px; }
    header h1 { font-size: 1.4rem; font-weight: 600; }
    header p { font-size: 0.85rem; color: var(--text-muted); margin-top: 4px; }

    .sensors-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 28px; }
    .sensor-card {
      background: rgba(15, 23, 42, 0.6);
      padding: 16px;
      border-radius: 16px;
      border: 1px solid rgba(255, 255, 255, 0.05);
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .sensor-label { font-size: 0.8rem; color: var(--text-muted); margin-bottom: 6px; text-transform: uppercase; }
    .sensor-value { font-size: 1.5rem; font-weight: 700; color: var(--primary); }

    .control-section {
      display: flex;
      flex-direction: column;
      align-items: center;
      background: rgba(15, 23, 42, 0.4);
      padding: 20px;
      border-radius: 20px;
      border: 1px solid rgba(255, 255, 255, 0.03);
    }

    .control-title { font-size: 0.95rem; margin-bottom: 16px; font-weight: 500; }
    .dial-container { position: relative; width: 180px; height: 180px; display: flex; justify-content: center; align-items: center; }
    .dial-display { position: absolute; text-align: center; }
    .target-temp { font-size: 2.2rem; font-weight: 800; color: var(--accent-warm); transition: color 0.3s ease; }
    .target-unit { font-size: 0.8rem; color: var(--text-muted); }

    .status-badge {
      margin-top: 16px;
      padding: 6px 14px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    .last-update { margin-top: 12px; font-size: 0.7rem; color: var(--text-muted); text-align: center; }
  </style>
</head>
<body>

  <div class="container">
    <div class="user-bar">
      <span>Usuario: <b><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></b></span>
      <a href="logout.php" class="btn-logout">Salir</a>
    </div>

    <header>
      <h1>Control Térmico ConstruTec</h1>
      <p>Sistema de Juntas Bi-Climáticas</p>
    </header>

    <div class="sensors-grid">
      <div class="sensor-card">
        <span class="sensor-label">Temp. Ambiente</span>
        <span class="sensor-value" id="current-temp">-- °C</span>
      </div>
      <div class="sensor-card">
        <span class="sensor-label">Humedad</span>
        <span class="sensor-value" id="current-humidity">-- %</span>
      </div>
    </div>

    <div class="control-section">
      <span class="control-title">Temperatura del Agua (Cañería)</span>
      
      <div class="dial-container">
        <div class="dial-display">
          <div class="target-temp" id="target-val">-- °C</div>
          <div class="target-unit">SETPOINT POTENCIÓMETRO</div>
        </div>
      </div>

      <div class="status-badge" id="status-mode">ESPERANDO DATOS...</div>
      <div class="last-update" id="last-update">Buscando datos...</div>
    </div>
  </div>

  <script>
    async function actualizarLecturas() {
      try {
        const response = await fetch('api/obtener_lectura.php');
        const json = await response.json();

        if (json.status === 'success') {
          const data = json.data;

          document.getElementById('current-temp').textContent = `${parseFloat(data.temperatura_real).toFixed(1)}°C`;
          document.getElementById('current-humidity').textContent = `${parseFloat(data.humedad).toFixed(1)}%`;

          const setpoint = parseFloat(data.setpoint_potenciometro).toFixed(1);
          const targetVal = document.getElementById('target-val');
          const statusMode = document.getElementById('status-mode');
          const lastUpdate = document.getElementById('last-update');

          targetVal.textContent = `${setpoint}°C`;
          lastUpdate.textContent = `Última lectura: ${data.fecha_registro}`;

          if (parseInt(data.calefaccion_activa) === 1) {
            targetVal.style.color = '#f97316';
            statusMode.textContent = 'MODO EXPANSIÓN (INVIERNO / ON)';
            statusMode.style.color = '#f97316';
            statusMode.style.backgroundColor = 'rgba(249, 115, 22, 0.15)';
            statusMode.style.borderColor = 'rgba(249, 115, 22, 0.3)';
          } else {
            targetVal.style.color = '#06b6d4';
            statusMode.textContent = 'MODO CONTRACCIÓN / PASIVO (OFF)';
            statusMode.style.color = '#06b6d4';
            statusMode.style.backgroundColor = 'rgba(6, 182, 212, 0.15)';
            statusMode.style.borderColor = 'rgba(6, 182, 212, 0.3)';
          }
        }
      } catch (e) {
        console.error("Error al actualizar datos:", e);
      }
    }

    setInterval(actualizarLecturas, 2000);
    actualizarLecturas();
  </script>
</body>
</html>