<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

include 'convert.php';

$allowedKeys = ['123456', 'abcdef'];
$providedKey = $_POST['api_key'] ?? '';
if (!in_array($providedKey, $allowedKeys)) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid API key.']);
    exit;
}

$resolution = $_POST['resolution'] ?? '128x128';
$invert = isset($_POST['invert']) && $_POST['invert'] === 'true';
$library = $_POST['library'] ?? 'Adafruit GFX';

$results = [];

foreach ($_FILES['images']['tmp_name'] as $index => $tmpPath) {
    $filename = $_FILES['images']['name'][$index];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);

    if (strtolower($ext) === 'gif') {
        // Load GIF and extract frames
        $frames = extractGifFrames($tmpPath);
        $frameResults = [];

        foreach ($frames as $frameIndex => $frameImg) {
            $base = pathinfo($filename, PATHINFO_FILENAME);
            $uniqueName = $base . '_frame' . $frameIndex;

            $converted = convertImage($frameImg, $uniqueName . '.png', $resolution, $invert, $library, false);
            $frameResults[] = $converted['frames'][0];
        }

        $results[] = [
            'filename' => $filename,
            'frames' => $frameResults
        ];
    } else {
        $image = imagecreatefromstring(file_get_contents($tmpPath));
        if (!$image) continue;

        $converted = convertImage($image, $filename, $resolution, $invert, $library, true);
        $results[] = [
            'filename' => $filename,
            'frames' => $converted['frames']
        ];
    }
}

$zip = new ZipArchive();
$zipDir = '../downloads';
if (!file_exists($zipDir)) mkdir($zipDir, 0755, true);

$zipFilename = 'converted_' . uniqid() . '.zip';
$zipPath = __DIR__ . '/../downloads/' . $zipFilename;
$zipUrl = 'http://localhost/hex_conversion/downloads/' . $zipFilename;


if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
    foreach ($results as $result) {
        foreach ($result['frames'] as $frame) {
            if (!empty($frame['download_url'])) {
                $filePath = __DIR__ . '/../downloads/' . basename($frame['download_url']);
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, basename($filePath));
                }
            }
        }
    }
    $zip->close();


} else {
    $zipUrl = null;
}

echo json_encode([
    'results' => $results,
    'zip_url' => $zipUrl
]);
