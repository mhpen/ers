<?php
require '../../vendor/autoload.php';
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class QRCodeHelper {
    public function generateQRCode($data) {
        try {
            $qrCode = QrCode::create($data)
                ->setSize(300)
                ->setMargin(10);

            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            // Generate unique filename
            $filename = 'qr_' . uniqid() . '.png';
            $path = '../../public/qrcodes/' . $filename;

            // Save QR code
            $result->saveToFile($path);

            return $path;
        } catch (Exception $e) {
            error_log("QR Code generation error: " . $e->getMessage());
            return false;
        }
    }
} 