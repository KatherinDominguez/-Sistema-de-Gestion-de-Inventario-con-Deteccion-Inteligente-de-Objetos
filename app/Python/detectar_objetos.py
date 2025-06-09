# detectar_objetos.py
import sys
import cv2
import numpy as np

def detectar(imagen_path, tipo, color):
    imagen = cv2.imread(imagen_path)
    if imagen is None:
        print("No se pudo abrir la imagen.")
        return

    hsv = cv2.cvtColor(imagen, cv2.COLOR_BGR2HSV)

    # Rangos HSV para colores
    rangos = {
        "rojo": [((0, 100, 100), (10, 255, 255)), ((160, 100, 100), (179, 255, 255))],
        "azul": [((100, 150, 0), (140, 255, 255))],
        "verde": [((40, 70, 70), (80, 255, 255))],
        "amarillo": [((20, 100, 100), (30, 255, 255))]
    }

    if color not in rangos:
        print(f"Color '{color}' no es válido.")
        return

    # Crear máscara combinada
    mascara_total = None
    for (lower, upper) in rangos[color]:
        lower_np = np.array(lower, dtype=np.uint8)
        upper_np = np.array(upper, dtype=np.uint8)
        mascara = cv2.inRange(hsv, lower_np, upper_np)
        mascara_total = mascara if mascara_total is None else cv2.bitwise_or(mascara_total, mascara)

    mascara_total = cv2.erode(mascara_total, None, iterations=2)
    mascara_total = cv2.dilate(mascara_total, None, iterations=2)

    # Encontrar contornos
    contornos, _ = cv2.findContours(mascara_total, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
    cantidad = len(contornos)

    print(f"Detectado {cantidad} objetos tipo '{tipo}' con color '{color}'.")

if __name__ == "__main__":
    if len(sys.argv) < 4:
        print("Faltan argumentos. Uso: python detectar_objetos.py <imagen> <tipo> <color>")
    else:
        detectar(sys.argv[1], sys.argv[2], sys.argv[3])
