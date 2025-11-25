<?php

namespace App\Helpers;
use Exception;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Writer\SvgWriter;
use Picqer\Barcode\BarcodeGeneratorSVG;

class BarcodeGenerator
{
    /**
     * Generate Code 128 barcode as SVG using Picqer library
     */
    public static function generateBarcode($code, $width = 2, $height = 50)
    {
        try {
            $generator = new BarcodeGeneratorSVG();
            // Generate Code 128 barcode
            $barcode = $generator->getBarcode($code, $generator::TYPE_CODE_128, $width, $height);
            // Wrap in a container div for better display
            return '<div style="display: inline-block;">' . $barcode . '</div>';
        } catch (Exception $e) {
            // Fallback to simple text if generation fails
            return '<div style="font-family: monospace; font-size: 14px; padding: 10px; background: #f0f0f0; border: 1px solid #ccc;">' .
                   htmlspecialchars($code) . '</div>';
        }
    }

    /**
     * Generate QR Code as SVG using Endroid library
     */
    public static function generateQRCode($data, $size = 200)
    {
        try {
            $result = Builder::create()
                ->writer(new SvgWriter())
                ->writerOptions([])
                ->data($data)
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
                ->size($size)
                ->margin(10)
                ->build();
            return $result->getString();
        } catch (Exception $e) {
            // Fallback if QR generation fails
            return '<div style="width: ' . $size . 'px; height: ' . $size . 'px; display: flex; align-items: center; justify-content: center; border: 2px dashed #ccc; background: #f9f9f9;">' .
                   '<div style="text-align: center; color: #666;">' .
                   '<div style="font-size: 48px;">⚠</div>' .
                   '<div style="font-size: 12px; margin-top: 10px;">QR Code<br>Generation Failed</div>' .
                   '</div></div>';
        }
    }

    /**
     * Generate a PNG barcode (for download/printing)
     */
    public static function generateBarcodePNG($code, $width = 2, $height = 50)
    {
        try {
            $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
            return $generator->getBarcode($code, $generator::TYPE_CODE_128, $width, $height);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Generate a PNG QR code (for download/printing)
     */
    public static function generateQRCodePNG($data, $size = 300)
    {
        try {
            $result = Builder::create()
                ->writer(new \Endroid\QrCode\Writer\PngWriter())
                ->data($data)
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
                ->size($size)
                ->margin(10)
                ->build();
            return $result->getString();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Generate a data URL for embedding (barcode)
     */
    public static function generateBarcodeDataURL($code, $width = 2, $height = 50)
    {
        $png = self::generateBarcodePNG($code, $width, $height);
        if ($png) {
            return 'data:image/png;base64,' . base64_encode($png);
        }
        return '';
    }

    /**
     * Generate a data URL for embedding (QR code)
     */
    public static function generateQRCodeDataURL($data, $size = 200)
    {
        $png = self::generateQRCodePNG($data, $size);
        if ($png) {
            return 'data:image/png;base64,' . base64_encode($png);
        }
        return '';
    }
}
