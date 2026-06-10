<?php

function generateSafeVariableName($filename)
{
    return preg_replace('/[^A-Za-z0-9_]/', '_', pathinfo($filename, PATHINFO_FILENAME));
}

function convertImageToHex($image, $resolution, $invert = false)
{
    list($newWidth, $newHeight) = explode('x', $resolution);
    $imgResized = imagescale($image, $newWidth, $newHeight);
    if ($invert) imagefilter($imgResized, IMG_FILTER_NEGATE);

    $hexCode = '';
    for ($y = 0; $y < imagesy($imgResized); $y++) {
        for ($x = 0; $x < imagesx($imgResized); $x++) {
            $rgb = imagecolorat($imgResized, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            $hexCode .= sprintf("#%02x%02x%02x ", $r, $g, $b);
        }
    }
    return [$imgResized, $hexCode];
}

function generateHexOutput($hexCode, $varName, $library)
{
    $lines = explode(' ', trim($hexCode));
    $out = "const ";
    if ($library === 'Adafruit GFX' || $library === 'TFT_eSPI') {
        $out .= "uint16_t {$varName}[] PROGMEM = {\n";
        foreach ($lines as $line) {
            if (strlen($line) < 7) continue;
            $r = hexdec(substr($line, 1, 2));
            $g = hexdec(substr($line, 3, 2));
            $b = hexdec(substr($line, 5, 2));
            $color = (($r >> 3) << 11) | (($g >> 2) << 5) | ($b >> 3);
            $out .= '  0x' . strtoupper(str_pad(dechex($color), 4, '0', STR_PAD_LEFT)) . ',' . PHP_EOL;
        }
    } else {
        $type = $library === 'U8g2' ? 'uint8_t' : 'char';
        $out .= "{$type} {$varName}[] PROGMEM = {\n";
        foreach ($lines as $line) {
            if (strlen($line) < 7) continue;
            $r = hexdec(substr($line, 1, 2));
            $g = hexdec(substr($line, 3, 2));
            $b = hexdec(substr($line, 5, 2));
            $gray = intval(($r + $g + $b) / 3);
            $out .= '  0x' . strtoupper(str_pad(dechex($gray), 2, '0', STR_PAD_LEFT)) . ',' . PHP_EOL;
        }
    }
    return rtrim($out, ",\n") . "\n};";
}
function extractGifFrames($gifPath)
{
    $frames = [];
    $image = new Imagick($gifPath);
    $image = $image->coalesceImages();

    foreach ($image as $frame) {
        $frame->setImageFormat('png');
        $framePng = imagecreatefromstring($frame->getImageBlob());
        if ($framePng) {
            $frames[] = $framePng;
        }
    }

    return $frames;
}

function convertImage($image, $filename, $resolution, $invert, $library, $saveToDisk = false)
{
    $varBase = generateSafeVariableName($filename);
    list($resized, $hex) = convertImageToHex($image, $resolution, $invert);
    $headerCode = generateHexOutput($hex, $varBase, $library);

    ob_start();
    imagepng($resized);
    $convertedPng = ob_get_clean();
    imagedestroy($resized);

    // Only save if explicitly requested
    $headerFilename = $varBase . '.h';
    $headerPath = __DIR__ . '/../downloads/' . $headerFilename;
    if ($saveToDisk) {
        if (!file_exists(dirname($headerPath))) mkdir(dirname($headerPath), 0755, true);
        file_put_contents($headerPath, $headerCode);
    }

    return [
        'filename' => $filename,
        'frames' => [[
            'frame_index' => 0,
            'variable_name' => $varBase,
            'hex_header' => $headerCode,
            'download_url' => $saveToDisk ? 'http://localhost/hex_conversion/downloads/' . $headerFilename : null,
            'converted_image_base64' => 'data:image/png;base64,' . base64_encode($convertedPng)
        ]]
    ];
}

