<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #barcode {
            margin: 20px auto;
            display: block;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">Barcode for Today</h2>

        <!-- Tempat untuk menampilkan barcode -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h3>Scan This Barcode</h3>
                <!-- Canvas untuk Barcode -->
                <svg id="barcode"></svg>
            </div>
        </div>

        <div class="row">
            <div class="col-12 text-center">
                <h4>QR Code untuk Pemindaian</h4>
                <!-- Tombol untuk memulai pemindaian QR Code -->
                <button class="btn btn-primary" id="startScan">Start Scan</button>
                <div id="result" class="mt-4"></div>
            </div>
        </div>
    </div>

    <!-- Load JsBarcode Library -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode/dist/JsBarcode.all.min.js"></script>

    <!-- Load jsQR Library -->
    <script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>

    <script>
        // Generate barcode yang berubah setiap hari
        const currentDate = new Date().toISOString().split('T')[0]; // Format YYYY-MM-DD
        const barcodeData = `ID-${currentDate}`;

        // Generate barcode berdasarkan tanggal hari ini
        JsBarcode("#barcode", barcodeData, {
            format: "CODE128",
            displayValue: true
        });

        // Fungsi untuk memulai pemindaian QR Code
        let videoElement = document.createElement('video');
        let resultDiv = document.getElementById('result');
        let videoStream;

        // Akses kamera dan mulai stream video
        document.getElementById('startScan').addEventListener('click', function() {
            navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: "environment"
                    }
                })
                .then(function(stream) {
                    videoElement.srcObject = stream;
                    videoStream = stream;
                    requestAnimationFrame(scanQRCode);
                })
                .catch(function(error) {
                    resultDiv.innerHTML = `<div class="alert alert-danger">Error accessing camera: ${error}</div>`;
                });
        });

        // Fungsi untuk melakukan pemindaian QR Code
        function scanQRCode() {
            if (videoStream.active) {
                let canvas = document.createElement('canvas');
                let context = canvas.getContext('2d');
                canvas.width = videoElement.videoWidth;
                canvas.height = videoElement.videoHeight;
                context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);

                let imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                let qrCode = jsQR(imageData.data, canvas.width, canvas.height);

                if (qrCode) {
                    // Kirim hasil QR Code ke server
                    fetch('<?= site_url('qrscan/scan'); ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                barcode: qrCode.data
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status) {
                                resultDiv.innerHTML =
                                    `<div class="alert alert-success">${data.message}</div>`;
                            } else {
                                resultDiv.innerHTML =
                                    `<div class="alert alert-danger">${data.message}</div>`;
                            }
                        })
                        .catch(error => {
                            resultDiv.innerHTML =
                                `<div class="alert alert-danger">Terjadi kesalahan saat memproses data.</div>`;
                        });

                    // Stop video stream after scanning
                    videoStream.getTracks().forEach(track => track.stop());
                } else {
                    requestAnimationFrame(scanQRCode);
                }
            }
        }
    </script>
</body>

</html>