<?php
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuario - Corekit</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .glow-effect {
            background: radial-gradient(circle 100px at var(--mouse-x, 0) var(--mouse-y, 0), rgba(59, 130, 246, 0.08), transparent 80%);
        }
    </style>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen font-sans antialiased selection:bg-blue-500 selection:text-white">

    <div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>

    <header class="bg-blue-900 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-3 flex flex-col md:flex-row items-center justify-between gap-4">
            
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-layer-group text-2xl text-blue-400"></i>
                <span class="text-xl font-bold tracking-wider">Corekit Portal</span>
            </div>

            <div class="relative w-full md:w-64 transition-all duration-300 ease-in-out focus-within:md:w-80">
                <input id="search-input" type="text" placeholder="Buscar en el sitio... (Presiona Esc para limpiar)" class="w-full bg-blue-800 text-sm text-white placeholder-blue-300 rounded-lg px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-blue-300 text-sm"></i>
            </div>

            <nav class="flex flex-wrap items-center gap-5 text-sm font-medium">
                <a href="#" class="hover:text-blue-300 transition"><i class="fa-solid fa-house mr-1"></i> Inicio</a>
                <a href="#" class="hover:text-blue-300 transition"><i class="fa-solid fa-chart-line mr-1"></i> Servicios</a>
                <a href="#" class="hover:text-blue-300 transition"><i class="fa-solid fa-envelope mr-1"></i> Buzón <span id="buzon-badge" class="bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full ml-1 transition-transform duration-300">2</span></a>
                <a href="#" class="hover:text-blue-300 transition"><i class="fa-solid fa-circle-question mr-1"></i> Ayuda</a>
                <a href="#" class="hover:text-blue-300 transition"><i class="fa-solid fa-address-book mr-1"></i> Contáctanos</a>
                <a href="api/logout.php" class="bg-red-600 hover:bg-red-700 px-3 py-1.5 rounded-lg transition font-semibold flex items-center gap-1">
                    <i class="fa-solid fa-right-from-bracket"></i> Salir
                </a>
            </nav>
        </div>
    </header>

    <main class="flex-grow max-w-7xl w-full mx-auto px-4 py-8">
        <div id="main-panel" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8 glow-effect transition-shadow duration-300">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                        ¡Bienvenido/a de nuevo, <?php echo $_SESSION['user_name']; ?>! 
                    </h1>
                    <p class="text-gray-600">Has iniciado sesión de forma totalmente segura. Desde aquí puedes gestionar tu información.</p>
                </div>
                <div class="bg-gray-100 px-4 py-2 rounded-lg border border-gray-200 text-right">
                    <span class="text-xs text-gray-500 block uppercase font-bold tracking-wider">Hora del Sistema</span>
                    <span id="live-clock" class="text-sm font-mono text-blue-900 font-bold">00:00:00</span>
                </div>
            </div>

            <div id="status-bar" class="mb-6 p-3 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center justify-between text-xs text-emerald-800">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span>Todos los sistemas operativos de Corekit funcionan correctamente.</span>
                </div>
                <button id="refresh-status-btn" class="hover:underline font-bold flex items-center gap-1"><i class="fa-solid fa-rotate mr-0.5"></i> Verificar API</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="dashboard-card p-5 border border-gray-200 rounded-lg shadow-sm transition-all duration-300 cursor-pointer bg-white" data-title="Buzón">
                    <div class="text-blue-600 text-2xl mb-2 flex justify-between items-center">
                        <i class="fa-solid fa-inbox"></i>
                        <span class="text-[10px] text-gray-400 font-normal">Doble click para fijar</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-1">Tu Buzón</h3>
                    <p class="text-xs text-gray-500">Revisa las alertas internas y mensajes enviados por el administrador.</p>
                </div>
                <div class="dashboard-card p-5 border border-gray-200 rounded-lg shadow-sm transition-all duration-300 cursor-pointer bg-white" data-title="Centro de Ayuda">
                    <div class="text-emerald-600 text-2xl mb-2 flex justify-between items-center">
                        <i class="fa-solid fa-life-ring"></i>
                        <span class="text-[10px] text-gray-400 font-normal">Doble click para fijar</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-1">Centro de Ayuda</h3>
                    <p class="text-xs text-gray-500">Preguntas frecuentes, guías de usuario y documentación del portal.</p>
                </div>
                <div class="dashboard-card p-5 border border-gray-200 rounded-lg shadow-sm transition-all duration-300 cursor-pointer bg-white" data-title="Seguridad">
                    <div class="text-amber-600 text-2xl mb-2 flex justify-between items-center">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span class="text-[10px] text-gray-400 font-normal">Doble click para fijar</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-1">Seguridad</h3>
                    <p class="text-xs text-gray-500">Configuración de credenciales y enlace para recuperación de contraseña.</p>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-gray-400 text-xs py-6 border-t border-gray-700">
        <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>&copy; 2026 Corekit Inc. Todos los derechos reservados bajo principios de protección de datos.</div>
            <div class="flex gap-4">
                <span class="font-bold text-gray-300">Mapa del sitio:</span>
                <a href="#" class="hover:underline">Inicio</a> • 
                <a href="#" class="hover:underline">Registro/Login</a> • 
                <a href="#" class="hover:underline">Buzón de Mensajes</a> • 
                <a href="#" class="hover:underline">Ayuda Legal</a> • 
                <a href="#" class="hover:underline">Contacto</a>
            </div>
        </div>
    </footer>

    <div class="fixed bottom-5 right-5 z-40">
        <button id="chat-btn" class="bg-blue-600 hover:bg-blue-700 text-white p-3.5 rounded-full shadow-lg transition-all duration-300 flex items-center justify-center">
            <i class="fa-solid fa-comments text-xl"></i>
        </button>
        
        <div id="chat-window" class="opacity-0 scale-95 pointer-events-none transform transition-all duration-300 absolute bottom-16 right-0 w-72 bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden">
            <div class="bg-blue-900 text-white p-3 font-bold text-sm flex justify-between items-center">
                <span><i class="fa-solid fa-robot mr-1"></i> Soporte Corekit</span>
                <button id="close-chat"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <div id="chat-messages" class="p-4 h-48 overflow-y-auto text-xs text-gray-600 flex flex-col gap-2">
                <p class="bg-gray-100 p-2 rounded-lg self-start mr-auto max-w-[85%]">¡Hola! ¿En qué podemos ayudarte con el mapa del sitio o tu buzón?</p>
            </div>
            
            <form id="chat-form" class="p-2 border-t border-gray-100 flex gap-1">
                <input id="chat-input" type="text" placeholder="Escribe aquí..." class="w-full text-xs border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                <button type="submit" class="bg-blue-600 text-white px-2 rounded text-xs"><i class="fa-solid fa-paper-plane"></i></button>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        
        const chatBtn = document.getElementById('chat-btn');
        const chatWindow = document.getElementById('chat-window');
        const closeChat = document.getElementById('close-chat');
        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input');
        const chatMessages = document.getElementById('chat-messages');
        const cards = document.querySelectorAll('.dashboard-card');
        const mainPanel = document.getElementById('main-panel');
        const liveClock = document.getElementById('live-clock');
        const buzonBadge = document.getElementById('buzon-badge');
        const refreshStatusBtn = document.getElementById('refresh-status-btn');
        const toastContainer = document.getElementById('toast-container');


        function actualizarReloj() {
            const ahora = new Date();
            const horas = String(ahora.getHours()).padStart(2, '0');
            const minutos = String(ahora.getMinutes()).padStart(2, '0');
            const segundos = String(ahora.getSeconds()).padStart(2, '0');
            liveClock.innerText = `${horas}:${minutos}:${segundos}`;
        }
        setInterval(actualizarReloj, 1000);
        actualizarReloj();


        function mostrarToastNotificacion(titulo, mensaje, tipo = 'info') {
            const toast = document.createElement('div');
            toast.className = "transform translate-x-full opacity-0 transition-all duration-500 ease-out p-3 rounded-lg shadow-xl text-xs flex flex-col gap-1 w-64 border pointer-events-auto bg-white ";
            
            if (tipo === 'success') toast.className += "border-emerald-200 bg-emerald-50 text-emerald-900";
            else if (tipo === 'warn') toast.className += "border-amber-200 bg-amber-50 text-amber-900";
            else toast.className += "border-blue-200 bg-blue-50 text-blue-900";

            toast.innerHTML = `<strong>${titulo}</strong><span>${mensaje}</span>`;
            toastContainer.appendChild(toast);

            toast.offsetHeight;

            toast.classList.remove('translate-x-full', 'opacity-0');

            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 500);
            }, 4000);
        }

        function agregarMensajeAlDOM(texto, esUsuario) {
            const wrapper = document.createElement('p');
            wrapper.innerText = texto;
            wrapper.className = esUsuario 
                ? "bg-blue-500 text-white p-2 rounded-lg text-left self-end ml-auto max-w-[85%] shadow-sm transform scale-95 transition-transform duration-200"
                : "bg-gray-100 text-gray-800 p-2 rounded-lg self-start mr-auto max-w-[85%] shadow-sm transform scale-95 transition-transform duration-200";
            
            chatMessages.appendChild(wrapper);
            chatMessages.scrollTop = chatMessages.scrollHeight;
            setTimeout(() => wrapper.classList.remove('scale-95'), 50);
        }

        async function simularVerificacionAPI() {
            refreshStatusBtn.disabled = true;
            refreshStatusBtn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Conectando...';
            
            try {
                await new Promise(resolve => setTimeout(resolve, 1500));
                mostrarToastNotificacion("API Corekit", "Conexión exitosa. Base de datos sincronizada de forma segura.", "success");
            } catch (err) {
                mostrarToastNotificacion("Error de red", "No se pudo conectar al clúster central.", "warn");
            } finally {
                refreshStatusBtn.disabled = false;
                refreshStatusBtn.innerHTML = '<i class="fa-solid fa-rotate mr-0.5"></i> Verificar API';
            }
        }
        refreshStatusBtn.addEventListener('click', simularVerificacionAPI);

        async function simularMensajeBuzonEntrante() {
            await new Promise(resolve => setTimeout(resolve, 3000));
            buzonBadge.innerText = "3";
            buzonBadge.classList.add('scale-125', 'bg-red-600');
            mostrarToastNotificacion("Nuevo Mensaje", "Has recibido una nueva notificación administrativa en tu buzón.", "info");
            setTimeout(() => buzonBadge.classList.remove('scale-125'), 300);
        }
        simularMensajeBuzonEntrante();

        async function consultarBotAsincrono(mensajeDelUsuario) {
            await new Promise(resolve => setTimeout(resolve, 1200));
            const consulta = mensajeDelUsuario.toLowerCase();
            if (consulta.includes('hola')) return "¡Hola de nuevo! ¿Te sirvieron las nuevas herramientas?";
            if (consulta.includes('ayuda')) return "El Centro de Ayuda técnico se encuentra disponible las 24 horas.";
            return "Entendido. He procesado tu consulta mediante la cola asíncrona del portal.";
        }

        mainPanel.addEventListener('mousemove', (e) => {
            const rect = mainPanel.getBoundingClientRect();
            const x = e.clientX - rect.left; 
            const y = e.clientY - rect.top;
            mainPanel.style.setProperty('--mouse-x', `${x}px`);
            mainPanel.style.setProperty('--mouse-y', `${y}px`);
        });


        cards.forEach(card => {

            card.addEventListener('mouseenter', () => {
                card.classList.remove('shadow-sm', 'border-gray-200');
                card.classList.add('scale-[1.02]', 'shadow-md', 'border-blue-400');
            });
            card.addEventListener('mouseleave', () => {
                card.classList.remove('scale-[1.02]', 'shadow-md', 'border-blue-400');
                card.classList.add('shadow-sm', 'border-gray-200');
            });

            card.addEventListener('dblclick', () => {
                const titulo = card.getAttribute('data-title');
                const estaFijado = card.classList.toggle('ring-2');
                card.classList.toggle('ring-blue-500');
                card.classList.toggle('bg-blue-50/20');
                
                if (estaFijado) {
                    mostrarToastNotificacion("Tarjeta Fijada", `Anclaste la sección ${titulo} al inicio de tu panel.`, "success");
                } else {
                    mostrarToastNotificacion("Tarjeta Desanclada", `Removiste el anclaje de ${titulo}.`, "info");
                }
            });
        });

        function toggleChat() {
            chatWindow.classList.toggle('opacity-0');
            chatWindow.classList.toggle('scale-95');
            chatWindow.classList.toggle('pointer-events-none');
            chatWindow.classList.toggle('opacity-100');
            chatWindow.classList.toggle('scale-100');
            chatWindow.classList.toggle('pointer-events-auto');
        }
        chatBtn.addEventListener('click', toggleChat);
        closeChat.addEventListener('click', toggleChat);


        chatBtn.classList.add('animate-bounce');
        setTimeout(() => chatBtn.classList.remove('animate-bounce'), 3000);

        document.getElementById('search-input').addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                e.target.value = '';
                mostrarToastNotificacion("Búsqueda limpia", "Se cancelaron los filtros de búsqueda.", "info");
            }
        });

        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const mensaje = chatInput.value.trim();
            if (!mensaje) return;

            agregarMensajeAlDOM(mensaje, true);
            chatInput.value = '';

            const indicadorEscribiendo = document.createElement('p');
            indicadorEscribiendo.id = 'typing-indicator';
            indicadorEscribiendo.innerText = 'Soporte Corekit está respondiendo...';
            indicadorEscribiendo.className = 'text-gray-400 italic text-[10px] animate-pulse self-start ml-1';
            chatMessages.appendChild(indicadorEscribiendo);
            chatMessages.scrollTop = chatMessages.scrollHeight;

            try {
                const respuestaDelServidor = await consultarBotAsincrono(mensaje);
                document.getElementById('typing-indicator')?.remove();
                agregarMensajeAlDOM(respuestaDelServidor, false);

                if (chatWindow.classList.contains('opacity-0')) {
                    chatBtn.classList.add('animate-ping');
                    setTimeout(() => chatBtn.classList.remove('animate-ping'), 1200);
                }
            } catch (error) {
                document.getElementById('typing-indicator')?.remove();
            }
        });
    });
    </script>
</body>
</html>