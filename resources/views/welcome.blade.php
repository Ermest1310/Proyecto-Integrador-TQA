<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>GymPro Manager - Sistema de Gestión Deportiva</title>

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --gym-primary: #1e3a8a;
            --gym-secondary: #059669;
            --gym-accent: #d97706;
            --gym-dark: #1f2937;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        .hero-section {
            background: linear-gradient(rgba(30, 58, 138, 0.9), rgba(30, 58, 138, 0.7)), 
                        url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?ixlib=rb-4.0.3') center/cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
        }
        
        .hero-content {
            z-index: 2;
            text-align: center;
            color: white;
        }
        
        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            animation: fadeInUp 1s ease-out;
        }
        
        .hero-subtitle {
            font-size: 1.5rem;
            font-weight: 300;
            margin-bottom: 2rem;
            opacity: 0.9;
            animation: fadeInUp 1s ease-out 0.3s both;
        }
        
        .gym-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            transition: all 0.4s ease;
            overflow: hidden;
            position: relative;
        }
        
        .gym-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 30px 60px rgba(0,0,0,0.2);
        }
        
        .gym-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--gym-primary), var(--gym-secondary), var(--gym-accent));
        }
        
        .gym-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, var(--gym-primary), var(--gym-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .btn-gym {
            background: linear-gradient(45deg, var(--gym-primary), var(--gym-secondary));
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        
        .btn-gym:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(30, 58, 138, 0.3);
            color: white;
        }
        
        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }
        
        .floating-elements i {
            position: absolute;
            color: rgba(255,255,255,0.1);
            animation: float 6s ease-in-out infinite;
        }
        
        .floating-elements i:nth-child(1) { top: 20%; left: 10%; font-size: 3rem; animation-delay: 0s; }
        .floating-elements i:nth-child(2) { top: 60%; right: 15%; font-size: 2rem; animation-delay: 2s; }
        .floating-elements i:nth-child(3) { bottom: 30%; left: 20%; font-size: 2.5rem; animation-delay: 4s; }
        .floating-elements i:nth-child(4) { top: 40%; right: 30%; font-size: 1.5rem; animation-delay: 1s; }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(10deg); }
        }
        
        .stats-section {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            margin: 3rem 0;
        }
        
        .stat-item {
            text-align: center;
            color: white;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            color: var(--gym-accent);
        }
        
        .navbar-gym {
            background: rgba(255,255,255,0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>



<!-- Hero Section -->
<section class="hero-section">
    <div class="floating-elements">
        <i class="fas fa-dumbbell"></i>
        <i class="fas fa-running"></i>
        <i class="fas fa-trophy"></i>
        <i class="fas fa-heartbeat"></i>
    </div>
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="hero-content">
                    <h1 class="hero-title">
                        <i class="fas fa-dumbbell mr-3"></i>
                        GymPro Manager
                    </h1>
                    <p class="hero-subtitle">
                        El sistema de gestión deportiva más completo y profesional
                    </p>
                    
                    <div class="mt-4">
                        <a href="{{ url('/gimnastas') }}" class="btn btn-gym btn-lg mr-3">
                            <i class="fas fa-tachometer-alt mr-2"></i>Acceder al Sistema
                        </a>
                        <a href="{{ url('/roles') }}" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-cog mr-2"></i>Administración
                        </a>
                    </div>
                    
                    <!-- Stats Section -->
                    <div class="stats-section">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="stat-item">
                                    <div class="stat-number">500+</div>
                                    <div>Gimnastas</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-item">
                                    <div class="stat-number">50+</div>
                                    <div>Entrenadores</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-item">
                                    <div class="stat-number">1000+</div>
                                    <div>Entrenamientos</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-item">
                                    <div class="stat-number">100+</div>
                                    <div>Competencias</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Feature Cards -->
        <div class="row mt-5">
            <div class="col-lg-4 mb-4">
                <div class="gym-card">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-running gym-icon"></i>
                        <h4 class="font-weight-bold mb-3">Gestión de Gimnastas</h4>
                        <p class="text-muted mb-4">Control completo de atletas con seguimiento de rendimiento, datos personales y progreso deportivo.</p>
                        <a href="{{ url('/gimnastas') }}" class="btn btn-gym">Explorar Gimnastas</a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="gym-card">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-user-graduate gym-icon"></i>
                        <h4 class="font-weight-bold mb-3">Entrenadores</h4>
                        <p class="text-muted mb-4">Administra el equipo de entrenadores profesionales y su especialización deportiva.</p>
                        <a href="{{ url('/entrenadores') }}" class="btn btn-gym">Ver Entrenadores</a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="gym-card">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-stopwatch gym-icon"></i>
                        <h4 class="font-weight-bold mb-3">Entrenamientos</h4>
                        <p class="text-muted mb-4">Planifica, programa y supervisa sesiones de entrenamiento con métricas detalladas y análisis.</p>
                        <a href="{{ url('/entrenamientos') }}" class="btn btn-gym">Ver Entrenamientos</a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="gym-card">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-trophy gym-icon"></i>
                        <h4 class="font-weight-bold mb-3">Competencias</h4>
                        <p class="text-muted mb-4">Organiza eventos deportivos, gestiona participantes y registra resultados profesionalmente.</p>
                        <a href="{{ url('/competencias') }}" class="btn btn-gym">Gestionar Competencias</a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="gym-card">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-heartbeat gym-icon"></i>
                        <h4 class="font-weight-bold mb-3">Lesiones</h4>
                        <p class="text-muted mb-4">Registro y seguimiento médico de lesiones para garantizar la salud de los atletas.</p>
                        <a href="{{ url('/lesiones') }}" class="btn btn-gym">Gestionar Lesiones</a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="gym-card">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-dumbbell gym-icon"></i>
                        <h4 class="font-weight-bold mb-3">Aparatos</h4>
                        <p class="text-muted mb-4">Inventario y mantenimiento de equipamiento deportivo y aparatos de gimnasia.</p>
                        <a href="{{ url('/aparatos') }}" class="btn btn-gym">Ver Aparatos</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- AdminLTE & Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
