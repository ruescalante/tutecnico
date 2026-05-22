-- =============================================================
-- SEEDS - Datos iniciales / de prueba
-- Ejecutar DESPUÉS de migrations.sql
-- Contraseñas: todas son "password" (hash bcrypt)
-- =============================================================

SET FOREIGN_KEY_CHECKS = 0;

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
INSERT INTO `users` (`nombre`, `correo`, `contrasena`, `telefono`, `rol`, `activo`) VALUES
('Admin Sistema', 'admin@servicios.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '22000000', 'admin', 1);

-- Clientes (id=2..6)
INSERT INTO `users` (`nombre`, `correo`, `contrasena`, `telefono`, `rol`, `activo`, `foto_perfil`) VALUES
('Carlos Ramírez',     'carlos@mail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '77112233', 'cliente', 1,
 '/uploads/fotos/user_1_1779076594.jpeg'),

('María González',     'maria@mail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '77445566', 'cliente', 1,
 '/uploads/fotos/user_1_1779076615.jpeg'),

('Luis Herrera',       'luis@mail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '77557788', 'cliente', 1, NULL),

('Sofía Torres',       'sofia@mail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '77119900', 'cliente', 1,
 '/uploads/fotos/user_1_1779076683.jpeg'),

('Diego Morales',      'diego@mail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '77221100', 'cliente', 1,
 '/uploads/fotos/user_1_1779076692.jpeg');

-- Técnicos (id=7..10)
INSERT INTO `users` (`nombre`, `correo`, `contrasena`, `telefono`, `rol`, `activo`, `foto_perfil`) VALUES
('Roberto López',      'roberto@mail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '78001122', 'tecnico', 1,
 '/uploads/fotos/user_1_1779077059.jpeg'),

('Ana Martínez',       'ana@mail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '78003344', 'tecnico', 1,
 '/uploads/fotos/user_1_1779077188.jpeg'),

('Jorge Mendoza',      'jorge@mail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '78005566', 'tecnico', 1,
 '/uploads/fotos/user_1_1779077201.jpeg'),

('Carmen Vásquez',     'carmen@mail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '78007788', 'tecnico', 1,
 '/uploads/fotos/user_1_1779077218.jpeg');

-- Cliente extra que también puede solicitar a técnicos (id=11)
INSERT INTO `users` (`nombre`, `correo`, `contrasena`, `telefono`, `rol`, `activo`, `foto_perfil`) VALUES
('Valentina Reyes',    'vale@mail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '77332200', 'cliente', 1,
 '/uploads/fotos/user_1_1779077434.jpeg');

-- Luis (id=4) aplica como técnico (pendiente) - sus datos quedan en usuarios pero su perfil sigue pendiente

-- -------------------------------------------------------------
-- Perfiles de técnicos
-- IDs de usuarios: Roberto=7, Ana=8, Jorge=9, Carmen=10, Luis=4
-- -------------------------------------------------------------
INSERT INTO `tecnico_perfiles`
     (`user_id`, `zona_cobertura`, `descripcion`, `disponibilidad`, `estado`, `cancelaciones`, `comentario_admin`) VALUES

(7, 'San Salvador, Soyapango, Ilopango',
 'Electricista con 10 años de experiencia en instalaciones residenciales y comerciales. Especializado en paneles eléctricos, tomacorrientes y cableado estructurado.',
 1, 'activo', 0, 'Validado'),

(8, 'Santa Ana, Metapán, Chalchuapa',
 'Plomera certificada, especialista en tuberías PVC y cobre. Manejo de sistemas de agua fría y caliente, desagües y grifería de todas las marcas.',
 1, 'activo', 0, 'Validada'),

(9, 'Santa Tecla, Antiguo Cuscatlán, San Salvador',
 'Carpintero con 8 años de experiencia en fabricación de muebles a medida, instalación de puertas, ventanas y trabajos de acabados en madera.',
 1, 'activo', 0, 'Validado'),

(10, 'Soyapango, San Martín, Ilopango',
 'Pintora profesional especializada en interiores y exteriores. Manejo de pintura en spray, estuco, texturas decorativas y pintura epóxica para pisos.',
 1, 'activo', 0, 'Validada'),

(4, 'Apopa, Nejapa',
 'Técnico general con experiencia en mantenimiento de hogar.',
 1, 'pendiente', 0, 'Pendiente de revisión de documentos');

-- -------------------------------------------------------------
-- Categorías por técnico
-- Roberto(7): Electricidad, Aires Acondicionados
-- Ana(8):     Plomería, Limpieza
-- Jorge(9):   Carpintería, Cerrajería
-- Carmen(10): Pintura, Jardinería
-- -------------------------------------------------------------
INSERT INTO `tecnico_categorias` (`user_id`, `id_categoria`) VALUES
(7, 1),  -- Roberto → Electricidad
(7, 5),  -- Roberto → Aires Acondicionados
(8, 2),  -- Ana     → Plomería
(8, 8),  -- Ana     → Limpieza
(9, 3),  -- Jorge   → Carpintería
(9, 6),  -- Jorge   → Cerrajería
(10, 4), -- Carmen  → Pintura
(10, 7); -- Carmen  → Jardinería

-- -------------------------------------------------------------
-- Solicitudes
-- id=1  Carlos(2)→Roberto(7)   aceptada  (con cotización y mensajes)
-- id=2  Carlos(2)→Roberto(7)   completada
-- id=3  María(3)→Roberto(7)    completada
-- id=4  María(3)→Ana(8)        completada
-- id=5  Sofía(5)→Roberto(7)    completada
-- id=6  Diego(6)→Ana(8)        completada
-- id=7  Valentina(11)→Roberto(7)  completada
-- id=8  Carlos(2)→Ana(8)       completada
-- id=9  Sofía(5)→Ana(8)        completada
-- id=10 Diego(6)→Jorge(9)      completada
-- id=11 Carlos(2)→Jorge(9)     completada
-- id=12 María(3)→Carmen(10)    completada
-- id=13 Valentina(11)→Carmen(10) completada
-- id=14 Diego(6)→Roberto(7)    pendiente
-- id=15 Sofía(5)→Jorge(9)      pendiente
-- -------------------------------------------------------------
INSERT INTO `solicitudes`
    (`id_cliente`, `id_tecnico`, `titulo`, `descripcion`, `direccion`, `estado`) VALUES
(2, 7,
 'Reparar tomacorriente dañado',
 'El tomacorriente de la sala dejó de funcionar después de un corto circuito.',
 'Col. Escalón, Calle La Mascota #45, San Salvador',
 'aceptada');

INSERT INTO `solicitudes`
    (`id_cliente`, `id_tecnico`, `titulo`, `descripcion`, `direccion`, `estado`, `fecha_completado`) VALUES
(2, 7,  'Instalación panel eléctrico',        'Panel de distribución para 8 circuitos.',              'Col. Escalón, Calle La Mascota #45, San Salvador', 'completada', DATE_SUB(NOW(), INTERVAL 30 DAY)),
(3, 7,  'Instalación de tomacorrientes',      'Agregar 4 tomacorrientes nuevos en oficina.',          'Av. Roosevelt #120, San Salvador',                 'completada', DATE_SUB(NOW(), INTERVAL 45 DAY)),
(3, 8,  'Reparar tuberías de baño',           'Tubería principal con fuga debajo del lavamanos.',     'Av. Roosevelt #120, San Salvador',                 'completada', DATE_SUB(NOW(), INTERVAL 60 DAY)),
(5, 7,  'Revisión sistema eléctrico general', 'Revisión completa de todo el sistema eléctrico.',      'Res. La Cima, Calle Los Pinos #8, Santa Tecla',    'completada', DATE_SUB(NOW(), INTERVAL 20 DAY)),
(6, 8,  'Instalación ducha eléctrica',        'Cambio de ducha antigua por ducha eléctrica nueva.',   'Col. Miramonte Calle #5, San Salvador',             'completada', DATE_SUB(NOW(), INTERVAL 15 DAY)),
(11, 7, 'Cambio de cableado en dormitorios',  'Cableado antiguo necesita reemplazo en 3 cuartos.',    'Urb. San Francisco, Pasaje 2 #14, Soyapango',      'completada', DATE_SUB(NOW(), INTERVAL 10 DAY)),
(2, 8,  'Reparar grifo de cocina',            'Grifo goteando constantemente, necesita reemplazo.',   'Col. Escalón, Calle La Mascota #45, San Salvador', 'completada', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(5, 8,  'Instalación lavadero exterior',      'Instalar lavadero con desagüe en patio trasero.',      'Res. La Cima, Calle Los Pinos #8, Santa Tecla',    'completada', DATE_SUB(NOW(), INTERVAL 35 DAY)),
(6, 9,  'Fabricación de closet empotrado',    'Closet a medida para habitación principal, 3m x 2.5m.','Col. Miramonte Calle #5, San Salvador',             'completada', DATE_SUB(NOW(), INTERVAL 25 DAY)),
(2, 9,  'Reparar puertas de madera',          'Dos puertas interiores con bisagras dañadas y marco descuadrado.', 'Col. Escalón, Calle La Mascota #45, San Salvador', 'completada', DATE_SUB(NOW(), INTERVAL 40 DAY)),
(3, 10, 'Pintar sala y comedor',              'Pintura completa de sala, comedor y pasillo interior.','Av. Roosevelt #120, San Salvador',                 'completada', DATE_SUB(NOW(), INTERVAL 50 DAY)),
(11, 10,'Pintar fachada exterior',            'Pintura exterior de toda la fachada con pintura látex.','Urb. San Francisco, Pasaje 2 #14, Soyapango',      'completada', DATE_SUB(NOW(), INTERVAL 18 DAY));

-- Solicitudes activas (sin completar)
INSERT INTO `solicitudes`
    (`id_cliente`, `id_tecnico`, `titulo`, `descripcion`, `direccion`, `estado`) VALUES
(6, 7,  'Instalación de luces LED',   'Cambiar toda la iluminación a LED en sala y cocina.','Col. Miramonte Calle #5, San Salvador',  'pendiente'),
(5, 9,  'Mueble a medida para sala',  'Mueble TV empotrado con estantes laterales.',        'Res. La Cima, Calle Los Pinos #8, Santa Tecla', 'pendiente');

-- -------------------------------------------------------------
-- Cotización y mensajes para la solicitud activa (id=1)
-- -------------------------------------------------------------
INSERT INTO `cotizaciones` (`id_solicitud`, `precio_estimado`, `descripcion`, `estado`) VALUES
(1, 35.00, 'Revisión eléctrica + reemplazo de tomacorriente y cableado dañado.', 'aceptada');

INSERT INTO `mensajes` (`id_solicitud`, `remitente_tipo`, `remitente_id`, `contenido`) VALUES
(1, 'cliente', 2, 'Buenos días, ¿a qué hora podría venir?'),
(1, 'tecnico', 7, 'Hola Carlos, puedo estar a las 10am. ¿Le parece bien?'),
(1, 'cliente', 2, 'Perfecto, lo espero a las 10.'),
(1, 'tecnico', 7, 'Listo, confirmado. Llevaré todo el material necesario.');

-- -------------------------------------------------------------
-- Calificaciones
-- Una por cada solicitud completada (id 2..13)
-- -------------------------------------------------------------
INSERT INTO `calificaciones` (`id_solicitud`, `puntuacion`, `comentario`) VALUES
(2,  5, 'Roberto instaló el panel perfectamente. Muy ordenado y profesional, dejó todo limpio.'),
(3,  5, 'Excelente servicio, fue puntual y terminó antes de lo esperado. Muy recomendado.'),
(4,  4, 'Ana hizo un buen trabajo con las tuberías. La fuga quedó completamente reparada.'),
(5,  5, 'Revisó todo el sistema y encontró problemas que ni sabíamos que existían. Muy detallista.'),
(6,  4, 'Buen servicio, la ducha quedó instalada correctamente. Muy amable y profesional.'),
(7,  5, 'Valentina dice que Roberto es el mejor técnico que ha contratado. Trabajo impecable.'),
(8,  4, 'El grifo quedó perfecto. Ana llegó puntual y fue muy eficiente. La recomiendo.'),
(9,  5, 'Ana instaló el lavadero exactamente como lo pedí. Quedó muy bien hecho y resistente.'),
(10, 5, 'Jorge hizo el closet de ensueño. Detalle perfecto en acabados, muy profesional.'),
(11, 4, 'Reparó las puertas rápidamente. Buena actitud y precio justo. Volvería a contratarlo.'),
(12, 5, 'Carmen transformó por completo la sala. Los colores quedaron exactamente como los quería.'),
(13, 4, 'La fachada quedó como nueva. Carmen es muy cuidadosa con los detalles. Muy satisfecha.');

-- -------------------------------------------------------------
-- Fotos de trabajos
-- tecnico_perfiles insert order: Roberto(7)=1, Ana(8)=2, Jorge(9)=3, Carmen(10)=4, Luis(4)=5
-- Luis=5 está pendiente, no aparece en la página pública
-- -------------------------------------------------------------

-- Roberto (perfil_id=1) — 7 fotos
INSERT INTO `foto_trabajos` (`tecnico_perfil_id`, `url`, `descripcion`) VALUES
(1, '/uploads/trabajos/trabajo_4_1779310094_0.jpeg', 'Panel eléctrico de distribución instalado'),
(1, '/uploads/trabajos/trabajo_4_1779310094_1.jpeg', 'Tablero de breakers residencial'),
(1, '/uploads/trabajos/trabajo_4_1779316439_0.jpeg', 'Cableado estructurado terminado'),
(1, '/uploads/fotos/user_1_1779076697.jpeg',         'Instalación de tomacorrientes dobles'),
(1, '/uploads/fotos/user_1_1779076711.jpeg',         'Revisión de circuitos en panel principal'),
(1, '/uploads/fotos/user_1_1779076839.png',          'Iluminación LED instalada en cocina'),
(1, '/uploads/fotos/user_1_1779076842.png',          'Canalización de cables en pared');

-- Ana (perfil_id=2) — 6 fotos
INSERT INTO `foto_trabajos` (`tecnico_perfil_id`, `url`, `descripcion`) VALUES
(2, '/uploads/fotos/user_1_1779077434.jpeg', 'Instalación de tubería PVC bajo lavamanos'),
(2, '/uploads/fotos/user_1_1779077446.png',  'Sistema de desagüe instalado en patio'),
(2, '/uploads/fotos/user_1_1779078883.png',  'Grifería nueva en baño principal'),
(2, '/uploads/fotos/user_1_1779076594.jpeg', 'Reparación de tubería con fuga'),
(2, '/uploads/fotos/user_1_1779076615.jpeg', 'Lavadero exterior con desagüe propio'),
(2, '/uploads/fotos/user_1_1779076683.jpeg', 'Ducha eléctrica instalada');

-- Jorge (perfil_id=3) — 5 fotos
INSERT INTO `foto_trabajos` (`tecnico_perfil_id`, `url`, `descripcion`) VALUES
(3, '/uploads/fotos/user_1_1779076692.jpeg', 'Closet empotrado a medida terminado'),
(3, '/uploads/fotos/user_1_1779077059.jpeg', 'Puerta de madera restaurada'),
(3, '/uploads/fotos/user_1_1779077188.jpeg', 'Mueble de cocina fabricado en taller'),
(3, '/uploads/fotos/user_1_1779077201.jpeg', 'Ventana de madera con marco nuevo'),
(3, '/uploads/fotos/user_1_1779077218.jpeg', 'Estante flotante instalado en sala');

-- Carmen (perfil_id=4) — 5 fotos
INSERT INTO `foto_trabajos` (`tecnico_perfil_id`, `url`, `descripcion`) VALUES
(4, '/uploads/trabajos/trabajo_4_1779310094_0.jpeg', 'Fachada exterior pintada con pintura látex'),
(4, '/uploads/trabajos/trabajo_4_1779310094_1.jpeg', 'Sala pintada con textura decorativa'),
(4, '/uploads/trabajos/trabajo_4_1779316439_0.jpeg', 'Pasillo con acabado en estuco veneciano'),
(4, '/uploads/fotos/user_1_1779076594.jpeg',         'Jardín diseñado y podado'),
(4, '/uploads/fotos/user_1_1779076615.jpeg',         'Comedor con pintura en dos tonos');

SET FOREIGN_KEY_CHECKS = 1;
