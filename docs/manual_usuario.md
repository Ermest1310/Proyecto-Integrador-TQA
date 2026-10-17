# Manual de Usuario

## Sistema de Gestión Gimnástica de Alto Rendimiento

---

## Índice

1. [Introducción](#1-introducción)
2. [Requisitos del Sistema](#2-requisitos-del-sistema)
3. [Acceso al Sistema](#3-acceso-al-sistema)
4. [Roles de Usuario](#4-roles-de-usuario)
5. [Módulo Administrador](#5-módulo-administrador)
6. [Módulo Entrenador](#6-módulo-entrenador)
7. [Módulo Gimnasta](#7-módulo-gimnasta)
8. [Módulo Médico / Fisioterapeuta](#8-módulo-médico--fisioterapeuta)
9. [Módulo Juez](#9-módulo-juez)
10. [Área Médica](#10-área-médica)
11. [Análisis de Movimiento y Video](#11-análisis-de-movimiento-y-video)
12. [Gestión de Aparatos](#12-gestión-de-aparatos)
13. [Gestión de Competencias](#13-gestión-de-competencias)
14. [Solicitudes de Nivel](#14-solicitudes-de-nivel)
15. [Postulaciones](#15-postulaciones)
16. [Reportes y Backups](#16-reportes-y-backups)
17. [Perfil de Usuario](#17-perfil-de-usuario)
18. [Preguntas Frecuentes](#18-preguntas-frecuentes)

---

## 1. Introducción

El **Sistema de Gestión Gimnástica de Alto Rendimiento** es una plataforma integral diseñada para administrar todos los aspectos de un centro de entrenamiento de gimnasia o federación deportiva. Permite gestionar desde el registro de gimnastas, la programación de sesiones de entrenamiento, evaluaciones médicas, seguimiento de lesiones, análisis de video con inteligencia artificial, hasta la calificación de rutinas en competencias.

### 1.1 Propósito del Sistema

- Centralizar la información de todos los actores del entorno gimnástico (administradores, entrenadores, gimnastas, médicos, fisioterapeutas y jueces).
- Facilitar la programación y seguimiento de sesiones de entrenamiento.
- Proveer herramientas de evaluación médica integral (física, nutricional, psicológica, respiratoria y fisiológica).
- Permitir el análisis de movimiento mediante video con inteligencia artificial.
- Gestionar competencias y evaluaciones de rutinas según el Código de Puntos FIG.
- Mantener un registro histórico de lesiones y rehabilitaciones.

---

## 2. Requisitos del Sistema

### 2.1 Requisitos de Software (Servidor)

- PHP 8.1 o superior
- Composer
- MySQL 8.0 o superior / MariaDB 10.3+
- Node.js 18+ y NPM
- Extensiones PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, GD, Curl

### 2.2 Requisitos de Cliente (Navegador)

- Navegador web moderno (Chrome 90+, Firefox 88+, Edge 90+, Safari 14+)
- JavaScript habilitado
- Conexión a internet
- Resolución mínima recomendada: 1280x720

### 2.3 Requisitos para Análisis de Video

- Cámara web o dispositivo de grabación
- Archivos de video en formato MP4, WebM, OGG, MOV o AVI
- Peso máximo: 200 MB por archivo
- Conexión a internet para análisis con IA (OpenAI)

---

## 3. Acceso al Sistema

### 3.1 Inicio de Sesión

Para acceder al sistema:

1. Abra su navegador web y diríjase a la URL del sistema.
2. Haga clic en **"Iniciar Sesión"**.
3. Ingrese sus credenciales:
   - **Correo electrónico**
   - **Nacionalidad**
   - **Cédula de identidad**
   - **Contraseña**
4. Haga clic en el botón **"Ingresar"**.

![Pantalla de Login](ruta/a/imagen-login.png)

> **Nota:** Si olvidó su contraseña, contacte al administrador del sistema para restablecerla.

### 3.2 Registro de Nuevos Usuarios

El registro está disponible únicamente a través del formulario de **Postulaciones** (ver sección 15). Los administradores pueden crear usuarios directamente desde el panel de administración.

### 3.3 Cierre de Sesión

Haga clic en su nombre de usuario en la esquina superior derecha y seleccione **"Cerrar Sesión"**.

---

## 4. Roles de Usuario

El sistema cuenta con 6 roles definidos, cada uno con permisos y funcionalidades específicas:

| Rol | Descripción |
|-----|-------------|
| **Administrador** | Acceso completo al sistema. Gestiona usuarios, roles, postulaciones, solicitudes de nivel, reportes y auditoría. |
| **Entrenador** | Gestiona sesiones de entrenamiento, visualiza y da seguimiento a sus gimnastas asignados, recomienda cambios de nivel. |
| **Gimnasta** | Programa sus sesiones de entrenamiento, visualiza su progreso, historial y entrenador asignado. Solicita cambios de nivel. |
| **Médico** | Realiza evaluaciones médicas, da seguimiento a lesiones y revisa el historial médico de los gimnastas. |
| **Fisioterapeuta** | Similar al rol Médico, enfocado en rehabilitación y fisioterapia. |
| **Juez** | Evalúa rutinas gimnásticas en competencias, asigna puntuaciones y aplica deducciones según el Código de Puntos FIG. |

---

## 5. Módulo Administrador

El panel de administración es el centro de control del sistema. Solo los usuarios con rol **Administrador** tienen acceso.

### 5.1 Dashboard Principal

Al iniciar sesión como administrador, verá un panel con estadísticas generales:

- **Total de usuarios** registrados en el sistema
- **Total de gimnastas** activos
- **Total de entrenadores**
- **Sesiones de hoy**
- **Sesiones programadas** vs **completadas**
- **Aparatos operativos**
- **Lesiones activas**

### 5.2 Inicio del Administrador

Ruta: `/admin/inicio`

Panel con estadísticas detalladas y resumen de actividades recientes.

### 5.3 Historial de Sesiones

Ruta: `/admin/historial`

Permite visualizar y filtrar el historial completo de sesiones de entrenamiento por:

- Estado (Programada, Completada, Cancelada)
- Gimnasta
- Entrenador
- Aparato
- Rango de fechas

### 5.4 Gestión de Roles

Ruta: `/roles`

Permite crear, editar, activar y desactivar roles del sistema.

**Acciones disponibles:**
- **Crear rol:** Defina el nombre y permisos del nuevo rol.
- **Editar rol:** Modifique el nombre y permisos de un rol existente.
- **Desactivar rol:** Deshabilite un rol sin eliminarlo.
- **Reactivar rol:** Vuelva a habilitar un rol desactivado.

### 5.5 Gestión de Usuarios

Ruta: `/usuarios-unificado`

Administración completa de todos los usuarios del sistema.

**Acciones disponibles:**
- **Crear usuario:** Registre un nuevo usuario con sus datos personales, rol y credenciales.
- **Editar usuario:** Modifique los datos de un usuario existente.
- **Desactivar usuario:** Deshabilite temporalmente el acceso de un usuario.
- **Reactivar usuario:** Restaure el acceso de un usuario desactivado.
- **Filtrar por rol:** Visualice usuarios por rol (gimnastas, entrenadores, médicos, fisioterapeutas).

### 5.6 Auditoría

Ruta: `/admin/audit`

Registro detallado de todas las actividades realizadas en el sistema:

- Inicios de sesión (exitosos y fallidos)
- Operaciones CRUD (crear, leer, actualizar, eliminar)
- Eventos de seguridad
- Control de acceso

Cada registro incluye: usuario, rol, fecha y hora, dirección IP, acción realizada y datos asociados.

### 5.7 Solicitudes de Nivel

Ruta: `/admin/solicitudes-nivel`

Gestiona las solicitudes de cambio de nivel de los gimnastas (ver sección 14).

### 5.8 Reportes y Backups

Ruta: `/admin/reportes`

Ver sección 16 para más detalles.

---

## 6. Módulo Entrenador

### 6.1 Dashboard del Entrenador

Ruta: `/entrenador/dashboard`

Al acceder, el entrenador visualiza:

- **Total de sesiones** realizadas
- **Sesiones completadas**
- **Sesiones programadas**
- **Gimnastas activos** bajo su cargo
- **Próximas sesiones** (próximos 7 días)

### 6.2 Mis Gimnastas

Ruta: `/entrenador/gimnastas`

Lista de todos los gimnastas asignados al entrenador. Cada gimnasta muestra:

- Nombre completo
- Indicador de lesión activa (si aplica)
- Enlace al dashboard individual del gimnasta

### 6.3 Dashboard del Gimnasta (vista Entrenador)

Ruta: `/entrenador/gimnasta/{id}/dashboard`

Panel detallado de un gimnasta específico con:

- Información personal del gimnasta
- Historial de sesiones
- Próximas sesiones programadas

### 6.4 Ver Gimnasta

Ruta: `/entrenador/gimnasta/{id}`

Ficha completa del gimnasta con todos sus datos personales y deportivos.

### 6.5 Crear Gimnasta

Ruta: `/entrenador/crear-gimnasta`

Formulario para registrar un nuevo gimnasta en el sistema. Campos requeridos:

- Nombres y apellidos
- Fecha de nacimiento
- Nacionalidad
- Cédula de identidad
- Correo electrónico
- Género
- Teléfono
- Dirección
- Nivel actual
- Información del entrenador asignado

### 6.6 Mis Sesiones

Ruta: `/entrenador/sesiones`

Lista de todas las sesiones de entrenamiento asignadas al entrenador, con opción de filtrado por estado y fecha.

### 6.7 Calendario

Ruta: `/entrenador/calendario`

Vista de calendario con todas las sesiones de entrenamiento. Las sesiones se muestran con códigos de color:

- **Verde:** Sesión completada
- **Azul:** Sesión programada

### 6.8 Solicitudes de Nivel

Ruta: `/entrenador/solicitudes-nivel`

Permite al entrenador:

- **Recomendar** un cambio de nivel para un gimnasta
- **Aprobar** o **rechazar** solicitudes de nivel de gimnastas

---

## 7. Módulo Gimnasta

### 7.1 Dashboard del Gimnasta

Ruta: `/gimnasta/dashboard`

Al acceder, el gimnasta visualiza:

- **Total de sesiones** realizadas
- **Sesiones completadas** y **programadas**
- **Próximas sesiones**
- **Últimas 5 sesiones completadas** (actividad reciente)

### 7.2 Mi Progreso

Ruta: `/gimnasta/progreso`

Página de progreso personal donde el gimnasta puede ver su evolución a lo largo del tiempo.

### 7.3 Mi Entrenador

Ruta: `/gimnasta/entrenador`

Información del entrenador asignado y estadísticas por entrenador.

### 7.4 Mis Sesiones

Ruta: `/gimnasta/sesiones`

Lista de todas las sesiones de entrenamiento del gimnasta, con opciones para:

- **Crear nueva sesión** (ver 7.5)
- **Cancelar sesión** (solo si está en estado "Programada")

### 7.5 Crear Sesión de Entrenamiento

Ruta: `/gimnasta/crear-sesion`

Para crear una nueva sesión:

> **Requisito:** El gimnasta no debe tener ninguna lesión activa.

1. Haga clic en **"Nueva Sesión"**.
2. Seleccione los siguientes campos:
   - **Fecha y hora** de la sesión
   - **Aparato** a utilizar (solo se muestran los disponibles/operativos)
   - **Entrenador:** Puede elegir entre:
     - **Asignación automática:** El sistema asigna al entrenador con menos sesiones en el último mes (balance de carga).
     - **Selección manual:** Elija un entrenador específico de la lista.
3. Agregue **notas** o comentarios para el entrenador (opcional).
4. Haga clic en **"Guardar"**.

> **Nota:** El sistema valida automáticamente que no existan conflictos de horario con otras sesiones.

### 7.6 Cancelar Sesión

Solo puede cancelar sesiones que estén en estado **"Programada"**. Una vez que la sesión ha sido completada o ya pasó la fecha, no es posible cancelarla.

### 7.7 Historial

Ruta: `/gimnasta/historial`

Historial completo de todas las sesiones realizadas, con filtros por:

- Fecha (desde / hasta)
- Estado
- Entrenador
- Aparato

### 7.8 Calendario

Ruta: `/gimnasta/calendario`

Vista de calendario con todas las sesiones, usando códigos de color:

- **Verde:** Sesión completada
- **Azul:** Sesión programada

### 7.9 Solicitudes de Nivel

Ruta: `/gimnasta/solicitudes-nivel`

El gimnasta puede solicitar un cambio de nivel (ej: de "Intermedio" a "Avanzado").

**Reglas:**
- Solo se puede solicitar un nivel superior al actual.
- Solo se permite un salto de nivel a la vez (no se puede saltar de Principiante a Avanzado directamente).
- La solicitud pasa por aprobación del entrenador y luego del administrador.

**Proceso:**
1. El gimnasta envía la solicitud con un comentario.
2. El entrenador recibe la notificación y puede recomendar, aprobar o rechazar.
3. El administrador revisa y da la aprobación final.

---

## 8. Módulo Médico / Fisioterapeuta

### 8.1 Dashboard Médico

Ruta: `/medico/dashboard`

Lista de todos los gimnastas activos en el sistema, con acceso rápido a su información médica.

### 8.2 Ver Gimnasta

Ruta: `/medico/gimnasta/{id}`

Ficha del gimnasta con datos personales y acceso a:

- Historial médico (ver 8.3)
- Gestión de lesiones (ver 8.4)

### 8.3 Historial Médico

Ruta: `/medico/historial/{id}`

Historial clínico completo del gimnasta, incluyendo:

- Lesiones registradas
- Evaluaciones médicas realizadas
- Tratamientos y su evolución

### 8.4 Gestión de Lesiones

Ruta: `/medico/lesiones`

Módulo completo para el registro y seguimiento de lesiones.

**Tipos de lesiones:**
| Tipo | Descripción |
|------|-------------|
| Esguince | Lesión de ligamentos |
| Luxación | Dislocación articular |
| Fractura | Rotura ósea |
| Tendinitis | Inflamación del tendón |
| Contractura | Contracción muscular involuntaria |
| Desgarro | Rotura de fibras musculares |
| Contusión | Golpe o hematoma |
| Sobrecarga | Fatiga muscular excesiva |

**Niveles de gravedad:**
- **Leve**
- **Moderada**
- **Grave**

**Estados de la lesión:**
- **Lesionado:** Lesión activa, el gimnasta no puede entrenar.
- **En Rehabilitación:** El gimnasta está en proceso de recuperación.
- **Recuperada:** La lesión ha sanado completamente.

**Partes del cuerpo:** El sistema contempla 17 partes del cuerpo para localizar la lesión.

**Acciones disponibles:**
- **Registrar lesión:** Cree un nuevo registro con tipo, gravedad, parte del cuerpo, descripción y tiempo estimado de recuperación.
- **Editar lesión:** Actualice el estado o detalles de una lesión existente.
- **Eliminar lesión:** Solo si fue registrada por error.

### 8.5 Evaluaciones Médicas

El médico puede realizar cinco tipos de evaluaciones:

1. **Aptitud Médica** - Evaluación general de la condición física del gimnasta.
2. **Evaluación Nutricional** - Análisis de hábitos alimenticios y estado nutricional.
3. **Evaluación Respiratoria** - Evaluación de la capacidad y función respiratoria.
4. **Evaluación Psicológica** - Evaluación del estado mental y emocional.
5. **Evaluación Fisiológica** - Evaluación de funciones fisiológicas.

Cada evaluación se registra en el sistema y queda disponible en el historial del gimnasta.

---

## 9. Módulo Juez

### 9.1 Dashboard del Juez

Ruta: `/juez/dashboard`

Panel principal con todas las evaluaciones asignadas, ordenadas por estado:

1. **Pendiente** - Evaluaciones por realizar
2. **En revisión** - Evaluaciones en proceso
3. **Confirmado** - Evaluaciones completadas y confirmadas

### 9.2 Crear Evaluación

Ruta: `/juez/crear`

Para crear una nueva evaluación:

1. Seleccione el **gimnasta** a evaluar.
2. Seleccione la **competencia** asociada.
3. Seleccione el **aparato** de la rutina.
4. Haga clic en **"Guardar"**.

### 9.3 Evaluar Rutina

Ruta: `/juez/evaluar/{id}`

Pantalla principal de evaluación con las siguientes secciones:

#### 9.3.1 Video de la Rutina
- Suba un video de la rutina del gimnasta.
- Formatos aceptados: MP4, WebM, OGG, MOV, AVI.
- Peso máximo: 200 MB.

#### 9.3.2 Puntuación
- **Nota de Dificultad:** Valor entre 0 y 10 puntos.
- **Nota de Ejecución:** Valor entre 0 y 10 puntos.

#### 9.3.3 Deducciones
Registre deducciones específicas con los siguientes campos:

- **Elemento:** Nombre del elemento gimnástico.
- **Tipo de error:** Seleccione el tipo de error según el Código FIG.
- **Descripción:** Detalle del error observado.
- **Ángulo detectado:** Ángulo de desviación (si aplica).
- **Justificación:** Explicación de la deducción aplicada.

La **puntuación final** se calcula automáticamente como:

```
Puntaje Final = Nota Dificultad + Nota Ejecución - Total Deducciones
```

#### 9.3.4 Landmarks (Puntos Corporales)
Visualice los puntos corporales (landmarks) detectados en los fotogramas del video analizado, lo que permite verificar la posición del gimnasta en momentos clave.

### 9.4 Evaluaciones Pendientes

Ruta: `/juez/pendientes`

Lista de gimnastas pendientes de evaluación, filtrada por competencia y aparato.

---

## 10. Área Médica

El Área Médica agrupa todas las funcionalidades relacionadas con la salud y bienestar de los gimnastas.

### 10.1 Acceso

Ruta: `/areamedica`

### 10.2 Submódulos

| Submódulo | Ruta | Descripción |
|-----------|------|-------------|
| Fisioterapia | `/areamedica/fisioterapia` | Gestión de tratamientos fisioterapéuticos |
| Nutrición | `/areamedica/nutricion` | Evaluación y seguimiento nutricional |
| Psicología | `/areamedica/psicologia` | Evaluación y apoyo psicológico |
| Psicología - Chatbot | `/areamedica/psicologia/chatbot` | Chatbot de apoyo psicológico |
| Medicina | `/areamedica/medicina` | Consultas y evaluaciones médicas generales |
| Rehabilitación | `/areamedica/rehabilitacion` | Programas y seguimiento de rehabilitación |

---

## 11. Análisis de Movimiento y Video

Módulo avanzado que integra inteligencia artificial para el análisis de rutinas gimnásticas.

### 11.1 Análisis de Video

Ruta: `/analisis/video`

#### 11.1.1 Subir Video
1. Seleccione un archivo de video (MP4, WebM, OGG, MOV, AVI; máximo 200 MB).
2. Haga clic en **"Subir Video"**.
3. Espere a que el sistema procese el archivo.

#### 11.1.2 Análisis con IA
Una vez subido el video, puede utilizar la inteligencia artificial para analizar la rutina:

1. Haga clic en **"Analizar con IA"**.
2. El sistema enviará el video al motor de OpenAI para su evaluación.
3. La IA analizará la rutina basándose en el **Código de Puntos FIG 2025-2028**.
4. Recibirá un desglose detallado que incluye:
   - Identificación de errores
   - Deducciones estándar aplicadas:
     - **Pequeño:** 0.10 puntos
     - **Medio:** 0.30 puntos
     - **Grande:** 0.50 puntos
   - Puntuación total estimada

#### 11.1.3 Resultados de Pose
Los resultados del análisis de pose incluyen:

- **Datos de landmarks:** Coordenadas de puntos corporales clave en cada fotograma.
- **Ángulos calculados:** Ángulos articulares en posiciones específicas.
- **Comparación:** Desviación respecto a la posición ideal.

#### 11.1.4 Análisis de Ángulo
Ruta: `/analisis/angulo`

Visualización detallada de los ángulos articulares detectados durante la rutina.

#### 11.1.5 Estadísticas
Ruta: `/analisis/estadisticas`

Estadísticas agregadas de todos los videos analizados.

### 11.2 Dashboard de Análisis de Movimiento

Ruta: `/analisis/movimiento-dashboard`

Panel centralizado con acceso a todas las herramientas de análisis de movimiento.

### 11.3 Evaluaciones desde Análisis

El módulo de análisis también permite realizar evaluaciones directamente:

- **Aptitud:** `/analisis/aptitud`
- **Nutricional:** `/analisis/nutricional`
- **Respiratoria:** `/analisis/respiratoria`
- **Psicológica:** `/analisis/psicologica`
- **Fisiológica:** `/analisis/fisiologica`

---

## 12. Gestión de Aparatos

Módulo para administrar los aparatos gimnásticos del centro.

### 12.1 Listado de Aparatos

Ruta: `/aparatos`

Lista de todos los aparatos registrados, con su estado operativo y fechas de mantenimiento.

### 12.2 Acciones Disponibles

- **Crear aparato:** Registre un nuevo aparato con nombre, descripción y fechas de mantenimiento.
- **Editar aparato:** Modifique los datos del aparato.
- **Desactivar aparato:** Marque el aparato como no disponible.
- **Reactivar aparato:** Vuelva a poner el aparato en servicio.
- **Finalizar mantenimiento:** Marque el mantenimiento como completado.

### 12.3 Estados del Aparato

- **Operativo:** El aparato está disponible para uso.
- **Mantenimiento:** El aparato está en reparación o mantenimiento programado.

> **Nota:** El sistema alerta con 7 días de anticipación cuando un aparato requiere mantenimiento próximo.

---

## 13. Gestión de Competencias

### 13.1 Listado de Competencias

Ruta: `/competencias`

Catálogo de todas las competencias registradas en el sistema.

### 13.2 Acciones Disponibles

- **Crear competencia:** Registre una nueva competencia con nombre, fecha, ubicación y descripción.
- **Editar competencia:** Modifique los datos de la competencia.
- **Desactivar competencia:** Marque la competencia como inactiva.
- **Reactivar competencia:** Restaure una competencia desactivada.

### 13.3 Asignación de Gimnastas

Los gimnastas se asignan a las competencias para participar, y los jueces evalúan sus rutinas dentro del contexto de la competencia.

---

## 14. Solicitudes de Nivel

### 14.1 ¿Qué son las Solicitudes de Nivel?

Permiten a los gimnastas progresar a través de los niveles de entrenamiento:

| Nivel | Descripción |
|-------|-------------|
| 1 - Principiante | Nivel inicial |
| 2 - Intermedio | Nivel medio |
| 3 - Avanzado | Nivel avanzado |
| 4 - Elite | Nivel de alta competencia |

### 14.2 Flujo de Aprobación

```
Gimnasta
    → Envía solicitud de cambio de nivel
        → Entrenador
            → Recomienda / Aprueba / Rechaza
                → Administrador
                    → Aprueba / Rechaza
```

### 14.3 Reglas

- Solo se puede solicitar un nivel superior al actual.
- No se permite saltar niveles (ej: de Principiante a Avanzado).
- Cada solicitud puede incluir comentarios del gimnasta, entrenador y administrador.

---

## 15. Postulaciones

### 15.1 ¿Qué son las Postulaciones?

Es el proceso mediante el cual nuevos gimnastas solicitan ingresar al sistema.

### 15.2 Formulario de Postulación

Los aspirantes completan un formulario con:

- Nombres y apellidos
- Fecha de nacimiento
- Nacionalidad
- Cédula de identidad
- Correo electrónico
- Teléfono
- Género
- Dirección
- Información adicional relevante

### 15.3 Validación Automática

El sistema valida automáticamente:

- **Formato de cédula** según el país seleccionado (Venezuela, Colombia, México, Argentina, Chile, Perú, Bolivia, Ecuador, Brasil, Uruguay, España).
- **Edad, BMI e índice de masa corporal** para generar recomendaciones automáticas.

### 15.4 Proceso de Aprobación

1. El aspirante completa y envía el formulario de postulación.
2. El administrador revisa la postulación.
3. El administrador puede **aprobar** o **rechazar** la postulación.
4. Si es aprobada, el sistema crea automáticamente el usuario del gimnasta.

---

## 16. Reportes y Backups

### 16.1 Reportes

Ruta: `/admin/reportes`

#### 16.1.1 Reporte Completo

Genera un informe detallado con todos los datos del sistema: usuarios, sesiones, competencias, lesiones, etc.

#### 16.1.2 Exportar a PDF

Permite descargar el reporte en formato PDF para su distribución o impresión.

### 16.2 Backups

#### 16.2.1 Crear Backup

1. Vaya a `/admin/reportes`.
2. Haga clic en **"Crear Backup"**.
3. El sistema generará una copia de seguridad de la base de datos.
4. Recibirá una confirmación cuando el backup esté listo.

#### 16.2.2 Descargar Backup

1. Vaya a `/admin/reportes`.
2. En la lista de backups disponibles, haga clic en **"Descargar"** junto al backup deseado.
3. El archivo se descargará a su computadora.

---

## 17. Perfil de Usuario

### 17.1 Ver Perfil

Ruta: `/perfil`

Muestra la información personal del usuario:

- Nombre y apellidos
- Correo electrónico
- Nacionalidad
- Cédula de identidad
- Teléfono
- Dirección
- Fecha de nacimiento
- Género
- Rol en el sistema

### 17.2 Editar Perfil

1. Vaya a `/perfil`.
2. Haga clic en **"Editar Perfil"**.
3. Modifique los campos deseados.
4. Haga clic en **"Guardar Cambios"**.

> **Nota:** No puede cambiar su propio rol. Contacte al administrador si necesita un cambio de rol.

---

## 18. Preguntas Frecuentes

### 18.1 ¿Cómo recupero mi contraseña?

Actualmente, la recuperación de contraseña debe ser gestionada por el administrador del sistema. Contacte al administrador para restablecer su contraseña.

### 18.2 ¿Por qué no puedo crear una sesión de entrenamiento?

Posibles causas:
- Tiene una **lesión activa** registrada en el sistema.
- Ya tiene una sesión programada en el mismo horario.
- El aparato seleccionado está en **mantenimiento**.
- No hay **entrenadores disponibles** en el horario seleccionado.

### 18.3 ¿Cómo sé si un aparato está disponible?

En el formulario de creación de sesiones, solo se muestran los aparatos con estado **"Operativo"**. También puede consultar la lista completa en `/aparatos`.

### 18.4 ¿Cuánto tiempo toma procesar un video?

El tiempo de procesamiento depende del tamaño del archivo y la carga del servidor. Videos de hasta 100 MB generalmente se procesan en menos de 2 minutos.

### 18.5 ¿Cómo se calcula la puntuación final de una rutina?

```
Puntaje Final = Nota de Dificultad + Nota de Ejecución - Total de Deducciones
```

Donde las deducciones se clasifican como:
- **Pequeño error:** -0.10 pts
- **Error medio:** -0.30 pts
- **Gran error:** -0.50 pts

### 18.6 ¿Qué hago si encuentro un error en el sistema?

Reporte el error al administrador del sistema, incluyendo:
- Descripción del problema
- Pasos para reproducirlo
- Captura de pantalla (si es posible)
- Fecha y hora del incidente

### 18.7 ¿Puedo tener más de un rol en el sistema?

No, cada usuario tiene un único rol asignado. Si necesita cambiar de rol, contacte al administrador.

### 18.8 ¿Los datos de las evaluaciones médicas son confidenciales?

Sí. Solo el personal médico autorizado y el administrador del sistema tienen acceso a los datos de evaluaciones médicas. El sistema cumple con las regulaciones de protección de datos aplicables.

---

## Soporte Técnico

Para soporte técnico adicional, contacte al administrador del sistema:

- **Correo electrónico:** [correo del administrador]
- **Teléfono:** [teléfono de contacto]

---

*Documento generado el {{ date('d/m/Y') }}*
*Versión del sistema: 1.0*
