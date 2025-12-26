{{-- resources/views/filament/components/turno-status.blade.php --}}
<div 
    x-data="turnoStatus()" 
    x-init="init()"
    class="bg-white rounded-lg shadow p-4 border border-gray-200"
>
    <!-- Encabezado -->
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
            <span class="w-3 h-3 rounded-full mr-2" :class="{
                'bg-green-500': estado === 'activo',
                'bg-yellow-500': estado === 'pendiente', 
                'bg-gray-500': estado === 'sin_turno'
            }"></span>
            Estado del Turno
        </h3>
        <button 
            @click="recargarEstado()" 
            :disabled="cargando"
            class="text-gray-400 hover:text-gray-600 transition-colors"
            title="Recargar estado"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
        </button>
    </div>

    <!-- Estado: Cargando -->
    <div x-show="cargando" class="text-center py-6">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
        <p class="text-sm text-gray-500 mt-2">Cargando estado del turno...</p>
    </div>

    <!-- Estado: Sin Turno -->
    <div x-show="!cargando && estado === 'sin_turno'" class="text-center py-6">
        <div class="text-gray-400 mb-3">
            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <p class="text-gray-500 font-medium">No tienes turnos asignados para hoy</p>
        <p class="text-sm text-gray-400 mt-1">Consulta con tu supervisor</p>
    </div>

    <!-- Estado: Pendiente -->
    <div x-show="!cargando && estado === 'pendiente'" class="space-y-3">
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
            <div class="flex items-center text-yellow-800 mb-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-semibold">Turno pendiente de inicio</span>
            </div>
            <div class="text-sm text-yellow-700 space-y-1">
                <p><strong>Puesto:</strong> <span x-text="turno.puesto"></span></p>
                <p><strong>Código:</strong> <span x-text="turno.codigo_puesto"></span></p>
                <p><strong>Inicia:</strong> <span x-text="turno.hora_inicio"></span></p>
                <p><strong>Finaliza:</strong> <span x-text="turno.hora_fin"></span></p>
            </div>
        </div>
        <p class="text-xs text-gray-500 text-center">
            El turno se activará automáticamente a la hora de inicio
        </p>
    </div>

    <!-- Estado: Activo -->
    <div x-show="!cargando && estado === 'activo'" class="space-y-4">
        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
            <div class="flex items-center text-green-800 mb-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-semibold">Turno activo</span>
            </div>
            <div class="text-sm text-green-700 space-y-1">
                <p><strong>Puesto:</strong> <span x-text="turno.puesto"></span></p>
                <p><strong>Código:</strong> <span x-text="turno.codigo_puesto"></span></p>
                <p><strong>Horario:</strong> <span x-text="turno.hora_inicio"></span> - <span x-text="turno.hora_fin"></span></p>
                <p><strong>Ingreso registrado:</strong> 
                    <span x-text="turno.verificaciones.ingreso || 'No registrado'" 
                          :class="{ 'text-green-600': turno.verificaciones.ingreso, 'text-gray-500': !turno.verificaciones.ingreso }">
                    </span>
                </p>
            </div>
        </div>
        
        <!-- Botón de acción -->
        <button 
            @click="abrirScanner()"
            :disabled="escaneando"
            class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white py-3 rounded-lg flex items-center justify-center transition-colors font-medium"
        >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
            </svg>
            <span x-text="escaneando ? 'Escaneando...' : 'Escanear QR del Puesto'"></span>
        </button>
    </div>

    <!-- Modal del Scanner -->
    <div x-show="mostrarScanner" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-hidden">
            <!-- Header del Modal -->
            <div class="flex items-center justify-between p-4 border-b">
                <h3 class="text-lg font-semibold text-gray-800">Escanear Código QR</h3>
                <button 
                    @click="cerrarScanner()" 
                    class="text-gray-400 hover:text-gray-600 transition-colors"
                    :disabled="escaneando"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Contenido del Scanner -->
            <div class="p-4">
                <div id="scanner-container"></div>
                <p class="text-sm text-gray-500 text-center mt-3">
                    📷 Enfoca el código QR del puesto de seguridad para registrar tu verificación
                </p>
            </div>
            
            <!-- Footer del Modal -->
            <div class="border-t p-3 bg-gray-50">
                <button 
                    @click="cerrarScanner()" 
                    class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2 rounded transition-colors"
                >
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function turnoStatus() {
    return {
        cargando: true,
        escaneando: false,
        estado: 'sin_turno', // sin_turno, pendiente, activo
        turno: {
            puesto: '',
            codigo_puesto: '',
            hora_inicio: '',
            hora_fin: '',
            verificaciones: {
                ingreso: null,
                salida: null
            }
        },
        mostrarScanner: false,
        scanner: null,
        
        async init() {
            await this.cargarTurnoActual();
            
            // Recargar cada 30 segundos
            setInterval(() => {
                this.cargarTurnoActual();
            }, 30000);
        },
        
        async cargarTurnoActual() {
            try {
                const response = await fetch('/api/turno-actual', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                
                const data = await response.json();
                
                if (data.success && data.turno_actual) {
                    this.turno = data.turno_actual;
                    this.estado = data.estado;
                } else {
                    this.estado = 'sin_turno';
                    this.turno = {
                        puesto: '',
                        codigo_puesto: '',
                        hora_inicio: '',
                        hora_fin: '',
                        verificaciones: { ingreso: null, salida: null }
                    };
                }
                
            } catch (error) {
                console.error('Error cargando turno:', error);
                this.estado = 'sin_turno';
            } finally {
                this.cargando = false;
            }
        },
        
        async recargarEstado() {
            this.cargando = true;
            await this.cargarTurnoActual();
        },
        
        async abrirScanner() {
            this.mostrarScanner = true;
            this.escaneando = true;
            
            // Esperar a que el modal se renderice
            await this.$nextTick();
            
            // Inicializar el scanner
            await this.inicializarScanner();
        },
        
        async inicializarScanner() {
            try {
                // Cargar la librería si no está disponible
                if (typeof Html5Qrcode === 'undefined') {
                    await this.cargarLibreriaQR();
                }
                
                this.scanner = new Html5Qrcode("scanner-container");
                
                const config = {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_QR]
                };
                
                await this.scanner.start(
                    { facingMode: "environment" },
                    config,
                    this.onScanSuccess.bind(this),
                    this.onScanFailure.bind(this)
                );
                
                this.escaneando = false;
                
            } catch (error) {
                console.error('Error iniciando scanner:', error);
                alert('Error al iniciar la cámara. Verifica los permisos.');
                this.cerrarScanner();
            }
        },
        
        async onScanSuccess(decodedText) {
            try {
                this.escaneando = true;
                
                // Detener el scanner
                if (this.scanner) {
                    await this.scanner.stop();
                }
                
                // Verificar el código QR
                const response = await fetch('/api/verificar-qr', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ codigo: decodedText })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Mostrar mensaje de éxito
                    alert(result.mensaje);
                    
                    // Cerrar modal y recargar estado
                    this.cerrarScanner();
                    this.recargarEstado();
                    
                    // Emitir evento para otros componentes
                    window.dispatchEvent(new CustomEvent('qr-verificado', {
                        detail: result
                    }));
                    
                } else {
                    alert(result.mensaje || 'Error en la verificación');
                    // Reiniciar scanner
                    await this.inicializarScanner();
                }
                
            } catch (error) {
                console.error('Error en escaneo:', error);
                alert('Error al procesar el código QR');
                this.cerrarScanner();
            }
        },
        
        onScanFailure(error) {
            // Errores silenciosos durante el escaneo
        },
        
        async cargarLibreriaQR() {
            return new Promise((resolve, reject) => {
                if (typeof Html5Qrcode !== 'undefined') {
                    resolve();
                    return;
                }
                
                const script = document.createElement('script');
                script.src = 'https://unpkg.com/html5-qrcode@2.3.9/minified/html5-qrcode.min.js';
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            });
        },
        
        async cerrarScanner() {
            this.escaneando = false;
            this.mostrarScanner = false;
            
            // Detener el scanner si está activo
            if (this.scanner && this.scanner.isScanning) {
                try {
                    await this.scanner.stop();
                } catch (error) {
                    console.warn('Error deteniendo scanner:', error);
                }
            }
        }
    }
}
</script>