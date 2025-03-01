import sys
from io import BytesIO
from typing import Sequence

import cv2 as cv
import numpy as np
import requests

INPUT_WIDTH = 640
INPUT_HEIGHT = 480


def has_faces(img_url: str) -> bool:
    model = cv.FaceDetectorYN.create(
        model="face_detection_yunet_2023mar.onnx",
        config="",
        input_size=(INPUT_WIDTH, INPUT_HEIGHT),
        score_threshold=0.5,
        nms_threshold=0.3,
        top_k=2,
    )

    response = requests.get(img_url)
    image_bytes = BytesIO(response.content)
    image = cv.imdecode(np.frombuffer(image_bytes.read(), np.uint8), cv.IMREAD_COLOR)

    h, w, _ = image.shape
    input_size: Sequence[int] = (w, h)

    model.setInputSize(input_size)

    _, faces = model.detect(image)
    return faces is not None


if __name__ == "__main__":
    img_url = sys.argv[1]
    print(has_faces(img_url))
