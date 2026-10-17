@extends('layouts.medico')

@section('title', 'SEPI - Psicólogo Deportivo Virtual')

@section('content')
<div class="container-fluid">
    <div class="card card-info mb-3">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-robot"></i> 🤖 SEPI - Psicólogo Deportivo Virtual</h3>
        </div>
        <div class="card-body">
            <p>
                Hola, soy **SEPI**, el **Sistema de Evaluación Psicológica Inteligente**. 
                Estoy aquí para ayudarte a mejorar tu enfoque, manejo de presión y resiliencia mental, especialmente en el ámbito de la gimnasia artistica. 
                como puedo ayudate el día de hoy?.
            </p>
        </div>
    </div>

    <div class="card card-outline card-primary" style="height: 70vh;">
        <div class="card-header">
            <h4 class="card-title"><i class="fas fa-comments"></i> Conversación</h4>
        </div>

        <div class="card-body chat-messages" id="chat-messages" style="overflow-y: auto; flex-grow: 1;">
            <div class="message bot">
                ¡Hola! Soy **SEPI**, el Sistema de Evaluación Psicológica Inteligente, y estoy listo para ayudarte. Pregúntame sobre técnicas de visualización o manejo de presión.
            </div>
        </div>
        
        <div class="card-footer chat-input">
            <div class="input-group">
                <input type="text" id="user-input" class="form-control" placeholder="Escribe tu pregunta o duda..." onkeypress="if(event.key === 'Enter') sendMessage()">
                <div class="input-group-append">
                    <button class="btn btn-primary" onclick="sendMessage()" id="send-button">
                        <i class="fas fa-paper-plane"></i> Enviar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos adaptados: Mantenemos la lógica de visualización del chat, pero adaptada a las clases de Bootstrap/AdminLTE */
    .chat-messages {
        /* Para que ocupe el espacio y permita el scroll */
        display: flex;
        flex-direction: column;
        padding: 20px;
    }
    .message { margin-bottom: 15px; clear: both; max-width: 80%; padding: 10px; border-radius: 15px; }
    .message.user { 
        background-color: #007bff; /* Color primario de Bootstrap/AdminLTE */
        color: white; 
        float: right; 
        border-radius: 15px 15px 0 15px; 
        margin-left: auto; /* Mueve el mensaje a la derecha */
    }
    .message.bot { 
        background-color: #e9ecef; /* Color gris claro de Bootstrap/AdminLTE */
        color: #333; 
        float: left; 
        border-radius: 15px 15px 15px 0; 
        margin-right: auto; /* Mueve el mensaje a la izquierda */
    }
    .spinner { 
        border: 4px solid rgba(0, 0, 0, 0.1); 
        border-left: 4px solid #3f51b5; 
        border-radius: 50%; 
        width: 20px; 
        height: 20px; 
        animation: spin 1s linear infinite; 
        margin: 5px; 
        display: inline-block; 
    }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    .chat-input {
        /* Aseguramos que el pie de página del card se vea bien con el input group */
        padding: 10px 15px;
    }
</style>

<script>
    // Obtener el token CSRF de Laravel para seguridad
    // NOTA: Esto asume que tienes la meta tag en tu plantilla 'layouts.medico'
    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '{{ csrf_token() }}';
    const chatMessages = document.getElementById('chat-messages');
    const userInput = document.getElementById('user-input');
    const sendButton = document.getElementById('send-button');

    // Función para desplazarse automáticamente al final del chat
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Función para añadir un mensaje al contenedor del chat
    function addMessage(sender, text) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}`;
        messageDiv.innerHTML = text;
        chatMessages.appendChild(messageDiv);
        scrollToBottom();
        return messageDiv;
    }

    // Función principal para enviar el mensaje
    async function sendMessage() {
        const message = userInput.value.trim();
        if (message === '') return;

        // 1. Mostrar mensaje del usuario y limpiar input
        addMessage('user', message);
        userInput.value = '';
        userInput.disabled = true;
        sendButton.disabled = true;

        // 2. Mostrar indicador de "escribiendo" del bot
        const botThinkingMessage = addMessage('bot', '<div class="spinner"></div> Analizando tu consulta...');

        try {
            // NOTA: Asegúrate que la ruta '/api/psicologia/chat' sea accesible y funcione
            const response = await fetch('/api/psicologia/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ message: message })
            });

            // Borrar el mensaje de "escribiendo"
            chatMessages.removeChild(botThinkingMessage);

            if (!response.ok) {
                const errorData = await response.json();
                addMessage('bot', `Error: ${errorData.response || 'No se pudo contactar al servicio de IA.'}`);
                return;
            }

            const data = await response.json();
            const botResponse = data.response;

            // 3. Mostrar la respuesta final del bot
            addMessage('bot', botResponse);

        } catch (error) {
            console.error('Error al enviar mensaje:', error);
            chatMessages.removeChild(botThinkingMessage);
            addMessage('bot', 'Error de conexión. Asegúrate de que tu servidor Laravel y la API Key estén correctos.');
        } finally {
            // 4. Habilitar la entrada
            userInput.disabled = false;
            sendButton.disabled = false;
            userInput.focus();
            scrollToBottom();
        }
    }
</script>
@endsection