<script src="{{ asset('vendor/html5-qrcode.min.js') }}" data-qr-lib></script>
<script>
(function () {
    const QR_LIB_URL = @js(asset('vendor/html5-qrcode.min.js'));

    const qrScannerFactory = (config) => ({
        verifyUrl: config.verifyUrl,
        verificacionId: config.verificacionId,
        readerId: config.readerId,
        scanner: null,
        procesando: false,
        mensajeError: null,
        mensajeCamara: 'Cargando cámara...',

        init() {
            this.$nextTick(() => {
                this.arrancar().catch((err) => {
                    console.error('QR scanner:', err);
                    this.mensajeError = 'No se pudo iniciar el escáner. Recarga la página e intenta de nuevo.';
                });
            });
        },

        async arrancar() {
            await this.esperarLibreria();
            await this.iniciarScanner();
        },

        async esperarLibreria() {
            if (typeof Html5Qrcode !== 'undefined') {
                return;
            }

            await new Promise((resolve, reject) => {
                const existente = document.querySelector('script[data-qr-lib]');

                if (existente) {
                    if (typeof Html5Qrcode !== 'undefined') {
                        resolve();
                        return;
                    }
                    existente.addEventListener('load', () => resolve(), { once: true });
                    existente.addEventListener('error', () => reject(new Error('No se cargó la librería QR')), { once: true });
                    return;
                }

                const script = document.createElement('script');
                script.src = QR_LIB_URL;
                script.dataset.qrLib = '1';
                script.onload = () => resolve();
                script.onerror = () => reject(new Error('No se cargó la librería QR'));
                document.head.appendChild(script);
            });
        },

        esDispositivoMovil() {
            const ua = navigator.userAgent || '';
            const tactil = navigator.maxTouchPoints > 1;
            const pantallaPequena = window.matchMedia('(max-width: 768px)').matches;

            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(ua)
                || (tactil && pantallaPequena);
        },

        async resolverCamara() {
            if (this.esDispositivoMovil()) {
                return { facingMode: 'environment' };
            }

            const cameras = await Html5Qrcode.getCameras();

            if (!cameras?.length) {
                return { facingMode: 'user' };
            }

            const webcam = cameras.find((c) =>
                /front|user|face|integrated|webcam|hd|usb/i.test(c.label || '')
            ) || cameras[0];

            return webcam.id;
        },

        async iniciarScanner() {
            const contenedor = document.getElementById(this.readerId);

            if (!contenedor) {
                this.mensajeError = 'No se encontró el contenedor del escáner.';
                return;
            }

            if (this.scanner?.isScanning) {
                await this.scanner.stop().catch(() => {});
            }

            this.scanner = new Html5Qrcode(this.readerId);

            const scanConfig = {
                fps: 10,
                qrbox: { width: 250, height: 250 },
            };

            const camara = await this.resolverCamara();

            try {
                await this.scanner.start(
                    camara,
                    scanConfig,
                    (texto) => this.onCodigoLeido(texto),
                    () => {}
                );
                this.mensajeCamara = this.esDispositivoMovil()
                    ? '📱 Enfoca el QR con la cámara trasera del celular'
                    : '💻 Apunta el QR a la cámara web (pruebas en PC)';
            } catch (err) {
                console.warn('Reintento con cámara alternativa:', err);
                await this.iniciarConCamaraAlternativa(scanConfig);
            }
        },

        async iniciarConCamaraAlternativa(scanConfig) {
            const cameras = await Html5Qrcode.getCameras();

            if (!cameras?.length) {
                throw new Error('No hay cámaras disponibles');
            }

            await this.scanner.start(
                cameras[0].id,
                scanConfig,
                (texto) => this.onCodigoLeido(texto),
                () => {}
            );

            this.mensajeCamara = '📷 Usando: ' + (cameras[0].label || 'cámara disponible');
        },

        async onCodigoLeido(texto) {
            if (this.procesando) {
                return;
            }

            this.procesando = true;

            if (this.scanner?.isScanning) {
                await this.scanner.stop();
            }

            try {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const body = { codigo_qr: texto };

                if (this.verificacionId) {
                    body.verificacion_id = this.verificacionId;
                }

                const response = await fetch(this.verifyUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(body),
                });

                const data = await response.json();

                if (data.success) {
                    alert(data.mensaje);
                    window.location.reload();
                    return;
                }

                alert(data.mensaje || 'Error al verificar el QR');
                this.procesando = false;
                await this.iniciarScanner();
            } catch (err) {
                console.error(err);
                alert('Error de conexión al verificar el QR');
                this.procesando = false;
                await this.iniciarScanner();
            }
        },

        destroy() {
            if (this.scanner?.isScanning) {
                this.scanner.stop().catch(() => {});
            }
        },
    });

    const registrar = () => {
        if (!window.Alpine) {
            return;
        }
        Alpine.data('qrScanner', qrScannerFactory);
    };

    document.addEventListener('alpine:init', registrar);

    if (window.Alpine) {
        registrar();
    }
})();
</script>
