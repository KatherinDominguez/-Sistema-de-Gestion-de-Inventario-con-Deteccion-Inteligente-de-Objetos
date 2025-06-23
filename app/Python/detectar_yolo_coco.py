#!/usr/bin/env python3
"""
Detectar objetos 'bottle','cup','wine glass'… usando YOLOv5s pre-entrenado en COCO.
Salida JSON con conteo y bounding-boxes.
Uso:
    python detectar_yolo_coco.py <ruta_imagen> [<nombre_opcional>]
"""

import sys, json, torch

def main(ruta_imagen, nombre=None):
    # 1. Cargar modelo YOLOv5s desde Torch Hub (pre-entrenado en COCO)
    model = torch.hub.load('ultralytics/yolov5', 'yolov5s', pretrained=True)
    # 2. Inferencia sobre la imagen
    results = model(ruta_imagen)
    # 3. Convertir a DataFrame pandas
    df = results.pandas().xyxy[0]

    # 4. Filtrar por clases de interés
    clases_interes = ['bottle', 'cup', 'wine glass']
    df = df[df['name'].isin(clases_interes)]

    # 5. Construir salida
    detecciones = []
    for _, row in df.iterrows():
        detecciones.append({
            'clase': row['name'],
            'conf': float(row['confidence']),
            'bbox': [float(row['xmin']), float(row['ymin']),
                     float(row['xmax']), float(row['ymax'])]
        })

    salida = {
        'count': len(detecciones),
        'nombre': nombre or '',
        'detecciones': detecciones
    }
    print(json.dumps(salida))

if __name__ == '__main__':
    if len(sys.argv) < 2 or len(sys.argv) > 3:
        print('ERROR: Uso: python detectar_yolo_coco.py <ruta_imagen> [<nombre>]')
        sys.exit(1)
    ruta, nombre = sys.argv[1], (sys.argv[2] if len(sys.argv)==3 else None)
    main(ruta, nombre)
