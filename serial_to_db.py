import serial
import requests
import re
import time

# Reemplazar por el puerto de tu Arduino (ejemplo 'COM3' o '/dev/ttyUSB0')
PUERTO_SERIE = 'COM7'
BAUD_RATE = 9600

# URL ajustada a la carpeta ConstruTec
URL_API = 'http://localhost/ConstruTec/api/guardar_lectura.php'

def iniciar_lectura():
    try:
        arduino = serial.Serial(PUERTO_SERIE, BAUD_RATE, timeout=2)
        time.sleep(2)
        print(f"--> Conectado en puerto {PUERTO_SERIE}")
        print("--> Recibiendo datos de Arduino y enviando a ConstruTec API...")

        while True:
            if arduino.in_waiting > 0:
                linea = arduino.readline().decode('utf-8', errors='ignore').strip()

                patron = r"Temp Real:\s*([\d\.]+).*?Humedad:\s*([\d\.]+).*?Setpoint \(POT\):\s*([\d\.]+).*?Calefacción:\s*(ON|OFF)"
                coincidencia = re.search(patron, linea)

                if coincidencia:
                    temp_real = float(coincidencia.group(1))
                    humedad = float(coincidencia.group(2))
                    setpoint = float(coincidencia.group(3))
                    estado_calefaccion = 1 if coincidencia.group(4) == "ON" else 0

                    payload = {
                        'temperatura': temp_real,
                        'humedad': humedad,
                        'setpoint': setpoint,
                        'calefaccion': estado_calefaccion
                    }

                    try:
                        respuesta = requests.post(URL_API, data=payload, timeout=3)
                        if respuesta.status_code == 200:
                            print(f"[OK] Temp: {temp_real}°C | Pot: {setpoint}°C | Estado: {coincidencia.group(4)}")
                        else:
                            print(f"[ERROR API]: Código HTTP {respuesta.status_code}")
                    except requests.exceptions.RequestException as e:
                        print(f"[ERROR CONEXIÓN]: {e}")

            time.sleep(0.5)

    except serial.SerialException as e:
        print(f"Error al abrir el puerto {PUERTO_SERIE}: {e}")
    except KeyboardInterrupt:
        print("\nPrograma finalizado.")
        if 'arduino' in locals() and arduino.is_open:
            arduino.close()

if __name__ == '__main__':
    iniciar_lectura()