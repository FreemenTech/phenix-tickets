<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class TicketPoster
{
    private static function load(?string $path)
    {
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        $source = @imagecreatefromstring(Storage::disk('public')->get($path));

        return $source ?: null;
    }

    /** Affiche réduite (et assombrie) en data URI. */
    public static function dataUri(?string $path, int $width = 1100, bool $darken = true): ?string
    {
        $source = self::load($path);

        if (! $source) {
            return null;
        }

        $w = imagesx($source);
        $h = imagesy($source);
        $newW = min($width, $w);
        $newH = (int) round($h * $newW / $w);

        $canvas = imagecreatetruecolor($newW, $newH);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $newW, $newH, $w, $h);

        if ($darken) {
            imagealphablending($canvas, true);
            imagefilledrectangle($canvas, 0, 0, $newW, $newH, imagecolorallocatealpha($canvas, 43, 10, 31, 40));
        }

        ob_start();
        imagejpeg($canvas, null, 78);
        $jpeg = ob_get_clean();

        return 'data:image/jpeg;base64,'.base64_encode($jpeg);
    }

    /** Bandeau paysage (recadré au centre) en JPEG brut, pour l'en-tête d'un billet dans un email. */
    public static function banner(?string $path, int $width = 1200, int $height = 420, int $focusX = 50, int $focusY = 50): ?string
    {
        $source = self::load($path);

        if (! $source) {
            return null;
        }

        $w = imagesx($source);
        $h = imagesy($source);

        $scale = max($width / $w, $height / $h);
        $cropW = (int) round($width / $scale);
        $cropH = (int) round($height / $scale);
        $srcX = (int) max(0, round(($w - $cropW) * $focusX / 100));
        $srcY = (int) max(0, round(($h - $cropH) * $focusY / 100));

        $canvas = imagecreatetruecolor($width, $height);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 43, 10, 31));
        imagecopyresampled($canvas, $source, 0, 0, $srcX, $srcY, $width, $height, $cropW, $cropH);

        ob_start();
        imagejpeg($canvas, null, 80);

        return ob_get_clean();
    }

    /**
     * Fond de la carte du billet PDF : affiche recadrée aux proportions de la carte,
     * assombrie, avec des coins arrondis (en blanc, comme la page). Sans affiche : fond violet nuit.
     */
    public static function card(?string $path, int $width = 1428, int $height = 860, int $radius = 56, int $focusX = 50, int $focusY = 50): string
    {
        $canvas = imagecreatetruecolor($width, $height);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 43, 10, 31));

        $source = self::load($path);

        if ($source) {
            $w = imagesx($source);
            $h = imagesy($source);

            $scale = max($width / $w, $height / $h);
            $cropW = (int) round($width / $scale);
            $cropH = (int) round($height / $scale);
            $srcX = (int) max(0, round(($w - $cropW) * $focusX / 100));
            $srcY = (int) max(0, round(($h - $cropH) * $focusY / 100));

            imagecopyresampled($canvas, $source, 0, 0, $srcX, $srcY, $width, $height, $cropW, $cropH);

            imagealphablending($canvas, true);
            imagefilledrectangle($canvas, 0, 0, $width, $height, imagecolorallocatealpha($canvas, 43, 10, 31, 32));
        }

        self::whiteCorners($canvas, $width, $height, $radius);

        ob_start();
        imagejpeg($canvas, null, 82);
        $jpeg = ob_get_clean();

        return 'data:image/jpeg;base64,'.base64_encode($jpeg);
    }

    private static function whiteCorners($canvas, int $width, int $height, int $radius): void
    {
        $white = imagecolorallocate($canvas, 255, 255, 255);

        for ($y = 0; $y < $radius; $y++) {
            for ($x = 0; $x < $radius; $x++) {
                if (hypot($radius - $x - 0.5, $radius - $y - 0.5) > $radius) {
                    imagesetpixel($canvas, $x, $y, $white);
                    imagesetpixel($canvas, $width - 1 - $x, $y, $white);
                    imagesetpixel($canvas, $x, $height - 1 - $y, $white);
                    imagesetpixel($canvas, $width - 1 - $x, $height - 1 - $y, $white);
                }
            }
        }
    }
}
