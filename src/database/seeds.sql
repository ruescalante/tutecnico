-- =============================================================
-- SEEDS - Datos de prueba enriquecidos
-- Ejecutar DESPUÉS de migrations.sql
-- Contraseñas: todas son "password" (hash bcrypt)
-- =============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- Limpiar datos previos para re-seed idempotente
TRUNCATE TABLE `notificaciones`;
TRUNCATE TABLE `calificaciones`;
TRUNCATE TABLE `mensajes`;
TRUNCATE TABLE `cotizaciones`;
TRUNCATE TABLE `solicitudes`;
TRUNCATE TABLE `foto_trabajos`;
TRUNCATE TABLE `tecnico_categorias`;
TRUNCATE TABLE `tecnico_perfiles`;
TRUNCATE TABLE `users`;
TRUNCATE TABLE `categorias`;

-- -------------------------------------------------------------
-- Categorías de servicios
-- -------------------------------------------------------------
INSERT INTO `categorias` (`nombre`, `descripcion`) VALUES
('Electricidad',         'Instalaciones eléctricas, reparación de circuitos y tomacorrientes'),
('Plomería',             'Tuberías, grifos, desagües y sistemas de agua'),
('Carpintería',          'Muebles, puertas, ventanas y trabajos en madera'),
('Pintura',              'Pintura interior y exterior de paredes y superficies'),
('Aires Acondicionados', 'Instalación, mantenimiento y reparación de A/C'),
('Cerrajería',           'Apertura de cerraduras, cambio de llaves y chapas'),
('Jardinería',           'Poda, mantenimiento de jardines y áreas verdes'),
('Limpieza',             'Limpieza profunda de hogares y oficinas');

-- -------------------------------------------------------------
-- Usuarios de prueba
-- Contraseña de todos: "password"
-- -------------------------------------------------------------

-- Admin (id=1)
INSERT INTO `users` (`nombre`, `correo`, `contrasena`, `telefono`, `rol`, `activo`, `foto_perfil`) VALUES
('Admin Sistema', 'admin@servicios.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '22000000', 'admin', 1,
 '/uploads/fotos/user_1_1748000000.jpg');

-- Clientes (id=2..6)
INSERT INTO `users` (`nombre`, `correo`, `contrasena`, `telefono`, `rol`, `activo`, `foto_perfil`, `direccion`, `ciudad`, `pais`) VALUES
('Carlos Ramírez', 'carlos@mail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '77112233', 'cliente', 1,
 '/uploads/fotos/user_2_1748000100.jpg',
 'Col. Escalón, Calle La Mascota #45', 'San Salvador', 'El Salvador'),

('María González', 'maria@mail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '77445566', 'cliente', 1,
 '/uploads/fotos/user_3_1748000200.jpg',
 'Av. Roosevelt #120, Col. Flor Blanca', 'San Salvador', 'El Salvador'),

('Luis Herrera', 'luis@mail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '77557788', 'cliente', 1,
 '/uploads/fotos/user_4_1748000300.jpg',
 'Col. América, Pasaje 3 #22', 'Apopa', 'El Salvador'),

('Sofía Torres', 'sofia@mail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '77119900', 'cliente', 1,
 '/uploads/fotos/user_5_1748000400.jpg',
 'Res. La Cima, Calle Los Pinos #8', 'Santa Tecla', 'El Salvador'),

('Diego Morales', 'diego@mail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '77221100', 'cliente', 1,
 '/uploads/fotos/user_6_1748000500.jpg',
 'Col. Miramonte, Calle Principal #78', 'San Salvador', 'El Salvador');

-- Técnicos (id=7..10)
INSERT INTO `users` (`nombre`, `correo`, `contrasena`, `telefono`, `rol`, `activo`, `foto_perfil`, `direccion`, `ciudad`, `pais`) VALUES
('Roberto López', 'roberto@mail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '78001122', 'tecnico', 1,
 '/uploads/fotos/user_7_1748000600.jpg',
 'Col. San Benito, Calle El Progreso #12', 'San Salvador', 'El Salvador'),

('Ana Martínez', 'ana@mail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '78003344', 'tecnico', 1,
 '/uploads/fotos/user_8_1748000700.jpg',
 'Urb. Santa Rosa, Av. Independencia #55', 'Santa Ana', 'El Salvador'),

('Jorge Mendoza', 'jorge@mail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '78005566', 'tecnico', 1,
 '/uploads/fotos/user_9_1748000800.jpg',
 'Col. Jardines de Guadalupe, Calle 5 #19', 'Antiguo Cuscatlán', 'El Salvador'),

('Carmen Vásquez', 'carmen@mail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '78007788', 'tecnico', 1,
 '/uploads/fotos/user_10_1748000900.jpg',
 'Col. Zacamil, Pasaje Los Almendros #3', 'Mejicanos', 'El Salvador');

-- Cliente extra (id=11)
INSERT INTO `users` (`nombre`, `correo`, `contrasena`, `telefono`, `rol`, `activo`, `foto_perfil`, `direccion`, `ciudad`, `pais`) VALUES
('Valentina Reyes', 'vale@mail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '77332200', 'cliente', 1,
 '/uploads/fotos/user_11_1748001000.jpg',
 'Urb. San Francisco, Pasaje 2 #14', 'Soyapango', 'El Salvador');

-- Usuario de prueba: Ruben (id=12) — cliente
INSERT INTO `users` (`nombre`, `correo`, `contrasena`, `telefono`, `rol`, `activo`, `foto_perfil`, `direccion`, `ciudad`, `pais`) VALUES
('Ruben Escalante', 'ruben@ruben.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '79001234', 'cliente', 1,
 '/uploads/fotos/user_12_1748001100.jpg',
 'Col. Médica, Av. Dr. Emilio Álvarez #34', 'San Salvador', 'El Salvador');

-- Usuario de prueba: Jorge técnico (id=13) — técnico
INSERT INTO `users` (`nombre`, `correo`, `contrasena`, `telefono`, `rol`, `activo`, `foto_perfil`, `direccion`, `ciudad`, `pais`) VALUES
('Jorge García', 'jorge@jorge.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '79005678', 'tecnico', 1,
 '/uploads/fotos/user_13_1748001200.jpg',
 'Col. Centroamérica, Calle Norte #67', 'San Salvador', 'El Salvador');

-- -------------------------------------------------------------
-- Perfiles de técnicos
-- Roberto(7)=1, Ana(8)=2, Jorge M.(9)=3, Carmen(10)=4, Luis(4)=5, Jorge G.(13)=6
-- -------------------------------------------------------------
INSERT INTO `tecnico_perfiles`
     (`user_id`, `zona_cobertura`, `descripcion`, `disponibilidad`, `estado`, `cancelaciones`, `comentario_admin`) VALUES

(7, 'San Salvador, Soyapango, Ilopango, Apopa',
 'Electricista certificado con 12 años de experiencia en instalaciones residenciales y comerciales. Especializado en diseño e instalación de tableros eléctricos, circuitos de alta tensión, cableado estructurado y sistemas de iluminación LED. Trabajo con normas de seguridad NEC y RETIE. Ofrezco garantía de 6 meses en todas mis instalaciones.',
 1, 'activo', 0, 'Documentos verificados. Licencia profesional vigente.'),

(8, 'Santa Ana, Metapán, Chalchuapa, Ahuachapán',
 'Plomera certificada con 9 años de experiencia. Especialista en instalación y reparación de sistemas de agua fría y caliente, tubería PVC y cobre, sistemas de desagüe y alcantarillado, grifería de todas las marcas y calentadores de agua. Atiendo emergencias 24/7 para fugas urgentes. Precios justos y materiales de primera calidad.',
 1, 'activo', 0, 'Certificación técnica validada. Excelentes referencias.'),

(9, 'Santa Tecla, Antiguo Cuscatlán, San Salvador, Nueva San Salvador',
 'Carpintero con 10 años de experiencia en fabricación de muebles a medida, instalación de closets empotrados, puertas, ventanas y trabajos de acabados en madera fina. Trabajo con cedro, pino, MDF y melanina. Diseño personalizado según las necesidades del cliente. Taller propio con maquinaria de precisión.',
 1, 'activo', 0, 'Taller propio verificado. Portafolio extenso aprobado.'),

(10, 'Mejicanos, Soyapango, San Martín, Ilopango',
 'Pintora profesional con 7 años de experiencia en proyectos residenciales y comerciales. Especializada en pintura interior y exterior, estuco veneciano, texturas decorativas, pintura epóxica para pisos y grafitis artísticos. Uso materiales de alta duración: Sherwin Williams, Pintuco y Glidden. Incluyo limpieza del área al finalizar.',
 1, 'activo', 0, 'Portafolio de proyectos verificado. Muy recomendada.'),

(4, 'Apopa, Nejapa, Quezaltepeque',
 'Técnico general con 5 años de experiencia en mantenimiento preventivo y correctivo del hogar. Reparaciones menores de electricidad, plomería y carpintería básica. Disponible fines de semana.',
 1, 'pendiente', 0, 'Pendiente de revisión de documentos de identidad y certificación.'),

(13, 'San Salvador, Mejicanos, Cuscatancingo, Ciudad Delgado',
 'Técnico electricista y climatista con 6 años de experiencia. Especializado en instalación y mantenimiento de sistemas de aire acondicionado (split, central y mini-split) de las marcas LG, Samsung, Carrier y Daikin. También realizo instalaciones eléctricas residenciales, cambio de tableros y revisión de circuitos. Certificado por SENACYT. Puntual y con garantía escrita en cada trabajo.',
 1, 'activo', 0, 'Certificación SENACYT verificada. Recomendado para proyectos de clima.');

-- -------------------------------------------------------------
-- Categorías por técnico
-- Roberto(7):    Electricidad, Aires Acondicionados
-- Ana(8):        Plomería, Limpieza
-- Jorge M.(9):   Carpintería, Cerrajería
-- Carmen(10):    Pintura, Jardinería
-- Jorge G.(13):  Electricidad, Aires Acondicionados
-- -------------------------------------------------------------
INSERT INTO `tecnico_categorias` (`user_id`, `id_categoria`) VALUES
(7,  1),  -- Roberto → Electricidad
(7,  5),  -- Roberto → Aires Acondicionados
(8,  2),  -- Ana     → Plomería
(8,  8),  -- Ana     → Limpieza
(9,  3),  -- Jorge M → Carpintería
(9,  6),  -- Jorge M → Cerrajería
(10, 4),  -- Carmen  → Pintura
(10, 7),  -- Carmen  → Jardinería
(13, 1),  -- Jorge G → Electricidad
(13, 5);  -- Jorge G → Aires Acondicionados

-- -------------------------------------------------------------
-- Solicitudes
-- id=1   Carlos(2)→Roberto(7)    aceptada  (con cotización y mensajes activos)
-- id=2   Carlos(2)→Roberto(7)    completada
-- id=3   María(3)→Roberto(7)     completada
-- id=4   María(3)→Ana(8)         completada
-- id=5   Sofía(5)→Roberto(7)     completada
-- id=6   Diego(6)→Ana(8)         completada
-- id=7   Valentina(11)→Roberto(7) completada
-- id=8   Carlos(2)→Ana(8)        completada
-- id=9   Sofía(5)→Ana(8)         completada
-- id=10  Diego(6)→Jorge M(9)     completada
-- id=11  Carlos(2)→Jorge M(9)    completada
-- id=12  María(3)→Carmen(10)     completada
-- id=13  Valentina(11)→Carmen(10) completada
-- id=14  Ruben(12)→Roberto(7)    completada
-- id=15  Diego(6)→Jorge G(13)    completada
-- id=16  Diego(6)→Roberto(7)     pendiente
-- id=17  Sofía(5)→Jorge M(9)     pendiente
-- id=18  Ruben(12)→Jorge G(13)   aceptada  (con cotización y mensajes)
-- -------------------------------------------------------------

INSERT INTO `solicitudes`
    (`id_cliente`, `id_tecnico`, `titulo`, `descripcion`, `direccion`, `estado`) VALUES
(2, 7,
 'Reparar tomacorriente quemado en sala',
 'El tomacorriente de la sala dejó de funcionar después de un corto circuito. Hay olor a quemado y la tapa está ennegrecida. Necesito revisión urgente del circuito completo para descartar daños mayores.',
 'Col. Escalón, Calle La Mascota #45, San Salvador',
 'aceptada');

INSERT INTO `solicitudes`
    (`id_cliente`, `id_tecnico`, `titulo`, `descripcion`, `direccion`, `estado`, `fecha_completado`) VALUES
(2, 7,  'Instalación de tablero eléctrico de 8 circuitos',
 'Necesito instalar un tablero de distribución nuevo para separar los circuitos de la cocina, lavandería y cuartos. La casa tiene 15 años y el tablero actual es muy antiguo y no tiene breakers individuales.',
 'Col. Escalón, Calle La Mascota #45, San Salvador',   'completada', DATE_SUB(NOW(), INTERVAL 30 DAY)),

(3, 7,  'Agregar 4 tomacorrientes en oficina en casa',
 'Estoy acondicionando una habitación como oficina y necesito 4 tomacorrientes adicionales: 2 dobles cerca del escritorio y 2 triples para equipos de cómputo. Deben tener protección contra sobrecarga.',
 'Av. Roosevelt #120, Col. Flor Blanca, San Salvador', 'completada', DATE_SUB(NOW(), INTERVAL 45 DAY)),

(3, 8,  'Tubería rota bajo el lavamanos del baño principal',
 'Hay una fuga en la tubería de agua caliente debajo del lavamanos. El agua se está filtrando hacia el piso y ya hay humedad en la pared. Necesito reparación urgente y verificar si hay más daños.',
 'Av. Roosevelt #120, Col. Flor Blanca, San Salvador', 'completada', DATE_SUB(NOW(), INTERVAL 60 DAY)),

(5, 7,  'Revisión y diagnóstico del sistema eléctrico completo',
 'Voy a comprar esta casa y quiero una revisión completa del sistema eléctrico antes de cerrar el trato. Necesito saber el estado real de cableado, tablero, tomas, interruptores y si cumple normas actuales.',
 'Res. La Cima, Calle Los Pinos #8, Santa Tecla',    'completada', DATE_SUB(NOW(), INTERVAL 20 DAY)),

(6, 8,  'Instalar ducha eléctrica tipo lluvia en baño master',
 'Quiero cambiar la ducha de cabezal simple por una ducha eléctrica tipo lluvia de 5500W. El baño tiene 220V disponible. Necesito instalación completa incluyendo el cableado dedicado para la ducha.',
 'Col. Miramonte, Calle Principal #78, San Salvador', 'completada', DATE_SUB(NOW(), INTERVAL 15 DAY)),

(11, 7, 'Reemplazo de cableado antiguo en 3 dormitorios',
 'La casa tiene más de 25 años y el cableado de los dormitorios es muy antiguo (cable tela). Necesito reemplazarlo todo por cable THW calibre 12 con tubo conduit. Son 3 cuartos de aprox. 15m² cada uno.',
 'Urb. San Francisco, Pasaje 2 #14, Soyapango',      'completada', DATE_SUB(NOW(), INTERVAL 10 DAY)),

(2, 8,  'Reparar grifo de cocina que gotea constantemente',
 'El grifo de la cocina gotea sin parar aunque esté bien cerrado. Ya probé apretando más pero sigue igual. Necesito revisión y si es necesario reemplazo completo del grifo. Prefiero marca URREA o similar.',
 'Col. Escalón, Calle La Mascota #45, San Salvador', 'completada', DATE_SUB(NOW(), INTERVAL 5 DAY)),

(5, 8,  'Instalar lavadero con desagüe en patio trasero',
 'Quiero instalar un lavadero de concreto con desagüe en el patio trasero. Hay una salida de agua fría cerca. Necesito la instalación completa: toma de agua, lavadero, desagüe y conexión al sistema existente.',
 'Res. La Cima, Calle Los Pinos #8, Santa Tecla',    'completada', DATE_SUB(NOW(), INTERVAL 35 DAY)),

(6, 9,  'Fabricar closet empotrado a medida para habitación principal',
 'La habitación principal tiene un nicho de 3.20m de ancho x 2.40m de alto x 0.60m de fondo. Quiero un closet empotrado completo con cajones, colgadores, estantes para zapatos y espejo interior. Madera MDF con acabado en melanina blanca.',
 'Col. Miramonte, Calle Principal #78, San Salvador', 'completada', DATE_SUB(NOW(), INTERVAL 25 DAY)),

(2, 9,  'Reparar 2 puertas de madera con marcos descuadrados',
 'Dos puertas interiores no cierran bien: una tiene el marco torcido y la otra tiene bisagras viejas que hacen que la puerta roce el piso. Necesito reparación completa y que queden funcionando perfectamente.',
 'Col. Escalón, Calle La Mascota #45, San Salvador', 'completada', DATE_SUB(NOW(), INTERVAL 40 DAY)),

(3, 10, 'Pintar sala, comedor y pasillo completo',
 'Quiero pintar sala, comedor y pasillo con colores modernos. Son aprox. 45m² en total. Las paredes tienen algunas grietas menores que necesitan masilla. Colores a definir: pared principal en gris perla y paredes laterales en blanco hueso.',
 'Av. Roosevelt #120, Col. Flor Blanca, San Salvador', 'completada', DATE_SUB(NOW(), INTERVAL 50 DAY)),

(11, 10,'Pintar fachada exterior completa de la casa',
 'La fachada exterior tiene aprox. 80m² entre paredes, portón y barda. La pintura actual está descascarada y hay manchas de humedad. Necesito limpieza de superficies, sellador, y dos manos de pintura látex exterior. Color: blanco con detalles en terracota.',
 'Urb. San Francisco, Pasaje 2 #14, Soyapango',      'completada', DATE_SUB(NOW(), INTERVAL 18 DAY)),

-- id=14: Ruben → Roberto (completada)
(12, 7, 'Instalación de sistema de iluminación LED en toda la casa',
 'Quiero cambiar toda la iluminación de la casa a LED empotrado tipo downlight. Son 4 habitaciones, sala, comedor, cocina, 2 baños y pasillo. En total aprox. 24 puntos de luz. Necesito que queden todos con dimmer y en 3 zonas independientes.',
 'Col. Médica, Av. Dr. Emilio Álvarez #34, San Salvador', 'completada', DATE_SUB(NOW(), INTERVAL 12 DAY)),

-- id=15: Diego → Jorge García (completada)
(6, 13, 'Instalar sistema de aires split en 2 habitaciones',
 'Quiero instalar 2 aires acondicionados tipo split de 12000 BTU en las habitaciones principales. Ya compré los equipos LG Dual Inverter. Necesito instalación completa con cableado 220V dedicado, soporte mural y vaciado de gas.',
 'Col. Miramonte, Calle Principal #78, San Salvador', 'completada', DATE_SUB(NOW(), INTERVAL 8 DAY));

-- Solicitudes sin completar
INSERT INTO `solicitudes`
    (`id_cliente`, `id_tecnico`, `titulo`, `descripcion`, `direccion`, `estado`) VALUES
-- id=16: Diego → Roberto (pendiente)
(6, 7,  'Cambiar toda la iluminación a focos LED regulables',
 'El comedor y la sala tienen focos incandescentes muy viejos. Quiero cambiarlos todos a LED regulables con control remoto. Son 8 puntos de luz en total, todos en el mismo circuito. También quiero instalar un switch dimmer por zona.',
 'Col. Miramonte, Calle Principal #78, San Salvador',  'pendiente'),

-- id=17: Sofía → Jorge M (pendiente)
(5, 9,  'Mueble de TV empotrado con estantes para sala',
 'Quiero un mueble de TV empotrado en la pared de la sala. La pared mide 3.80m de ancho. El mueble debe tener: hueco central para TV de 65", estantes a ambos lados, cajonera abajo y espacio para equipo de sonido. Material: MDF laqueado en blanco.',
 'Res. La Cima, Calle Los Pinos #8, Santa Tecla', 'pendiente'),

-- id=18: Ruben → Jorge García (aceptada, con mensajes y cotización)
(12, 13,'Instalar aire acondicionado en oficina y revisar cableado',
 'Tengo una oficina en casa de aprox. 20m². Quiero instalar un split de 18000 BTU (ya lo tengo, marca Samsung WindFree). Además necesito que revisen el tablero eléctrico porque a veces se cae el breaker cuando uso muchos aparatos al mismo tiempo. Si hay que agregar un circuito dedicado para el A/C, mejor.',
 'Col. Médica, Av. Dr. Emilio Álvarez #34, San Salvador', 'aceptada');

-- -------------------------------------------------------------
-- Cotizaciones
-- -------------------------------------------------------------
-- Solicitud id=1 (Carlos → Roberto, aceptada)
INSERT INTO `cotizaciones` (`id_solicitud`, `precio_estimado`, `descripcion`, `estado`) VALUES
(1, 45.00,
 'Diagnóstico completo del circuito de la sala, reemplazo de tomacorriente doble GFCI con protección, reposición de cableado dañado en tramo de 2m y prueba de funcionamiento. Incluye materiales.',
 'aceptada');

-- Solicitud id=18 (Ruben → Jorge García, aceptada)
INSERT INTO `cotizaciones` (`id_solicitud`, `precio_estimado`, `descripcion`, `estado`) VALUES
(18, 280.00,
 'Instalación completa de split Samsung WindFree 18000 BTU: montaje de unidad interior y exterior, canalización de refrigerante, instalación de cableado 220V dedicado desde el tablero (20A), adición de breaker y prueba de funcionamiento. Se incluye inspección del tablero eléctrico y diagnóstico de caída de breaker. Garantía de instalación: 12 meses.',
 'aceptada');

-- -------------------------------------------------------------
-- Mensajes
-- -------------------------------------------------------------
-- Solicitud id=1: Carlos (2) ↔ Roberto (7)
INSERT INTO `mensajes` (`id_solicitud`, `remitente_tipo`, `remitente_id`, `contenido`) VALUES
(1, 'cliente', 2,  'Buenos días Roberto, ¿a qué hora podría venir a revisar el tomacorriente?'),
(1, 'tecnico', 7,  'Hola Carlos, buenas tardes. Puedo estar el martes a las 9am. ¿Le parece?'),
(1, 'cliente', 2,  'Perfecto, el martes a las 9 está bien. La dirección ya la tiene en la solicitud.'),
(1, 'tecnico', 7,  'Anotado. Llevaré el multímetro y los materiales necesarios. Si hay daño en el cableado lo detecto en el mismo diagnóstico.'),
(1, 'cliente', 2,  'Excelente. Le dejo abierta la reja. Cualquier cosa me avisa por acá.');

-- Solicitud id=18: Ruben (12) ↔ Jorge García (13)
INSERT INTO `mensajes` (`id_solicitud`, `remitente_tipo`, `remitente_id`, `contenido`) VALUES
(18, 'cliente', 12, 'Hola Jorge, gracias por aceptar la solicitud. El split ya lo tengo aquí en casa, es el Samsung WindFree de 18000 BTU. ¿Cuándo podría venir?'),
(18, 'tecnico', 13, 'Hola Ruben, con mucho gusto. Puedo ir el jueves en la tarde, como a las 3pm. ¿Está disponible?'),
(18, 'cliente', 12, 'El jueves está perfecto. ¿Trae usted los soportes y el refrigerante o los necesito comprar?'),
(18, 'tecnico', 13, 'Yo traigo todo el material: soportes, tornillería, canaleta para los tubos, y el gas refrigerante R-410A. Solo necesito que tenga acceso a la pared exterior para la unidad condensadora.'),
(18, 'cliente', 12, 'Perfecto, hay acceso sin problema. Y sobre el tablero, ¿puede revisarlo el mismo día?'),
(18, 'tecnico', 13, 'Sí, lo reviso primero antes de instalar el A/C. Si necesitamos agregar un circuito dedicado lo hago en el mismo servicio, ya está incluido en la cotización.'),
(18, 'cliente', 12, 'Genial, entonces quedamos para el jueves a las 3pm. Muchas gracias.');

-- -------------------------------------------------------------
-- Calificaciones
-- Una por cada solicitud completada (id 2..15)
-- -------------------------------------------------------------
INSERT INTO `calificaciones` (`id_solicitud`, `puntuacion`, `comentario`) VALUES
(2,  5, 'Roberto instaló el tablero perfectamente. Explicó todo el proceso, dejó el área limpia y me dio un informe del estado de los circuitos. Muy profesional y ordenado. Sin duda lo vuelvo a contratar.'),
(3,  5, 'Excelente servicio. Llegó puntual, trabajó rápido y los tomacorrientes quedaron perfectos con su respectiva placa y sin cables a la vista. Muy recomendado para cualquier trabajo eléctrico.'),
(4,  4, 'Ana hizo un buen trabajo con las tuberías. La fuga quedó completamente reparada y revisó el resto de las tuberías del baño sin cargo extra. Muy responsable y profesional.'),
(5,  5, 'Hizo una revisión muy completa y encontró 3 problemas que no sabíamos que existían. Me dio un reporte detallado y recomendaciones claras. Gracias a él pude negociar el precio de la casa.'),
(6,  5, 'La ducha quedó instalada perfectamente, incluyendo el cableado dedicado. Ana es muy detallista y se aseguró de que todo funcionara correctamente antes de irse. Muy profesional.'),
(7,  5, 'Roberto reemplazó todo el cableado antiguo en tiempo récord. La diferencia es notable: antes los focos parpadeaban y ahora todo funciona perfectamente. Trabajo impecable y garantía incluida.'),
(8,  4, 'El grifo quedó como nuevo. Ana llegó a la hora acordada, diagnosticó el problema rápido y lo resolvió en menos de una hora. Precio justo y buen trato. La recomiendo sin dudar.'),
(9,  5, 'Ana instaló el lavadero exactamente como lo pedí, bien nivelado, con el desagüe perfectamente integrado al sistema existente. Quedó resistente y con buen acabado. Excelente trabajo.'),
(10, 5, 'Jorge hizo el closet de nuestros sueños. El nivel de detalle en los acabados es increíble: cajones que se deslizan suave, estantes bien distribuidos y el espejo interior le dio otro nivel. 100% recomendado.'),
(11, 4, 'Reparó las dos puertas rápidamente y con muy buen acabado. Una quedó completamente alineada y la otra con bisagras nuevas que no hacen ruido. Buen precio y trato amable. Volvería a contratarlo.'),
(12, 5, 'Carmen transformó por completo la sala y el comedor. Los colores quedaron exactamente como los pedí, las grietas desaparecieron completamente y el acabado es muy parejo. Dejó todo limpio al terminar.'),
(13, 4, 'La fachada quedó como nueva. Carmen es muy cuidadosa protegiendo plantas y pisos antes de pintar, y el color quedó uniforme en toda la fachada. Muy satisfecha con el resultado.'),
(14, 5, 'Roberto hizo un trabajo excepcional con la iluminación LED. Los dimmers funcionan perfectamente, las zonas independientes quedaron muy bien planeadas y la diferencia en el consumo eléctrico ya se nota. Casa completamente transformada.'),
(15, 5, 'Jorge instaló los dos aires en tiempo récord y sin dejar rastro de suciedad. Los equipos funcionan perfectamente en modo inverter y el cableado quedó muy ordenado con canaleta. Muy profesional y puntual.');

-- -------------------------------------------------------------
-- Fotos de trabajos (portfolio de técnicos)
-- Roberto(1), Ana(2), Jorge M.(3), Carmen(4), Luis(5), Jorge G.(6)
-- -------------------------------------------------------------

-- Roberto (perfil_id=1) — 6 fotos: electricidad y aires
INSERT INTO `foto_trabajos` (`tecnico_perfil_id`, `url`, `descripcion`) VALUES
(1, '/uploads/trabajos/trabajo_1_1748002001_0.jpg',  'Instalación de tablero eléctrico de distribución de 12 circuitos'),
(1, '/uploads/trabajos/trabajo_1_1748002001_1.jpg',  'Canalización y tendido de cableado THW estructurado en pared'),
(1, '/uploads/trabajos/trabajo_1_1748002001_2.webp', 'Conexión de circuito dedicado para horno eléctrico 240V'),
(1, '/uploads/trabajos/trabajo_1_1748002001_3.jpg',  'Instalación de tomacorrientes GFCI en área húmeda de cocina'),
(1, '/uploads/trabajos/trabajo_1_1748002002_0.jpg',  'Instalación de sistema de aire acondicionado central 5 toneladas'),
(1, '/uploads/trabajos/trabajo_1_1748002002_1.jpg',  'Mantenimiento preventivo de condensadora y limpieza de filtros');

-- Ana (perfil_id=2) — 4 fotos: plomería
INSERT INTO `foto_trabajos` (`tecnico_perfil_id`, `url`, `descripcion`) VALUES
(2, '/uploads/trabajos/trabajo_2_1748002003_0.jpg', 'Instalación de tubería PVC de 1/2" para agua fría en baño completo'),
(2, '/uploads/trabajos/trabajo_2_1748002003_1.jpg', 'Reparación de fuga en tubería de cobre con soldadura de plata'),
(2, '/uploads/trabajos/trabajo_2_1748002003_2.jpg', 'Instalación de grifería mezcladora en lavamanos y tina'),
(2, '/uploads/trabajos/trabajo_2_1748002003_3.jpg', 'Sistema de desagüe y sifón instalado en lavadero de patio');

-- Jorge Mendoza (perfil_id=3) — 4 fotos: carpintería
INSERT INTO `foto_trabajos` (`tecnico_perfil_id`, `url`, `descripcion`) VALUES
(3, '/uploads/trabajos/trabajo_3_1748002004_0.webp', 'Closet empotrado a medida en cedro natural con barniz semi-mate'),
(3, '/uploads/trabajos/trabajo_3_1748002004_1.jpg',  'Fabricación de mueble de cocina en melanina con bordes en aluminio'),
(3, '/uploads/trabajos/trabajo_3_1748002004_2.jpg',  'Instalación de puerta de madera maciza con marco y herrajes nuevos'),
(3, '/uploads/trabajos/trabajo_3_1748002004_3.jpg',  'Estante flotante de madera de pino con soporte oculto en sala');

-- Carmen (perfil_id=4) — 4 fotos: albañilería y pintura
INSERT INTO `foto_trabajos` (`tecnico_perfil_id`, `url`, `descripcion`) VALUES
(4, '/uploads/trabajos/trabajo_4_1748002005_0.jpeg', 'Fachada exterior terminada con pintura látex y masillado de grietas'),
(4, '/uploads/trabajos/trabajo_4_1748002005_1.jpeg', 'Sala pintada en gris perla con franja decorativa en pared principal'),
(4, '/uploads/trabajos/trabajo_4_1748002005_2.jpg',  'Acabado en estuco veneciano en comedor con efecto bicolor'),
(4, '/uploads/trabajos/trabajo_4_1748002005_3.jpg',  'Pintura de barda perimetral con impermeabilizante y sellador');

-- Jorge García (perfil_id=6) — 4 fotos: electricidad y aires
INSERT INTO `foto_trabajos` (`tecnico_perfil_id`, `url`, `descripcion`) VALUES
(6, '/uploads/trabajos/trabajo_6_1748002006_0.jpg',  'Instalación de split Samsung WindFree 18000 BTU con canaleta'),
(6, '/uploads/trabajos/trabajo_6_1748002006_1.jpg',  'Cableado 220V dedicado para split con breaker independiente en tablero'),
(6, '/uploads/trabajos/trabajo_6_1748002006_2.jpg',  'Unidad condensadora instalada con soporte antisísmico en pared exterior'),
(6, '/uploads/trabajos/trabajo_6_1748002006_3.jpeg', 'Revisión y limpieza de tablero eléctrico residencial de 12 circuitos');

SET FOREIGN_KEY_CHECKS = 1;
