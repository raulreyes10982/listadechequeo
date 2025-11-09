<!-- 📸 Escáner QR -->
<div 
    x-data="qrScanner()"
    x-init="iniciarScanner()"
    class="text-center"
>
    <div id="reader" style="width:100%; height:300px; border-radius:8px; overflow:hidden;"></div>
    <p class="text-gray-500 text-sm mt-3">
        📷 Apunta la cámara al código QR del puesto de seguridad
    </p>
</div>

<!-- ✅ Librerías necesarias -->
<script src="https://unpkg.com/html5-qrcode" defer></script>
<script src="https://unpkg.com/alpinejs" defer></script>

<script>
function qrScanner() {
    return {
        iniciarScanner() {
            // Esperar que la librería esté lista
            if (typeof Html5Qrcode === "undefined") {
                console.error("La librería html5-qrcode no se cargó correctamente.");
                return;
            }

            const scanner = new Html5Qrcode("reader");

            scanner.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: 250 },
                decodedText => {
                    // ✅ Código QR detectado
                    alert('Código detectado: ' + decodedText);

                    // Detener escáner
                    scanner.stop().then(() => {
                        console.log("Escaneo detenido correctamente");
                    });

                    // Aquí podrías llamar tu endpoint Laravel
                    // Ejemplo con fetch:
                    fetch("/api/verificar-qr", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Authorization": `Bearer ${localStorage.getItem('token')}`
                        },
                        body: JSON.stringify({ codigo: decodedText })
                    })
                    .then(r => r.json())
                    .then(data => alert(data.mensaje))
                    .catch(err => alert("Error: " + err));
                },
                error => {
                    console.warn(error);
                }
            );
        }
    }
}
</script>
