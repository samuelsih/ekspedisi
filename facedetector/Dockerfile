FROM python:3.13-slim-bookworm

ENV PYTHONDONTWRITEBYTECODE=1
ENV PYTHONUNBUFFERED=1

ARG OPENCV_VERSION=4.11.0
ARG DEBIAN_FRONTEND=noninteractive

WORKDIR /app

COPY --from=ghcr.io/astral-sh/uv:latest /uv /uvx /bin/
COPY . /app

RUN apt-get update && apt-get install -y \
    libglib2.0-0 libsm6 libxext6 libxrender-dev libgl1 \
    && rm -rf /var/lib/apt/lists/* \
    && uv sync --frozen --no-dev

ENTRYPOINT [ "uv", "run", "main.py" ]
