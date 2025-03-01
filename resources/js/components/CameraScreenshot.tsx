import { useEffect, useRef } from "react";

const timeToTakePicInMilliSeconds = 15_000;
const canvasWidth = 640;
const canvasHeight = 480;

interface Props {
	onPermissionDenied: () => void;
	onCaptureSuccess: (blob: Blob | null) => void;
}

export default function CameraScreenshot({
	onPermissionDenied,
	onCaptureSuccess,
}: Props) {
	const videoRef = useRef<HTMLVideoElement>(null);
	const canvasRef = useRef<HTMLCanvasElement>(null);

	useEffect(() => {
		const startCamera = async () => {
			try {
				const stream = await navigator.mediaDevices.getUserMedia({
					video: true,
				});
				if (videoRef.current) {
					videoRef.current.srcObject = stream;
					videoRef.current.onloadedmetadata = () => {
						if (!canvasRef.current) return;

						canvasRef.current.width = canvasWidth;
						canvasRef.current.height = canvasHeight;

						setTimeout(captureScreenshot, timeToTakePicInMilliSeconds);
					};
				}
			} catch (error) {
				onPermissionDenied();
			}
		};

		const captureScreenshot = () => {
			if (!videoRef.current || !canvasRef.current) return;

			const ctx = canvasRef.current.getContext("2d");
			if (!ctx) return;

			ctx.drawImage(videoRef.current, 0, 0, 640, 480);

			canvasRef.current.toBlob((blob) => {
				onCaptureSuccess(blob);
				stopCamera();
			}, "image/png");
		};

		const stopCamera = () => {
			const stream = videoRef.current?.srcObject as MediaStream;
			stream?.getTracks().forEach((track) => track.stop());
		};

		startCamera();
		return stopCamera;
	}, []);

	return (
		<>
			<video ref={videoRef} autoPlay playsInline className="hidden" />
			<canvas ref={canvasRef} className="hidden" />
		</>
	);
}
