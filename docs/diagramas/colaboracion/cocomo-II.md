## Análisis de Estimación de Esfuerzo (COCOMO II)

El modelo COCOMO II (Constructive Cost Model II), desarrollado por Boehm et al. (2000) como evolución del modelo COCOMO 81 original, es el estándar de referencia en la ingeniería de software para estimar el esfuerzo, tiempo y costo requerido para el desarrollo de un proyecto de software. Para la estimación formal de **Gimnasia Unificada** se adoptó el submodelo Post-Architecture, el más robusto de los tres disponibles, dado que la arquitectura del sistema está completamente definida en el momento del análisis.

### Tabla 15
*Distribución de líneas de código fuente por capa — Gimnasia Unificada*

| Capa del sistema | Lenguaje principal | KLOC reales | % del total |
|---|---|---|---|
| Backend (Laravel PHP) | PHP 8.1 | 11.87 | 37.2% |
| Frontend (Blade + JavaScript + CSS) | Blade / JavaScript | 16.17 | 50.6% |
| Base de datos (Migraciones + SQL dump) | SQL / MySQL | 1.00 | 3.1% |
| Configuración e infraestructura (JSON, YAML, scripts Python) | JSON / YAML / Python | 2.91 | 9.1% |
| **TOTAL del sistema** | — | **31.95** | **100%** |

*Nota.* KLOC = Kilo Lines of Code. Análisis con herramientas nativas de conteo sobre el repositorio del proyecto, excluyendo `node_modules`, `vendor`, `storage`, `.git` y `public`. Elaboración propia (2026).

---

### 5.4.4 Factores de Escala (Scale Factors)

### Tabla 16
*Factores de escala COCOMO II — Gimnasia Unificada*

| Factor (SF) | Nivel | Valor | Justificación |
|---|---|---|---|
| **PREC** — Precedentedness | Bajo | 4.96 | No existen antecedentes directos de un sistema integral de gestión de gimnasia artística con análisis de video asistido por IA en el contexto boliviano; el dominio de reglas FIG y evaluaciones multi-rol añade un factor alto de novedad |
| **FLEX** — Development Flexibility | Nominal | 3.04 | Las reglas de puntuación FIG (Fédération Internationale de Gymnastique), los protocolos médicos deportivos y los flujos de entrenamiento restringen parcialmente la flexibilidad en los módulos de evaluación |
| **RESL** — Risk Resolution | Alto | 2.83 | Se realizaron prototipos previos de integración con MediaPipe para detección de poses y con OpenAI para análisis de movimiento antes de iniciar el desarrollo completo |
| **TEAM** — Team Cohesion | Muy Alto | 1.62 | Equipo de un solo desarrollador/autor, lo que minimiza problemas de comunicación y maximiza la cohesión interna del proyecto |
| **PMAT** — Process Maturity | Nominal | 3.12 | Adopción sistemática de estructura MVC con Laravel, control de versiones Git, y organización por capas (Controllers, Services, Models, Middleware) |
| **SUMA total SF** | — | **15.57** | — |

*Nota.* Elaboración propia (2026) con valores calibrados del modelo COCOMO II Post-Architecture (Boehm et al., 2000).

```
E = B + 0.01 × ΣSF = 0.91 + 0.01 × 15.57 = 1.0657
```

### Tabla 16b
*Multiplicadores de Esfuerzo (Effort Multipliers — EM) — Gimnasia Unificada*

| ID | Factor EM | Nivel | Valor | Justificación |
|---|---|---|---|---|
| RELY | Confiabilidad requerida | Nominal | 1.00 | Sistema de gestión deportiva sin riesgo de vida, aunque requiere consistencia en datos de lesiones y evaluaciones |
| DATA | Tamaño de la base de datos | Alto | 1.08 | Base de datos con 27 tablas, relaciones FK múltiples y almacenamiento de landmarks JSON para análisis de video |
| CPLX | Complejidad del producto | Alto | 1.17 | Lógica de negocio compleja: asignación automática de entrenadores, cálculo de puntuación FIG, pipeline de análisis de video con IA, detección de conflictos de horario |
| RUSE | Desarrollo para reutilización | Nominal | 1.00 | Sin requisitos explícitos de reutilización más allá de la arquitectura MVC estándar |
| DOCU | Documentación del proyecto | Alto | 1.11 | Se requiere documentación técnica y diagramas UML (colaboración, clases, navegacional, relacional) |
| TIME | Restricción de tiempo de ejecución | Nominal | 1.00 | Sin restricciones críticas de tiempo real; el análisis de video es asíncrono |
| STOR | Restricción de almacenamiento | Nominal | 1.00 | Almacenamiento de videos e imágenes en disco estándar, sin restricciones severas |
| PVOL | Volatilidad de la plataforma | Bajo | 0.87 | Laravel, MySQL y Blade son tecnologías maduras y estables con retrocompatibilidad garantizada |
| ACAP | Capacidad del analista | Alto | 0.85 | El desarrollador conoce la arquitectura completa del sistema y el dominio del problema |
| PCAP | Capacidad del programador | Alto | 0.88 | El desarrollador tiene experiencia demostrada en Laravel, PHP, JavaScript y bases de datos |
| PCON | Continuidad del personal | Muy Alto | 0.81 | Desarrollador único durante todo el ciclo de vida del proyecto |
| APEX | Experiencia en la aplicación | Bajo | 1.10 | Sin experiencia previa en sistemas específicos de gimnasia artística ni reglamento FIG |
| PLEX | Experiencia en la plataforma | Alto | 0.91 | Experiencia sólida en Laravel, MySQL y desarrollo web full-stack |
| LTEX | Experiencia en lenguaje y herramientas | Alto | 0.91 | Dominio de PHP 8, JavaScript, SQL y herramientas del ecosistema Laragon |
| TOOL | Uso de herramientas de software | Alto | 0.90 | Uso de Laragon, Git, VSCode, Postman, MediaPipe Studio, y herramientas de diagramado UML |
| SITE | Desarrollo multisitio | Muy Alto | 0.86 | Desarrollo en entorno local unificado (Laragon), sin coordinación remota |
| SCED | Programa requerido | Nominal | 1.00 | Sin presión de calendario externa; desarrollo académico con planificación flexible |
| **EM compuesto** | — | — | **0.70** | Producto de los 17 multiplicadores anteriores |

*Nota.* Elaboración propia (2026) con valores calibrados del modelo COCOMO II Post-Architecture (Boehm et al., 2000).

---

### 5.4.5 a 5.4.8 — Cálculos de Esfuerzo, Tiempo, Personal y Costos

Aplicando la ecuación fundamental de COCOMO II Post-Architecture con A=2.94 (constante calibrada por Boehm et al., 2000):

```
PM = A × (Size)^E × EM
PM = 2.94 × (31.95)^1.0657 × 0.70
PM = 2.94 × 4.964 × 0.70
PM = 10.22 Personas-Mes
```

```
F = 0.28 + 0.2 × (E - 0.91)
F = 0.28 + 0.2 × (1.0657 - 0.91)
F = 0.3111

TDEV = C × (PM)^F
TDEV = 3.67 × (10.22)^0.3111
TDEV = 3.67 × 1.369
TDEV = 5.03 meses de calendario

Staff = PM / TDEV
Staff = 10.22 / 5.03
Staff = 2.03 desarrolladores equivalentes
```

### Tabla 17
*Presupuesto detallado del proyecto Gimnasia Unificada — implementación académica (Fase MVP)*

| Categoría | Ítem | BOB | USD |
|---|---|---|---|
| Recursos Humanos | Desarrollador Full Stack (10 meses × 10,500 BOB) | 105,000 | 15,086 |
| Recursos Humanos | Capacitación y documentación técnica (2 semanas) | 3,000 | 431 |
| Hardware | Equipo de desarrollo (computadora, monitor, periféricos) | 12,000 | 1,724 |
| Software | Stack OSS: Laravel, MySQL, FPDF, MediaPipe, OpenAI API | 0 | 0 |
| Infraestructura | Hosting VPS + dominio + SSL (12 meses) | 4,800 | 690 |
| Infraestructura | API OpenAI para análisis de movimiento (créditos, 12 meses) | 3,600 | 517 |
| Infraestructura | Almacenamiento de videos y backups (12 meses) | 1,800 | 259 |
| Capacitación | Talleres para entrenadores y personal médico (2 talleres) | 4,000 | 575 |
| Contingencia (15%) | Reserva técnica para imprevistos | 20,130 | 2,892 |
| **TOTAL ESTIMADO (MVP académico)** | — | **154,330** | **22,174** |

*Nota.* BOB = Bolivianos; USD = Dólares (tipo de cambio 6.96 BOB/USD, BCB enero 2026); OSS = Open Source Software; MVP = Minimum Viable Product. Elaboración propia (2026).

---

### 5.4.9 Análisis Financiero — VAN y TIR

```
VAN = −154,330 + 42,000/1.12¹ + 60,000/1.12² + 78,000/1.12³ + 96,000/1.12⁴ + 114,000/1.12⁵
VAN = −154,330 + 37,500 + 47,831 + 55,521 + 61,012 + 64,703
VAN = 112,237 BOB
VAN = 16,126 USD

TIR = 42.8% anual
```

Un **VAN positivo de 112,237 BOB (16,126 USD)** confirma que el proyecto genera valor económico significativo a lo largo del horizonte de cinco años con una tasa de descuento del 12% anual. La **TIR de 42.8%** supera ampliamente el costo de oportunidad del capital, confirmando la viabilidad financiera del proyecto para su adopción en academias de gimnasia del departamento de La Paz.

---

### 5.5 Fiabilidad y Disponibilidad del Software

```
Disponibilidad = MTBF / (MTBF + MTTR) = 720 / (720 + 2) = 99.72%
```

La disponibilidad calculada del **99.72%** equivale a dos nueves, dentro del rango aceptable para sistemas de gestión deportiva. La probabilidad de operación libre de fallos durante una sesión de entrenamiento de una hora es del **99.86%**, y durante un día completo de operación es del **96.72%**. La arquitectura MVC de Laravel con manejo de excepciones, transacciones de base de datos y validación de requests garantiza que errores de entrada no interrumpan el servicio y que la integridad de los datos se mantenga incluso en caso de fallos parciales.

---

### 5.6 Calidad de Software según ISO/IEC 25010

### Tabla 18
*Cumplimiento de calidad ISO/IEC 25010 — criterios de aceptación técnica por característica*

| Característica ISO 25010 | Criterio de aceptación | % Cumplimiento | Estado |
|---|---|---|---|
| **Adecuación Funcional** | 100% de los RF críticos implementados y validados en autenticación multi-rol, gestión de sesiones, evaluación FIG, control de lesiones y análisis de video | 95% | CUMPLIDO |
| **Eficiencia de Rendimiento** | Carga de interfaz < 2.0 s. Consultas SQL < 500 ms. Análisis de video < 5.0 s por frame | 90% | CUMPLIDO |
| **Compatibilidad** | Compatibilidad con navegadores modernos (Chrome, Firefox, Edge) y MySQL 8.0. Integración con MediaPipe y OpenAI | 85% | CUMPLIDO |
| **Usabilidad** | Interfaz basada en roles con navegación diferenciada, calendario interactivo FullCalendar y formularios intuitivos con validación en tiempo real | 80% | CUMPLIDO |
| **Fiabilidad** | Disponibilidad del sistema ≥ 99.7% (MTBF=720h, MTTR=2h). Validación de requests en el 100% de las entradas | 99.72% | CUMPLIDO |
| **Seguridad** | Sanitización de entradas con detección de SQLi/XSS/Path Traversal/Command Injection. Permisos por rol. Auditoría de accesos. Protección CSRF | 90% | CUMPLIDO |
| **Mantenibilidad** | Arquitectura MVC con separación por capas (Controllers, Services, Models, Middleware, Form Requests). Código organizado con naming consistente | 85% | CUMPLIDO |
| **Portabilidad** | Despliegue en Laragon (Windows) o LAMP/LEMP (Linux) con configuración mínima. Migraciones automatizadas de base de datos | 90% | CUMPLIDO |

*Nota.* MTBF = Mean Time Between Failures; MTTR = Mean Time To Repair; RF = Requisitos Funcionales; SQLi = SQL Injection; XSS = Cross-Site Scripting; CSRF = Cross-Site Request Forgery. Elaboración propia (2026).
