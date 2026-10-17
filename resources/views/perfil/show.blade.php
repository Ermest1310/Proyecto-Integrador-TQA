@if(session('usuario_rol') == 'Gimnasta')
    @extends('layouts.gimnasta')
@elseif(session('usuario_rol') == 'Entrenador')
    @extends('layouts.entrenador')
@elseif(in_array(session('usuario_rol'), ['Médico', 'Fisioterapeuta']))
    @extends('layouts.medico')
@elseif(session('usuario_rol') == 'Administrador')
    @extends('layouts.admin')
@else
    @extends('layouts.adminlte')
@endif

@section('title', 'Mi Perfil')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-user"></i> Mi Perfil</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('perfil.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="Email" class="form-control" 
                                           value="{{ $usuario->Email }}" required>
                                    @error('Email')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Nacionalidad</label>
                                    <select name="Nacionalidad" class="form-control" required>
                                        <option value="Argentina" {{ $usuario->Nacionalidad == 'Argentina' ? 'selected' : '' }}>Argentina</option>
                                        <option value="Bolivia" {{ $usuario->Nacionalidad == 'Bolivia' ? 'selected' : '' }}>Bolivia</option>
                                        <option value="Brasil" {{ $usuario->Nacionalidad == 'Brasil' ? 'selected' : '' }}>Brasil</option>
                                        <option value="Chile" {{ $usuario->Nacionalidad == 'Chile' ? 'selected' : '' }}>Chile</option>
                                        <option value="Colombia" {{ $usuario->Nacionalidad == 'Colombia' ? 'selected' : '' }}>Colombia</option>
                                        <option value="Ecuador" {{ $usuario->Nacionalidad == 'Ecuador' ? 'selected' : '' }}>Ecuador</option>
                                        <option value="Paraguay" {{ $usuario->Nacionalidad == 'Paraguay' ? 'selected' : '' }}>Paraguay</option>
                                        <option value="Perú" {{ $usuario->Nacionalidad == 'Perú' ? 'selected' : '' }}>Perú</option>
                                        <option value="Uruguay" {{ $usuario->Nacionalidad == 'Uruguay' ? 'selected' : '' }}>Uruguay</option>
                                        <option value="Venezuela" {{ $usuario->Nacionalidad == 'Venezuela' ? 'selected' : '' }}>Venezuela</option>
                                        <option value="México" {{ $usuario->Nacionalidad == 'México' ? 'selected' : '' }}>México</option>
                                        <option value="Otro" {{ $usuario->Nacionalidad == 'Otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                    @error('Nacionalidad')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Documento de Identidad</label>
                                    <input type="text" name="CedulaIdentidad" class="form-control" 
                                           value="{{ $usuario->CedulaIdentidad }}" required>
                                    @error('CedulaIdentidad')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Campos comunes -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" class="form-control" value="{{ $usuario->Nombre }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Apellido</label>
                                    <input type="text" class="form-control" value="{{ $usuario->Apellido }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Rol</label>
                                    <input type="text" class="form-control" value="{{ $usuario->rol->NombreRol }}" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Campos específicos por rol -->
                        @if($usuario->rol->NombreRol === 'Gimnasta')
                            <h5 class="mt-3"><i class="fas fa-dumbbell"></i> Información de Gimnasta</h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Fecha de Nacimiento</label>
                                        <input type="text" class="form-control" value="{{ $usuario->FechaNacimiento }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Nacionalidad</label>
                                        <input type="text" class="form-control" value="{{ $usuario->Nacionalidad }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Peso (kg)</label>
                                        <input type="text" class="form-control" value="{{ $usuario->Peso }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Altura (m)</label>
                                        <input type="text" class="form-control" value="{{ $usuario->Altura }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nivel</label>
                                        <input type="text" class="form-control" value="{{ $usuario->Nivel }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Entrenador Asignado</label>
                                        <input type="text" class="form-control" 
                                               value="{{ $usuario->entrenador ? $usuario->entrenador->Nombre . ' ' . $usuario->entrenador->Apellido : 'No asignado' }}" readonly>
                                    </div>
                                </div>
                            </div>
                        @elseif(in_array($usuario->rol->NombreRol, ['Entrenador', 'Médico', 'Fisioterapeuta']))
                            <h5 class="mt-3"><i class="fas fa-user-tie"></i> Información Profesional</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Contacto</label>
                                        <input type="text" class="form-control" value="{{ $usuario->Contacto ?? 'No registrado' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Estado</label>
                                        <input type="text" class="form-control" value="{{ $usuario->activo ? 'Activo' : 'Inactivo' }}" readonly>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Estado</label>
                                        <input type="text" class="form-control" value="{{ $usuario->activo ? 'Activo' : 'Inactivo' }}" readonly>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> <strong>Nota:</strong> Al cambiar tu documento de identidad, este se convertirá en tu nueva contraseña de acceso.
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Perfil
                            </button>
                            @if(session('usuario_rol') == 'Gimnasta')
                                <a href="{{ route('gimnasta.dashboard') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </a>
                            @elseif(session('usuario_rol') == 'Entrenador')
                                <a href="{{ route('entrenador.dashboard') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </a>
                            @elseif(in_array(session('usuario_rol'), ['Médico', 'Fisioterapeuta']))
                                <a href="{{ route('medico.dashboard') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </a>
                            @else
                                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection