<?php
include 'header.php';
?>

<div class="container">
    <div class="form-section">
        <h1>Select Multiple Pre-stored Images</h1>
        <form method="post" enctype="multipart/form-data">
            <div class="image_library">
                <?php
                function getImageLibrary()
                {
                    $dir = 'image_library/';
                    return array_map('basename', glob($dir . "*.{jpg,jpeg,png,gif,PNG,JPG,JPEG}", GLOB_BRACE));
                }

                $images = getImageLibrary();
                foreach ($images as $index => $image) {
                    echo '
                <label style="display:inline-block; margin:10px;">
                    <input type="checkbox" name="prestored_images[]" value="' . $image . '" />
                    <img src="image_library/' . $image . '" alt="' . $image . '" width="100" /><br>
                    <span>' . htmlspecialchars($image) . '</span>
                </label>';
                }
                ?>
            </div>

            <br>
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

        <?php
        function convertImageToHex($image, $resolution, $invert = false, $library = 'Adafruit GFX')
        {
            list($newWidth, $newHeight) = explode('x', $resolution);
            $imgResized = imagescale($image, $newWidth, $newHeight);
            if ($invert) {
                imagefilter($imgResized, IMG_FILTER_NEGATE);
            }

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

        function generateAdafruitGFX($hexCode, $varName)
        {
            $lines = explode(' ', trim($hexCode));
            $out = "#ifndef IMAGE_DATA_H\n#define IMAGE_DATA_H\n\nconst uint16_t {$varName}[] PROGMEM = {\n";
            foreach ($lines as $line) {
                if (strlen($line) < 7) continue;
                $r = hexdec(substr($line, 1, 2));
                $g = hexdec(substr($line, 3, 2));
                $b = hexdec(substr($line, 5, 2));
                $color = ($r >> 3) << 11 | ($g >> 2) << 5 | ($b >> 3);
                $out .= '  0x' . str_pad(dechex($color), 4, '0', STR_PAD_LEFT) . ',';
            }
            return rtrim($out, ',') . "\n};\n\n#endif";
        }

        function generateU8g2($hexCode, $varName)
        {
            $lines = explode(' ', trim($hexCode));
            $out = "#ifndef IMAGE_DATA_H\n#define IMAGE_DATA_H\n\nconst uint8_t {$varName}[] PROGMEM = {\n";
            foreach ($lines as $line) {
                if (strlen($line) < 7) continue;
                $r = hexdec(substr($line, 1, 2));
                $g = hexdec(substr($line, 3, 2));
                $b = hexdec(substr($line, 5, 2));
                $gray = (int)(($r + $g + $b) / 3);
                $out .= '  0x' . str_pad(dechex($gray), 2, '0', STR_PAD_LEFT) . ',';
            }
            return rtrim($out, ',') . "\n};\n\n#endif";
        }

        function generateLiquidCrystal($hexCode, $varName)
        {
            $lines = explode(' ', trim($hexCode));
            $out = "#ifndef IMAGE_DATA_H\n#define IMAGE_DATA_H\n\nconst char {$varName}[] = {\n";
            foreach ($lines as $line) {
                if (strlen($line) < 7) continue;
                $r = hexdec(substr($line, 1, 2));
                $g = hexdec(substr($line, 3, 2));
                $b = hexdec(substr($line, 5, 2));
                $gray = (int)(($r + $g + $b) / 3);
                $out .= '  0x' . str_pad(dechex($gray), 2, '0', STR_PAD_LEFT) . ',';
            }
            return rtrim($out, ',') . "\n};\n\n#endif";
        }

        function generateHexOutput($hexCode, $varName, $library)
        {
            switch ($library) {
                case 'Adafruit GFX':
                case 'TFT_eSPI':
                    return generateAdafruitGFX($hexCode, $varName);
                case 'U8g2':
                    return generateU8g2($hexCode, $varName);
                case 'LiquidCrystal':
                    return generateLiquidCrystal($hexCode, $varName);
                default:
                    return '';
            }
        }

        if (isset($_POST['prestored_images']) && is_array($_POST['prestored_images'])) {
            $resolution = $_POST['resolution'] ?? '128x128';
            $invert = isset($_POST['invert']) && $_POST['invert'] == '1';
            $library = $_POST['library'] ?? 'Adafruit GFX';
            $zip = new ZipArchive();
            $zipFileName = 'downloads/converted_images.zip';

            if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                die("Failed to create zip archive.");
            }

            $hexResults = [];
            $downloadLinks = [];
            $imageBase64Array = [];
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

            foreach ($_POST['prestored_images'] as $imageName) {
                $filePath = 'image_library/' . $imageName;
                if (!file_exists($filePath)) continue;

                $imageInfo = getimagesize($filePath);
                $isGif = $imageInfo && $imageInfo[2] === IMAGETYPE_GIF;

                $frames = [];
                $convertedFrames = [];
                $combinedHex = '';
                $animatedGifB64 = '';

                if ($isGif && class_exists('Imagick')) {
                    $imagick = new Imagick();
                    $imagick->readImage(realpath($filePath));
                    foreach ($imagick as $frame) {
                        $frame->setImageFormat('png');
                        $frames[] = imagecreatefromstring($frame->getImageBlob());
                    }
                    $imagick->clear();
                    $imagick->destroy();
                } else {
                    $frames[] = imagecreatefromstring(file_get_contents($filePath));
                }

                $varBaseName = generateSafeVariableName($imageName);
                $frameIndex = 0;

                foreach ($frames as $frameImage) {
                    list($resizedFrame, $frameHex) = convertImageToHex($frameImage, $resolution, $invert, $library);
                    $frameVarName = $varBaseName . '_frame' . $frameIndex;
                    $header = generateHexOutput($frameHex, $frameVarName, $library);
                    $filename = $frameVarName . '_' . $library . '_' . $resolution . '.h';
                    file_put_contents($filename, $header);
                    $zip->addFile($filename);

                    ob_start();
                    imagepng($resizedFrame);
                    $imgData = ob_get_clean();
                    $imageBase64Array[] = base64_encode($imgData);
                    $hexResults[] = $header;
                    $downloadLinks[] = $filename;

                    $combinedHex .= $frameHex . "\n";
                    $convertedFrames[] = $resizedFrame;

                    $frameIndex++;
                }

                // Combine frames into animated preview (only if more than 1 frame)
                if (count($convertedFrames) > 1 && class_exists('Imagick')) {
                    $animatedGif = assembleAnimatedGIF($convertedFrames);
                    $animatedGifB64 = base64_encode($animatedGif);
                    $animatedHeader = generateHexOutput($combinedHex, $varBaseName . '_animated', $library);
                    $zip->addFromString($varBaseName . '_animated.h', $animatedHeader);

                    echo '<div class="conversion-result">';
                    echo '<div style="border:1px solid #aaa; margin:20px; padding:10px;">';
                    echo '<h3>' . htmlspecialchars($imageName) . ' (Animated Preview)</h3>';
                    echo '<img src="data:image/gif;base64,' . $animatedGifB64 . '" width="128"><br>';
                    echo '<pre>' . htmlspecialchars($animatedHeader) . '</pre>';
                    echo '</div>';
                    echo '</div>';
                }

                foreach ($convertedFrames as $img) imagedestroy($img);
            }


            $zip->close();
            foreach ($downloadLinks as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }

            echo '<div class="conversion-result">';
            echo '<h2>Conversion Done</h2>';
            echo '<a href="' . $zipFileName . '" download>Download All Converted Files (ZIP)</a><br><br>';

            foreach ($hexResults as $i => $hex) {
                echo '<div style="border:1px solid #ccc; margin:10px; padding:10px;">';
                echo '<h3>' . htmlspecialchars($downloadLinks[$i]) . '</h3>';
                echo '<img src="data:image/png;base64,' . $imageBase64Array[$i] . '" width="128" /><br>';
                echo '<pre>' . htmlspecialchars($hex) . '</pre>';
                echo '</div>';
            }

            echo '</div>';
        }
        ?>
    </div>
</div>