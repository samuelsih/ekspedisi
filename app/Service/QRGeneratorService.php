<?php

namespace App\Service;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRGeneratorService
{
    public function __invoke(string $qrContent, ?string $qrName = null): string
    {
        if (empty($qrName)) {
            return QrCode::format('png')
                ->size(300)
                ->margin(5)
                ->color(0, 0, 0)
                ->backgroundColor(255, 255, 255)
                ->errorCorrection('H')
                ->generate($qrContent);
        }

        $qrData = QrCode::format('png')
            ->size(300)
            ->margin(5)
            ->color(0, 0, 0)
            ->backgroundColor(255, 255, 255)
            ->errorCorrection('H')
            ->generate($qrContent);

        $qrImage = imagecreatefromstring($qrData);
        if (! $qrImage) {
            throw new \RuntimeException('Cannot generate qr code, QR Data is null');
        }

        $qrWidth = imagesx($qrImage);
        $qrHeight = imagesy($qrImage);

        $padding = 20;
        $fontSize = 5;
        $textHeight = imagefontheight($fontSize);
        $textWidth = imagefontwidth($fontSize) * strlen($qrName);
        $width = max($qrWidth, $textWidth + 20);
        $height = $qrHeight + $textHeight + $padding;

        $finalImage = imagecreatetruecolor($width, $height);
        if (! $finalImage) {
            throw new \RuntimeException('Cannot generate QR image canvas');
        }

        $white = imagecolorallocate($finalImage, 255, 255, 255);
        $black = imagecolorallocate($finalImage, 0, 0, 0);
        imagefill($finalImage, 0, 0, $white);

        imagecopy($finalImage, $qrImage, ($width - $qrWidth) / 2, 0, 0, 0, $qrWidth, $qrHeight);

        imagestring($finalImage, $fontSize, ($width - $textWidth) / 2, $qrHeight + 5, $qrName, $black);

        ob_start();
        imagepng($finalImage);
        $imageData = ob_get_clean();

        imagedestroy($qrImage);
        imagedestroy($finalImage);

        return $imageData;
    }
}
