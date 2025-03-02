from flask import Flask, request
from granian import Granian
from granian.constants import Interfaces
from io import BytesIO

import cv2 as cv
import numpy as np
import requests

INPUT_WIDTH = 640
INPUT_HEIGHT = 480

MODEL = cv.FaceDetectorYN.create(
    model="face_detection_yunet_2023mar.onnx",
    config="",
    input_size=(INPUT_WIDTH, INPUT_HEIGHT),
    score_threshold=0.5,
    nms_threshold=0.3,
    top_k=2,
)

MODEL.setInputSize((INPUT_WIDTH, INPUT_HEIGHT))

app = Flask(__name__)

@app.route("/")
def has_faces():
    img_url = request.args.get('img_url')
    response = requests.get(img_url)
    image_bytes = BytesIO(response.content)
    image = cv.imdecode(np.frombuffer(image_bytes.read(), np.uint8), cv.IMREAD_COLOR)

    _, faces = MODEL.detect(image)
    if faces is not None:
        return "True"

    return "False"


if __name__ == "__main__":
    server = Granian(
        target="main:app",
        address="0.0.0.0",
        port=5000,
        interface=Interfaces.WSGI,
    )

    server.serve()
