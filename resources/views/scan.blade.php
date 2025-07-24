<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Scan Chromebook</title>
</head>
<body>
    <h1>Scan Chromebook</h1>

    <!-- Tombol Login -->
    <a href="{{ route('login') }}">
        <button>Login Admin</button>
    </a>

    <!-- Tempat QR Code Scanner -->
    <div id="reader" style="width:300px;"></div>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        function onScanSuccess(decodedText, decodedResult) {
            // Kirim hasil scan ke backend atau redirect
            console.log("Scan berhasil: " + decodedText);
            window.location.href = "/peminjaman?q=" + encodeURIComponent(decodedText);
        }

        const html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", { fps: 10, qrbox: 250 });
        html5QrcodeScanner.render(onScanSuccess);
    </script>
</body>
</html>
