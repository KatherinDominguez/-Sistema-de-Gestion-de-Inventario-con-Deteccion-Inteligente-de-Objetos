import sys
import cv2
import numpy as np

def obtener_rango_color(color):
    color = color.lower()
    if color == "rojo":
        return [([0, 120, 70], [10, 255, 255]), ([170, 120, 70], [180, 255, 255])]
    elif color == "verde":
        return [([36, 25, 25], [86, 255, 255])]
    elif color == "azul":
        return [([94, 80, 2], [126, 255, 255])]
    elif color == "amarillo":
        return [([15, 150, 150], [35, 255, 255])]
    else:
        return []

def detectar(video_path, tipo, color):
    try:
        cap = cv2.VideoCapture(video_path)
        if not cap.isOpened():
            print("ERROR: No se pudo abrir el video.")
            sys.stdout.flush()
            return

        rangos = obtener_rango_color(color)
        if not rangos:
            print("ERROR: Color no reconocido.")
            sys.stdout.flush()
            return

        total_objetos = 0
        total_frames = 0
        frame_interval = 10

        while True:
            ret, frame = cap.read()
            if not ret:
                break
            total_frames += 1

            if total_frames % frame_interval != 0:
                continue

            hsv = cv2.cvtColor(frame, cv2.COLOR_BGR2HSV)
            mascara_total = None

            for rango in rangos:
                lower = np.array(rango[0])
                upper = np.array(rango[1])
                mascara = cv2.inRange(hsv, lower, upper)
                if mascara_total is None:
                    mascara_total = mascara
                else:
                    mascara_total = cv2.bitwise_or(mascara_total, mascara)

            contornos, _ = cv2.findContours(mascara_total, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)

            objetos_en_frame = 0
            for contorno in contornos:
                area = cv2.contourArea(contorno)
                if area > 500:
                    objetos_en_frame += 1

            if objetos_en_frame > 0:
                total_objetos += objetos_en_frame

        cap.release()

        mensaje = f"Procesado {total_frames} frames. Detectado {total_objetos} objetos tipo '{tipo}' con color '{color}' en el video."
        print(mensaje)
        sys.stdout.flush()

    except Exception as e:
        print(f"ERROR: {str(e)}")
        sys.stdout.flush()

if __name__ == "__main__":
    if len(sys.argv) < 4:
        print("ERROR: Faltan argumentos. Uso: python detectar_objetos_video.py <video> <tipo> <color>")
        sys.stdout.flush()
    else:
        detectar(sys.argv[1], sys.argv[2], sys.argv[3])
