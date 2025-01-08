<?= $this->extend('main_layout'); ?>
<?= $this->section('content'); ?>

<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
    <div class="col">
        <div class="card radius-10 border-start border-0 border-4 border-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Total Orders</p>
                        <h4 class="my-1 text-info">4805</h4>
                        <p class="mb-0 font-13">+2.5% from last week</p>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-blues text-white ms-auto"><i class='bx bxs-cart'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0 border-4 border-danger">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Total Revenue</p>
                        <h4 class="my-1 text-danger">$84,245</h4>
                        <p class="mb-0 font-13">+5.4% from last week</p>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-burning text-white ms-auto"><i class='bx bxs-wallet'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0 border-4 border-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Bounce Rate</p>
                        <h4 class="my-1 text-success">34.6%</h4>
                        <p class="mb-0 font-13">-4.5% from last week</p>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto"><i class='bx bxs-bar-chart-alt-2'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0 border-4 border-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Total Customers</p>
                        <h4 class="my-1 text-warning">8.4K</h4>
                        <p class="mb-0 font-13">+8.4% from last week</p>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto"><i class='bx bxs-group'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><!--end row-->
<div class="container mt-5">
    <h2 class="text-center">Absensi with QRcode</h2>
    <div class="row justify-content-center">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="<?= base_url('assets/uploads/') . session('image'); ?>" alt="Admin" class="rounded-circle p-1 bg-primary" width="110">
                        <div class="mt-3">
                            <h4><?= session('name'); ?></h4>
                            <p class="text-secondary mb-1"><?= session('type'); ?></p>
                        </div>
                    </div>
                    <div class="text-center">
                        <hr class="my-4">
                        <h4>Your QR Code:</h4>
                        <?php if (isset($qrcode)): ?>
                            <img src="<?= $qrcode ?>" alt="QR Code" class="img-fluid">
                        <?php else: ?>
                            <p>QR Code could not be generated.</p>
                        <?php endif; ?>
                        <h5>
                            <b>
                                <?php if (isset($cekabsen)) {
                                    echo $cekabsen;
                                }; ?>
                            </b>
                        </h5>
                        <button type="button" class="btn btn-success" onclick="generate()">Generate</button>
                    </div>
                </div>
            </div>

        </div>
        <?php if (session('type') == 'Admin') { ?>
            <div class="col-sm-6">
                <h2>Scan QR Code</h2>
                <div id="result"></div>

                <!-- Tombol untuk memulai pemindaian (buka kamera) -->
                <button id="startButton" onclick="startScan()" class="btn btn-success">Start Camera</button>

                <!-- Tombol untuk menghentikan pemindaian (nonaktifkan kamera) -->
                <button id="stopButton" onclick="stopScan()" class="btn btn-warning">Stop Camera</button>

                <!-- Elemen untuk menampilkan hasil pemindaian -->
                <div id="reader"></div>

                <!-- Elemen untuk menampilkan hasil pemindaian -->

            </div>
        <?php }; ?>

    </div>


</div>
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode/minified/html5-qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.8/dist/sweetalert2.all.min.js"></script>
<script>
    // Variabel untuk menyimpan objek QR Code scanner
    let html5QrCode;
    let isProcessing = false; // Untuk memastikan pemindaian tidak dilakukan berulang dalam interval 5 detik

    // Fungsi untuk memulai pemindaian QR Code
    function startScan() {
        // Menonaktifkan tombol start saat kamera sudah aktif
        document.getElementById('startButton').disabled = true;
        document.getElementById('stopButton').disabled = false;

        // Inisialisasi scanner QR Code
        html5QrCode = new Html5Qrcode("reader");

        // Konfigurasi pemindaian
        const config = {
            fps: 10, // Frame per detik
            qrbox: 250 // Ukuran kotak pemindaian QR
        };

        // Mulai pemindaian menggunakan kamera belakang
        html5QrCode.start({
                facingMode: "environment"
            },
            config,
            onScanSuccess,
            onScanError
        ).catch(err => {
            console.error("Error starting QR Code scanner:", err);
        });
    }

    // Fungsi untuk menghentikan pemindaian QR Code
    function stopScan() {
        // Menghentikan scanner dan membersihkan elemen
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                console.log("QR Code scanner stopped.");
                document.getElementById('startButton').disabled = false;
                document.getElementById('stopButton').disabled = true;
            }).catch(err => {
                console.error("Error stopping QR Code scanner:", err);
            });
        }
    }

    // Fungsi yang dipanggil saat QR Code berhasil dipindai
    function onScanSuccess(decodedText, decodedResult) {
        if (isProcessing) return; // Abaikan jika sedang dalam jeda 5 detik

        isProcessing = true; // Blokir pemrosesan ulang selama 5 detik
        console.log(`QR Code detected: ${decodedText}`, decodedResult);

        // Kirim hasil scan ke server
        fetch('<?= base_url('qrcode/processScan') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    qr_data: decodedText
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        title: "Good job!",
                        text: `Attendance logged for ${data.user.name}`,
                        icon: "success"
                    });

                    // Hentikan pemindaian setelah sukses
                    stopScan();
                    document.getElementById('result').innerHTML = `<p>Terimakasih sudah absen hari ini</p>`;
                } else {
                    Swal.fire({
                        title: "Opss!",
                        text: data.message,
                        icon: "warning"
                    });
                }
            })
            .catch(error => console.error('Error:', error))
            .finally(() => {
                // Atur ulang pemrosesan setelah 5 detik
                setTimeout(() => {
                    isProcessing = false;
                }, 5000);
            });
    }

    // Fungsi untuk menangani error saat pemindaian QR Code
    function onScanError(errorMessage) {
        console.error('QR Code scan error:', errorMessage);
    }

    function generate() {
        location.reload();
    }
</script>
<?= $this->endSection(); ?>