<?php
include 'header.php';

// --- Conversion Functions ---
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

function generateSafeVariableName($filename)
{
    return preg_replace('/[^A-Za-z0-9_]/', '_', pathinfo($filename, PATHINFO_FILENAME));
}

function generateHexOutput($hexCode, $varName, $library)
{
    $lines = explode(' ', trim($hexCode));
    $out = "#ifndef IMAGE_DATA_H\n#define IMAGE_DATA_H\n\n";

    if ($library === 'Adafruit GFX' || $library === 'TFT_eSPI') {
        $out .= "const uint16_t {$varName}[] PROGMEM = {\n";
        foreach ($lines as $line) {
            if (strlen($line) < 7) continue;
            $r = hexdec(substr($line, 1, 2));
            $g = hexdec(substr($line, 3, 2));
            $b = hexdec(substr($line, 5, 2));
            $color = (($r >> 3) << 11) | (($g >> 2) << 5) | ($b >> 3);
            $out .= '  0x' . strtoupper(str_pad(dechex($color), 4, '0', STR_PAD_LEFT)) . ',';
        }
    } else {
        $type = $library === 'U8g2' ? 'uint8_t' : 'char';
        $out .= "const {$type} {$varName}[] PROGMEM = {\n";
        foreach ($lines as $line) {
            if (strlen($line) < 7) continue;
            $r = hexdec(substr($line, 1, 2));
            $g = hexdec(substr($line, 3, 2));
            $b = hexdec(substr($line, 5, 2));
            $gray = intval(($r + $g + $b) / 3);
            $out .= '  0x' . strtoupper(str_pad(dechex($gray), 2, '0', STR_PAD_LEFT)) . ',';
        }
    }

    return rtrim($out, ',') . "\n};\n\n#endif";
}

function processGIFFrames($filePath)
{
    $frames = [];
    $imagick = new Imagick();
    $imagick->readImage($filePath);
    foreach ($imagick as $frame) {
        $frame->setImageFormat('png');
        $frames[] = imagecreatefromstring($frame->getImageBlob());
    }
    $imagick->clear();
    $imagick->destroy();
    return $frames;
}

function assembleAnimatedGIF($frames, $delay = 100)
{
    $imagickAnim = new Imagick();
    $imagickAnim->setFormat("gif");
    foreach ($frames as $frameRes) {
        ob_start();
        imagepng($frameRes);
        $pngBlob = ob_get_clean();
        $imFrame = new Imagick();
        $imFrame->readImageBlob($pngBlob);
        $imFrame->setImageDelay($delay);
        $imagickAnim->addImage($imFrame);
    }
    return $imagickAnim->getImagesBlob();
}

function createZipWithHeaders($results)
{
    $zipDir = 'downloads';
    if (!file_exists($zipDir)) mkdir($zipDir, 0755, true);

    $zipFile = $zipDir . '/converted_' . uniqid() . '.zip';
    $zip = new ZipArchive();
    if ($zip->open($zipFile, ZipArchive::CREATE) !== TRUE) {
        return false;
    }

    foreach ($results as $res) {
        if ($res['animated']) {
            $zip->addFromString($res['filename'] . '_animated.h', $res['combined_hex']);
        }

        foreach ($res['frames'] as $i => $frame) {
            $ext = pathinfo($res['filename'], PATHINFO_EXTENSION);
            $base = basename($res['filename'], ".$ext");
            $frameName = $base . '_frame' . $i . '.h';
            $zip->addFromString($frameName, $frame['hex']);
        }
    }

    $zip->close();
    return $zipFile;
}

// --- Main Block ---
$results = [];
$zipFilePath = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resolution = (!empty($_POST['width']) && !empty($_POST['height'])) ? $_POST['width'] . 'x' . $_POST['height'] : ($_POST['resolution'] ?? '128x128');
    $invert = isset($_POST['invert']);
    $library = $_POST['library'] ?? 'Adafruit GFX';

    foreach ($_FILES['images']['tmp_name'] as $index => $tmpPath) {
        if (!is_uploaded_file($tmpPath)) continue;

        $originalName = basename($_FILES['images']['name'][$index]);
        $varBase = generateSafeVariableName($originalName);
        $mime = mime_content_type($tmpPath);

        if ($mime !== 'image/gif') {
            $img = imagecreatefromstring(file_get_contents($tmpPath));
            list($resized, $hex) = convertImageToHex($img, $resolution, $invert);
            ob_start();
            imagepng($resized);
            $b64 = base64_encode(ob_get_clean());

            $results[] = [
                'filename' => $originalName,
                'frames' => [[
                    'img' => $b64,
                    'hex' => generateHexOutput($hex, $varBase, $library),
                ]],
                'animated' => null,
                'combined_hex' => null,
            ];
            imagedestroy($resized);
        } else {
            $frames = processGIFFrames($tmpPath);
            $frameResults = [];
            $combinedHex = '';
            $convertedFrames = [];

            foreach ($frames as $i => $frame) {
                list($resized, $hex) = convertImageToHex($frame, $resolution, $invert);
                ob_start();
                imagepng($resized);
                $b64 = base64_encode(ob_get_clean());
                $frameResults[] = [
                    'img' => $b64,
                    'hex' => generateHexOutput($hex, $varBase . "_f{$i}", $library),
                ];
                $combinedHex .= $hex . "\n";
                $convertedFrames[] = $resized;
                imagedestroy($frame);
            }

            $animated = base64_encode(assembleAnimatedGIF($convertedFrames));
            foreach ($convertedFrames as $img) imagedestroy($img);

            $results[] = [
                'filename' => $originalName,
                'frames' => $frameResults,
                'animated' => $animated,
                'combined_hex' => generateHexOutput($combinedHex, $varBase . "_animated", $library),
            ];
        }
    }

    if (!empty($results)) {
        $zipFilePath = createZipWithHeaders($results);
    }
}
?>

<div class="container">
    <div class="form-section">
        <h1>Upload Images</h1>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="images[]" multiple required>
            <label for="resolution">Select Resolution:</label>
            <select name="resolution" id="resolution">
                <option value="128x128">128x128</option>
                <option value="256x256">256x256</option>
                <option value="512x512">512x512</option>
            </select><br><br>

            <label for="invert">Invert Image Colors:</label>
            <input type="checkbox" name="invert" id="invert" value="1"><br><br>

            <label for="library">Select LCD Library:</label>
            <select name="library" id="library">
                <option value="Adafruit GFX">Adafruit GFX</option>
                <option value="U8g2">U8g2</option>
                <option value="TFT_eSPI">TFT_eSPI</option>
                <option value="LiquidCrystal">LiquidCrystal</option>
            </select><br><br>
            <div class="btn">
                <button type="submit">Convert</button>

            </div>
        </form>
    </div>

    <?php if (!empty($results)): ?>
        <div class="conversion-result">
            <?php if ($zipFilePath && file_exists($zipFilePath)): ?>
                <hr>
                <a href="<?= htmlspecialchars($zipFilePath) ?>" download class="btn btn-primary">Download All as ZIP</a>
            <?php endif; ?>

            <?php foreach ($results as $res): ?>
                <hr>
                <h3><?= htmlspecialchars($res['filename']) ?></h3>

                <?php if ($res['animated']): ?>
                    <p><strong>Animated Preview:</strong></p>
                    <img src="data:image/gif;base64,<?= $res['animated'] ?>" width="128">
                    <pre><?= htmlspecialchars($res['combined_hex']) ?></pre>
                <?php endif; ?>

                <?php foreach ($res['frames'] as $i => $frame): ?>
                    <h4>Frame <?= $i ?></h4>
                    <img src="data:image/png;base64,<?= $frame['img'] ?>" width="128">
                    <pre><?= htmlspecialchars($frame['hex']) ?></pre>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>

</html>