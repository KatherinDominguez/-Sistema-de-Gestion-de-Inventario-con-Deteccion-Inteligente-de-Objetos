
import sys
import cv2
import numpy as np

def detectar(imagen_path, forma_esperada, color_esperado, nombre=None):
    # Cargar imagen
    imagen = cv2.imread(imagen_path)
    if imagen is None:
        print(f"ERROR: No se pudo cargar la imagen '{imagen_path}'")
        sys.exit(1)

    # Convertir a HSV
    hsv = cv2.cvtColor(imagen, cv2.COLOR_BGR2HSV)

    # Definir rangos de color en HSV
    color_ranges = {
        'rojo': [((0, 70, 50), (10, 255, 255)), ((170, 70, 50), (180, 255, 255))],
        'azul': [((100, 150, 0), (140, 255, 255))],
        'verde': [((40, 70, 70), (80, 255, 255))],
        'amarillo': [((20, 100, 100), (30, 255, 255))],
        'morado': [((130, 50, 50), (160, 255, 255))],
        'naranja': [((10, 100, 20), (25, 255, 255))],
        'rosa': [((160, 50, 50), (170, 255, 255))],
        'gris': [((0, 0, 50), (180, 50, 200))],
        'blanco': [((0, 0, 200), (180, 25, 255))],
        'negro': [((0, 0, 0), (180, 255, 30))]
    }

    if color_esperado not in color_ranges:
        print(f"ERROR: Color '{color_esperado}' no es válido.")
        sys.exit(1)

    # Máscara de color
    mask = None
    for (lower, upper) in color_ranges[color_esperado]:
        lower_np = np.array(lower, dtype=np.uint8)
        upper_np = np.array(upper, dtype=np.uint8)
        mask_color = cv2.inRange(hsv, lower_np, upper_np)
        mask = mask_color if mask is None else cv2.bitwise_or(mask, mask_color)

    # Refinar máscara
    kernel = cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (5, 5))
    mask = cv2.morphologyEx(mask, cv2.MORPH_CLOSE, kernel, iterations=2)
    mask = cv2.morphologyEx(mask, cv2.MORPH_OPEN, kernel, iterations=1)

    # Encontrar contornos
    contornos, _ = cv2.findContours(mask, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)

    contador = 0
    detalles = []

    for cnt in contornos:
        area = cv2.contourArea(cnt)
        if area < 500:  # filtrar ruido
            continue

        # Aproximar contorno
        perimetro = cv2.arcLength(cnt, True)
        epsilon = 0.02 * perimetro
        approx = cv2.approxPolyDP(cnt, epsilon, True)

        # Calcular bounding box
        x, y, w, h = cv2.boundingRect(cnt)

        # Determinar forma
        forma_detectada = 'otra'

        # 1. Círculo (por circularidad)
        if perimetro > 0:
            circularidad = 4 * np.pi * area / (perimetro ** 2)
            if circularidad > 0.8:
                forma_detectada = 'circular'
        # 2. Polígono de 3 lados → Triángulo
        if forma_detectada == 'otra' and len(approx) == 3:
            forma_detectada = 'triangular'
        # 3. Polígono de 4 lados → Cuadrado o Rectángulo
        elif forma_detectada == 'otra' and len(approx) == 4:
            # Calcular longitudes de lados
            lados = []
            for i in range(4):
                p1 = approx[i][0]
                p2 = approx[(i+1) % 4][0]
                lados.append(np.linalg.norm(p2 - p1))
            # Relación de lados
            ratio_lados = max(lados) / (min(lados) if min(lados) > 0 else 1)
            if ratio_lados < 1.1:
                forma_detectada = 'cuadrada'
            else:
                forma_detectada = 'rectangular'
        # 4. Cilíndrico (basado en proporción ancho/alto)
        elif forma_detectada == 'otra':
            razon = w / h if h > 0 else 0
            if 0.3 <= razon <= 0.7:
                forma_detectada = 'cilindrico'
            elif 0.7 < razon < 1.1:
                # ya filtrados cuadrados, pero relajar para rectángulos no extremos
                forma_detectada = 'rectangular'
            elif razon >= 1.1:
                forma_detectada = 'rectangular-horizontal'
            elif razon <= 0.3:
                forma_detectada = 'rectangular-vertical'

        # Guardar si coincide con lo esperado
        if forma_detectada == forma_esperada:
            contador += 1
            detalles.append((forma_detectada, w, h))

    # Resultado
    nombre_info = f"'{nombre}' " if nombre else ''
    if contador > 0:
        líneas = [f"Detected: {contador} objeto(s) {nombre_info}(forma='{forma_esperada}', color='{color_esperado}')"]
        for fd, w, h in detalles:
            líneas.append(f"  - {fd}: ancho={w}, alto={h}")
        print("\n".join(líneas))
    else:
        print(f"No se detectaron objetos {nombre_info}con forma '{forma_esperada}' y color '{color_esperado}'.")

if __name__ == '__main__':
    if len(sys.argv) < 4 or len(sys.argv) > 5:
        print("ERROR: Uso esperado: python detectar_objetos_mejorado.py <ruta_imagen> <forma_esperada> <color_esperado> [<nombre>]")
        sys.exit(1)
    ruta = sys.argv[1]
    forma = sys.argv[2]
    color = sys.argv[3]
    nombre = sys.argv[4] if len(sys.argv) == 5 else None
    detectar(ruta, forma, color, nombre)
