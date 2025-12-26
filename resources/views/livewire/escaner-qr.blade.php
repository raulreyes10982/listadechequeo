<script>
document.addEventListener('DOMContentLoaded', () => {
    function iniciarScanner() {
        const readerElement = document.getElementById("reader");

        if (!readerElement) {
            console.error("⛔ No se encontró el elemento #reader.");
            return;
        }

        const html5QrCode = new Html5Qrcode("reader");

        Html5Qrcode.getCameras()
            .then(cameras => {
                if (cameras && cameras.length) {
                    const cameraId = cameras[0].id; // usa la primera cámara disponible
                    console.log("📸 Iniciando cámara:", cameras[0].label);

                    html5QrCode.start(
                        cameraId,
                        {
                            fps: 10,
                            qrbox: { width: 250, height: 250 },
                        },
                        qrCodeMessage => {
                            console.log("✅ Código detectado:", qrCodeMessage);
                            alert("Código QR detectado: " + qrCodeMessage);
                            html5QrCode.stop();
                        },
                        errorMessage => {
                            // errores de escaneo (no críticos)
                        }
                    ).catch(err => {
                        console.error("⚠️ Error al iniciar el escáner:", err);
                        alert("Error al iniciar la cámara: " + err);
                    });
                } else {
                    alert("❌ No se detectaron cámaras disponibles.");
                }
            })
            .catch(err => {
                console.error("🚫 No se pudo acceder a la cámara:", err);
                alert("No se pudo acceder a la cámara: " + err);
            });
    }

    // Cargar la librería si no existe
    if (typeof Html5Qrcode === 'undefined') {
        const script = document.createElement('script');
        script.src = "https://unpkg.com/html5-qrcode/html5-qrcode.min.js";
        script.onload = () => {
            console.log("✅ Librería Html5Qrcode cargada.");
            iniciarScanner();
        };
        script.onerror = () => console.error("⛔ No se pudo cargar la librería Html5Qrcode.");
        document.head.appendChild(script);
    } else {
        iniciarScanner();
    }
});
</script>