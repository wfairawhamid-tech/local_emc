<?php

namespace App\Libraries;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRMarkupSVG;

class QRCodeGenerator
{
    /**
     * Generate inline SVG string for the given data/URL.
     *
     * @param string $data The URL or payload to encode
     * @param int $scale Scale multiplier for the QR SVG
     * @param string $cssClass Optional CSS class name
     * @return string Raw SVG markup
     */
    public static function svg(string $data, int $scale = 3, string $cssClass = 'qr-svg'): string
    {
        $options = new QROptions();
        $options->outputInterface  = QRMarkupSVG::class;
        $options->outputBase64     = false;
        $options->svgAddXmlHeader  = false;
        $options->eccLevel         = EccLevel::M;
        $options->addQuietzone     = true;
        $options->quietzoneSize    = 1;
        $options->scale            = $scale;
        $options->cssClass         = $cssClass;

        $qrcode = new QRCode($options);
        return $qrcode->render($data);
    }

    /**
     * Generate data URI format for <img> tag (e.g. src="data:image/svg+xml;base64,...")
     */
    public static function svgDataUri(string $data, int $scale = 3): string
    {
        $svg = self::svg($data, $scale);
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
