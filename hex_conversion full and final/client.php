<style>
    .converted-image {
        max-width: 100%;
        height: auto;
        border-radius: 5px;
    }

    .hex-container {
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 40px;
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    .hex-code {
        font-family: 'Courier New', monospace;
        background-color: #f7f7f7;
        padding: 10px;
        border-radius: 5px;
        overflow-x: auto;
    }

    .download-links {
        list-style-type: none;
        padding: 0;
        margin: 0;
        text-align: center;
    }

    .download-item {
        margin-bottom: 15px;
    }

    .download-link {
        font-size: 18px;
        color: #fff;
        background-color: #7b46f8;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .download-link:hover {
        background-color: #6d3fd8;
    }

    .download-link:focus {
        outline: none;
    }

    .conversion-result {
        text-align: center;
        margin-top: 40px;
        background-color: #f7f7f7;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .conversion-result h2 {
        color: #6d3fd8;
        margin-bottom: 20px;
    }

    .conversion-result img {
        max-width: 100%;
        height: auto;
        border: 2px solid #ddd;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .conversion-result h3 {
        color: #333;
        margin-top: 30px;
    }

    .conversion-result pre {
        background: #222;
        color: #0f0;
        text-align: left;
        padding: 20px;
        border-radius: 8px;
        overflow-x: auto;
        max-width: 800px;
        max-height: 100px;
        margin: 0 auto 30px;
        font-size: 14px;
    }

    .conversion-result ul {
        list-style: none;
        padding-left: 0;
    }

    .conversion-result li {
        margin: 10px 0;
    }

    .conversion-result a {
        text-decoration: none;
        background-color: #7b46f8;
        color: white;
        padding: 10px 20px;
        border-radius: 6px;
        display: inline-block;
        transition: background-color 0.3s;
    }

    .conversion-result a:hover {
        background-color: #6d3fd8;
    }
</style>
<?php
include 'header.php'; ?>
<div class="container">
    <div class="form-section">

        <h1>Image Converter Using API</h1>

        <form id="hexForm" enctype="multipart/form-data">
            <input type="file" name="images[]" multiple required>
            <input type="hidden" name="api_key" value="123456">
               <label for="resolution">Select Resolution:</label>
            <select name="resolution" id="resolution">
                <option value="128x128">128x128</option>
                <option value="256x256">256x256</option>
                <option value="512x512">512x512</option>
            </select><br><br>

            <label for="invert">Invert Image Colors:</label>
            <input type="checkbox" name="invert" id="invert" value="true"><br><br>

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

        <div id="result" class="conversion-result"></div>

        <!-- Add gif.js library -->
        <script src="https://cdn.jsdelivr.net/npm/gif.js.optimized/dist/gif.worker.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/gif.js.optimized/dist/gif.min.js"></script>

        <script>
            document.getElementById('hexForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const container = document.getElementById('result');
                container.innerHTML = 'Processing...';

                const res = await fetch('http://localhost/hex_conversion/api/api.php', {
                    method: 'POST',
                    body: formData
                });

                const json = await res.json();
                if (json.error) return alert(json.error);

                container.innerHTML = '<h2>Conversion Results</h2>';

                const isSingleFile = json.results.length === 1;
                const allFrames = [];

                json.results.forEach(fileResult => {
                    const fileGroup = document.createElement('div');

                    const title = document.createElement('h3');
                    title.textContent = fileResult.filename;
                    fileGroup.appendChild(title);

                    const frameImages = []; // for gif.js

                    fileResult.frames.forEach(frame => {
                        allFrames.push(frame);

                        if (isSingleFile && fileResult.frames.length === 1) {
                            const dl = document.createElement('a');
                            dl.href = frame.download_url;
                            dl.download = frame.variable_name + '.h';
                            dl.className = 'download-link';
                            dl.textContent = 'Download .h file';
                            fileGroup.appendChild(dl);
                        }

                        const img = document.createElement('img');
                        img.src = frame.converted_image_base64;
                        img.className = 'converted-image';
                        fileGroup.appendChild(img);

                        const pre = document.createElement('pre');
                        pre.textContent = frame.hex_header;
                        fileGroup.appendChild(pre);

                        frameImages.push(frame.converted_image_base64);
                    });

                    // === If multiple frames, generate preview animated GIF ===
                    if (frameImages.length > 1) {
                        const gif = new GIF({
                            workers: 2,
                            quality: 10,
                            workerScript: 'https://cdn.jsdelivr.net/npm/gif.js.optimized/dist/gif.worker.js'
                        });

                        const framePromises = frameImages.map(src => {
                            return new Promise(resolve => {
                                const image = new Image();
                                image.onload = () => resolve(image);
                                image.src = src;
                            });
                        });

                        Promise.all(framePromises).then(images => {
                            images.forEach(img => gif.addFrame(img, {
                                delay: 100
                            }));

                            gif.on('finished', function(blob) {
                                const url = URL.createObjectURL(blob);

                                const gifLabel = document.createElement('p');
                                gifLabel.textContent = 'Animated Preview:';
                                gifLabel.style.fontWeight = 'bold';

                                const animatedImg = document.createElement('img');
                                animatedImg.src = url;
                                animatedImg.className = 'converted-image';

                                fileGroup.insertBefore(gifLabel, fileGroup.children[1]);
                                fileGroup.insertBefore(animatedImg, fileGroup.children[2]);
                            });

                            gif.render();
                        });
                    }

                    container.appendChild(fileGroup);
                });

                if (!isSingleFile || allFrames.length > 1) {
                    if (json.zip_url) {
                        const zipLink = document.createElement('a');
                        zipLink.href = json.zip_url;
                        zipLink.textContent = 'Download All (.zip)';
                        zipLink.className = 'download-link';
                        zipLink.download = '';

                        container.insertBefore(zipLink, container.childNodes[1]); // after <h2>
                    }
                }
            });
        </script>
    </div>
</div>