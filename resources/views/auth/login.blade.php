<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club de Gimnasia AlphaSigma - Sistema de Análisis y Evaluación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300..0,900;1,300..1,900&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --alpha-blue-dark: #0b192c;   /* Azul Élite */
            --alpha-blue-light: #1e3e62;  /* Azul Institucional */
            --alpha-gold: #bfa15f;        /* Oro Mate */
            --alpha-text-muted: #6c757d;
        }

        body { 
            font-family: 'Montserrat', sans-serif;
            min-height: 100vh; 
            margin: 0;
            overflow-x: hidden;
            position: relative;
        }

        /* --- CONTENEDOR MULTIMEDIA COLLAGE DE FONDO (EXCLUSIVO VIDEOS) --- */
        .background-collage {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* 3 columnas, una para cada video */
            grid-template-rows: 1fr;
            z-index: -2;
            overflow: hidden;
            background-color: var(--alpha-blue-dark);
        }

        /* Bloques individuales del collage */
        .collage-item {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: var(--alpha-blue-dark);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Capa oscura unificada sobre el collage de videos para garantizar legibilidad */
        .collage-item::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg, rgba(11, 25, 44, 0.82) 0%, rgba(30, 62, 98, 0.85) 100%);
            z-index: 2;
        }

        /* Contenedor específico para el video incrustado */
        .video-wrapper {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 320%; /* Escala expandida para cubrir la columna vertical sin barras negras */
            height: 100%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            z-index: 1;
        }

        .video-wrapper iframe {
            width: 100%;
            height: 100%;
            border: none;
            transform: scale(1.3); /* Compensación de aspecto */
        }

        /* Capa global de profundidad */
        .global-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: radial-gradient(circle, rgba(11, 25, 44, 0.1) 0%, rgba(11, 25, 44, 0.5) 100%);
            z-index: -1;
            pointer-events: none;
        }

        /* --- ESTILOS DE LA INTERFAZ CENTRAL --- */
        .auth-card { 
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-top: 6px solid var(--alpha-gold);
            border-radius: 16px; 
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.6); 
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(16px);
        }

        .system-title {
            font-weight: 800;
            font-size: 1.15rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--alpha-blue-dark);
            margin-bottom: 2px;
        }

        .club-subtitle {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--alpha-gold);
            text-transform: uppercase;
            letter-spacing: 3px;
        }

        .nav-tabs-alpha {
            border-bottom: 2px solid #e9ecef;
        }

        .nav-tabs-alpha .nav-link {
            color: var(--alpha-text-muted);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
            border: none;
            padding: 14px 10px;
            letter-spacing: 1px;
            position: relative;
            background: transparent;
        }

        .nav-tabs-alpha .nav-link.active {
            color: var(--alpha-blue-dark);
            background: transparent;
        }

        .nav-tabs-alpha .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3.5px;
            background-color: var(--alpha-blue-dark);
            border-radius: 4px;
        }

        .form-control, .form-select {
            border: 1.5px solid #ced4da;
            border-radius: 8px;
            padding: 11px 14px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--alpha-blue-light);
            box-shadow: 0 0 0 3px rgba(30, 62, 98, 0.15);
        }

        .form-label {
            font-weight: 700;
            font-size: 0.75rem;
            color: var(--alpha-blue-dark);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 6px;
        }

        .btn-alpha-primary {
            background-color: var(--alpha-blue-dark);
            border: none;
            color: white;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 13px;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .btn-alpha-primary:hover {
            background-color: var(--alpha-blue-light);
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(11, 25, 44, 0.4);
        }

        .btn-alpha-success {
            background-color: var(--alpha-blue-light);
            border: none;
            color: white;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 13px;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .btn-alpha-success:hover {
            background-color: #274f7d;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(30, 62, 98, 0.4);
        }

        .alert-alpha {
            border-radius: 10px;
            font-size: 0.85rem;
            border: none;
        }

        /* Adaptabilidad del collage en dispositivos móviles (Apila los videos verticalmente) */
        @media (max-width: 768px) {
            .background-collage {
                grid-template-columns: 1fr;
                grid-template-rows: repeat(3, 1fr);
            }
            .video-wrapper {
                width: 150%;
                height: 150%;
            }
        }
    </style>
</head>
<body class="d-flex align-items-center py-5">

    <div class="background-collage">
        <div class="collage-item">
            <div class="video-wrapper">
                <iframe src="https://www.youtube.com/embed/8pNRXxDkO6o?autoplay=1&mute=1&loop=1&playlist=8pNRXxDkO6o&controls=0&showinfo=0&rel=0&modestbranding=1" allow="autoplay; encrypted-media"></iframe>
            </div>
        </div>
        
        <div class="collage-item">
            <div class="video-wrapper">
                <iframe src="https://www.youtube.com/embed/_TD2txQXZOo?autoplay=1&mute=1&loop=1&playlist=_TD2txQXZOo&controls=0&showinfo=0&rel=0&modestbranding=1" allow="autoplay; encrypted-media"></iframe>
            </div>
        </div>
        
        <div class="collage-item">
            <div class="video-wrapper">
                <iframe src="https://www.youtube.com/embed/3xGljG7yK-s?autoplay=1&mute=1&loop=1&playlist=3xGljG7yK-s&controls=0&showinfo=0&rel=0&modestbranding=1" allow="autoplay; encrypted-media"></iframe>
            </div>
        </div>
    </div>
    
    <div class="global-overlay"></div>

    <div class="container text-start">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8 col-sm-10">
                <div class="card auth-card">
                    
                    <div class="card-header bg-transparent text-center border-0 pt-4 pb-1">
                        <div class="mb-2">
                            <i class="fa-solid fa-chart-line fa-2xl" style="color: var(--alpha-blue-light);"></i>
                        </div>
                        <h3 class="system-title">Sistema de Análisis y Evaluación</h3>
                        <p class="club-subtitle">Club de Gimnasia AlphaSigma</p>
                    </div>
                    
                    <div class="card-body px-4 pb-4">
                        @if(session('success'))
                            <div class="alert alert-success alert-alpha alert-dismissible fade show" role="alert">
                                <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        
                        @if($errors->any())
                            <div class="alert alert-danger alert-alpha alert-dismissible fade show" role="alert">
                                <strong class="d-block mb-1"><i class="fa-solid fa-triangle-exclamation me-2"></i>Verifique los datos:</strong>
                                <ul class="mb-0 ps-3">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <ul class="nav nav-tabs-alpha nav-justified mb-4" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#login">
                                    <i class="fa-solid fa-key me-2"></i>Acceder
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#register">
                                    <i class="fa-solid fa-user-plus me-2"></i>Registro de Gimnasta
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="login">
                                <form method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label"><i class="fa-solid fa-envelope me-2"></i>Email Institucional</label>
                                        <input type="text" name="username" class="form-control" value="{{ old('username') }}" required placeholder="usuario@alphasigma.com">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label"><i class="fa-solid fa-earth-americas me-2"></i>Nacionalidad / Federación</label>
                                        <select name="nacionalidad" class="form-select" required id="nacionalidadSelect">
                                            <option value="">Seleccionar país...</option>
                                            <option value="Argentina">Argentina</option>
                                            <option value="Bolivia">Bolivia</option>
                                            <option value="Brasil">Brasil</option>
                                            <option value="Chile">Chile</option>
                                            <option value="Colombia">Colombia</option>
                                            <option value="Ecuador">Ecuador</option>
                                            <option value="México">México</option>
                                            <option value="Paraguay">Paraguay</option>
                                            <option value="Perú">Perú</option>
                                            <option value="Uruguay">Uruguay</option>
                                            <option value="Venezuela">Venezuela</option>
                                            <option value="Estados Unidos">Estados Unidos</option>
                                            <option value="España">España</option>
                                            <option value="Otro">Otro país</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label"><i class="fa-solid fa-id-card me-2"></i><span id="documentoLabel">Documento de Identidad</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text font-monospace fw-bold" id="documentoPrefijo" style="border-right: none; display: none;">ID</span>
                                            <input type="text" name="cedula" class="form-control" value="{{ old('cedula') }}" required placeholder="Código numérico de acceso" id="documentoInput">
                                        </div>
                                        <div class="form-text text-muted" id="documentoAyuda"><i class="fa-solid fa-circle-question me-1"></i>Seleccione nacionalidad para validar formato</div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label"><i class="fa-solid fa-lock me-2"></i>Contraseña</label>
                                        <input type="password" name="password" class="form-control" required placeholder="••••••••">
                                    </div>
                                    
                                    <button type="submit" class="btn btn-alpha-primary w-100 shadow-sm">
                                        <i class="fa-solid fa-shield-halved me-2"></i>Autenticar Ingreso
                                    </button>
                                </form>
                            </div>
                            
                            <div class="tab-pane fade" id="register">
                                <form method="POST" action="{{ route('register') }}">
                                    @csrf
                                    <div class="row g-3 mb-3">
                                        <div class="col-sm-6">
                                            <label class="form-label"><i class="fa-solid fa-user me-2"></i>Nombre</label>
                                            <input type="text" name="Nombre" class="form-control" value="{{ old('Nombre') }}" required placeholder="Nombre" maxlength="20" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label"><i class="fa-solid fa-user me-2"></i>Apellido</label>
                                            <input type="text" name="Apellido" class="form-control" value="{{ old('Apellido') }}" required placeholder="Apellido" maxlength="20" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+">
                                        </div>
                                    </div>
                                    
                                    <div class="row g-3 mb-3">
                                        <div class="col-sm-6">
                                            <label class="form-label"><i class="fa-solid fa-envelope me-2"></i>Email Contacto</label>
                                            <input type="email" name="Email" class="form-control" value="{{ old('Email') }}" required placeholder="correo@ejemplo.com" maxlength="50">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label"><i class="fa-solid fa-earth-americas me-2"></i>Nacionalidad</label>
                                            <select name="Nacionalidad" class="form-select" required id="regNacionalidadSelect">
                                                <option value="">Seleccionar...</option>
                                                <option value="Argentina">Argentina</option>
                                                <option value="Bolivia">Bolivia</option>
                                                <option value="Brasil">Brasil</option>
                                                <option value="Chile">Chile</option>
                                                <option value="Colombia">Colombia</option>
                                                <option value="Ecuador">Ecuador</option>
                                                <option value="México">México</option>
                                                <option value="Paraguay">Paraguay</option>
                                                <option value="Perú">Perú</option>
                                                <option value="Uruguay">Uruguay</option>
                                                <option value="Venezuela">Venezuela</option>
                                                <option value="Estados Unidos">Estados Unidos</option>
                                                <option value="Otro">Otro</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="row g-2 mb-3">
                                        <div class="col-sm-4">
                                            <label class="form-label"><i class="fa-solid fa-cake-candles me-2"></i>F. Nacimiento</label>
                                            <input type="date" name="FechaNacimiento" class="form-control" value="{{ old('FechaNacimiento') }}" required>
                                        </div>
                                        <div class="col-sm-4">
                                            <label class="form-label"><i class="fa-solid fa-weight-scale me-2"></i>Peso (kg)</label>
                                            <input type="number" name="Peso" class="form-control" step="0.1" min="25" max="150" value="{{ old('Peso') }}" required placeholder="00.0">
                                        </div>
                                        <div class="col-sm-4">
                                            <label class="form-label"><i class="fa-solid fa-ruler-vertical me-2"></i>Altura (m)</label>
                                            <input type="number" name="Altura" class="form-control" step="0.01" min="1.00" max="2.20" value="{{ old('Altura') }}" required placeholder="0.00">
                                        </div>
                                    </div>
                                    
                                    <div class="row g-3 mb-3">
                                        <div class="col-sm-6">
                                            <label class="form-label"><i class="fa-solid fa-id-card me-2"></i>Documento</label>
                                            <input type="text" name="CedulaIdentidad" class="form-control" value="{{ old('CedulaIdentidad') }}" required placeholder="Nro de identidad" id="cedulaInput">
                                            <div class="form-text text-muted" id="cedulaHelp">Formato según origen</div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label"><i class="fa-solid fa-medal me-2"></i>Nivel Técnico</label>
                                            <select name="Nivel" class="form-select" required>
                                                <option value="">Seleccionar nivel...</option>
                                                <option value="Principiante">Principiante</option>
                                                <option value="Intermedio">Intermedio</option>
                                                <option value="Avanzado">Avanzado</option>
                                                <option value="Elite">Élite FIG</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row g-3 mb-4">
                                        <div class="col-sm-6">
                                            <label class="form-label"><i class="fa-solid fa-lock me-2"></i>Contraseña</label>
                                            <input type="password" name="password" class="form-control" required placeholder="Mínimo 6 caracteres">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label"><i class="fa-solid fa-lock me-2"></i>Confirmar Contraseña</label>
                                            <input type="password" name="password_confirmation" class="form-control" required placeholder="Repite la contraseña">
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-dark alert-alpha d-flex align-items-center mb-4" style="background-color: rgba(11, 25, 44, 0.05); border: 1px solid rgba(11, 25, 44, 0.1);">
                                        <i class="fa-solid fa-user-shield fa-lg me-3" style="color: var(--alpha-blue-light);"></i>
                                        <div class="text-dark small">
                                            <strong>Cuenta Inmediata:</strong> Al registrarte, tu cuenta quedará habilitada al instante para iniciar sesión en la pestaña "Acceder".
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-alpha-success w-100 shadow-sm">
                                        <i class="fa-solid fa-user-plus me-2"></i>Finalizar e Ingresar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const formatosDocumento = {
            'Venezuela': { label: 'Cédula', prefijo: 'V-', placeholder: '12345678', help: 'Cédula de identidad oficial' },
            'Colombia': { label: 'Cédula', prefijo: '', placeholder: '12345678', help: 'Cédula de Ciudadanía' },
            'México': { label: 'CURP', prefijo: '', placeholder: 'CURP12345678901234', help: 'Clave Única de Registro' },
            'Perú': { label: 'DNI', prefijo: '', placeholder: '12345678', help: 'DNI nacional' },
            'Bolivia': { label: 'C.I.', prefijo: '', placeholder: '1234567', help: 'Cédula de Identidad' },
            'Chile': { label: 'RUT', prefijo: '', placeholder: '12345678-9', help: 'RUT con guión' },
            'Argentina': { label: 'DNI', prefijo: '', placeholder: '12345678', help: 'DNI nacional' },
            'Brasil': { label: 'CPF', prefijo: '', placeholder: '12345678901', help: 'CPF (solo dígitos)' },
            'Ecuador': { label: 'Cédula', prefijo: '', placeholder: '1234567890', help: 'Cédula de ciudadanía' },
            'Uruguay': { label: 'Cédula', prefijo: '', placeholder: '1234567-8', help: 'Cédula con dígito' },
            'Paraguay': { label: 'C.I.', prefijo: '', placeholder: '1234567', help: 'Cédula de Identidad' },
            'Estados Unidos': { label: 'SSN / ID', prefijo: '', placeholder: '123-45-6789', help: 'Format Social Security / US ID' },
            'España': { label: 'DNI / NIE', prefijo: '', placeholder: '12345678A', help: 'Identificación oficial con letra' },
            'Otro': { label: 'Documento', prefijo: '', placeholder: 'Número oficial', help: 'Pasaporte o documento de origen' }
        };

        document.getElementById('nacionalidadSelect').addEventListener('change', function() {
            actualizarInterfazDocumento(this.value, 'documentoLabel', 'documentoPrefijo', 'documentoInput', 'documentoAyuda');
        });

        document.getElementById('regNacionalidadSelect').addEventListener('change', function() {
            const nacionalidad = this.value;
            const regCedulaInput = document.getElementById('cedulaInput');
            const regCedulaHelp = document.getElementById('cedulaHelp');
            
            if (formatosDocumento[nacionalidad]) {
                regCedulaInput.placeholder = formatosDocumento[nacionalidad].placeholder;
                regCedulaHelp.textContent = formatosDocumento[nacionalidad].help;
            } else {
                regCedulaInput.placeholder = 'Nro de identificación';
                regCedulaHelp.textContent = 'Formato oficial de origen';
            }
        });

        function actualizarInterfazDocumento(pais, labelId, prefijoId, inputId, ayudaId) {
            const label = document.getElementById(labelId);
            const prefijo = document.getElementById(prefijoId);
            const input = document.getElementById(inputId);
            const ayuda = document.getElementById(ayudaId);
            
            if (formatosDocumento[pais]) {
                const config = formatosDocumento[pais];
                label.textContent = config.label;
                prefijo.textContent = config.prefijo || '';
                input.placeholder = config.placeholder;
                ayuda.innerHTML = `<i class="fa-solid fa-circle-info me-1"></i>${config.help}`;
                prefijo.style.display = config.prefijo ? 'block' : 'none';
            } else {
                label.textContent = 'Documento de Identidad';
                prefijo.textContent = '';
                input.placeholder = 'Código numérico de acceso';
                ayuda.innerHTML = `<i class="fa-solid fa-circle-question me-1"></i>Seleccione nacionalidad para validar formato`;
                prefijo.style.display = 'none';
            }
        }
    </script>
</body>
</html>