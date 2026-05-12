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
('Electricidad',      'Instalaciones eléctricas, reparación de circuitos y tomacorrientes'),
('Plomería',          'Tuberías, grifos, desagües y sistemas de agua'),
('Carpintería',       'Muebles, puertas, ventanas y trabajos en madera'),
('Pintura',           'Pintura interior y exterior de paredes y superficies'),
('Aires Acondicionados', 'Instalación, mantenimiento y reparación de A/C'),
('Cerrajería',        'Apertura de cerraduras, cambio de llaves y chapas'),
('Jardinería',        'Poda, mantenimiento de jardines y áreas verdes'),
('Limpieza',          'Limpieza profunda de hogares y oficinas');

-- -------------------------------------------------------------
-- Usuarios de prueba
-- -------------------------------------------------------------
-- Admin
INSERT INTO `users` (`nombre`, `correo`, `contrasena`, `telefono`, `rol`, `activo`) VALUES
('Admin Sistema', 'admin@servicios.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '22000000', 'admin', 1);

-- Clientes
INSERT INTO `users` (`nombre`, `correo`, `contrasena`, `telefono`, `rol`, `activo`) VALUES
('Carlos Ramírez',  'carlos@mail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '77112233', 'cliente', 1),
('María González',  'maria@mail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '77445566', 'cliente', 1),
('Luis Herrera',  'luis@mail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '77557788', 'cliente', 1);

-- Técnicos
INSERT INTO `users` (`nombre`, `correo`, `contrasena`, `telefono`, `rol`, `activo`) VALUES
('Roberto López',  'roberto@mail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '78001122', 'tecnico', 1),
('Ana Martínez',   'ana@mail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 '78003344', 'tecnico', 1);

-- -------------------------------------------------------------
-- Perfiles de técnicos
-- ID 4 = Luis (cliente con solicitud pendiente)
-- ID 5 = Roberto (técnico activo)
-- ID 6 = Ana (técnico activo)
-- -------------------------------------------------------------
INSERT INTO `tecnico_perfiles`
     (`user_id`, `zona_cobertura`, `descripcion`, `disponibilidad`, `estado`, `cancelaciones`, `comentario_admin`) VALUES
(5, 'San Salvador, Soyapango, Ilopango',
   'Electricista con 10 años de experiencia en instalaciones residenciales y comerciales.',
    1, 'activo', 0, 'Validado'),
(6, 'Santa Ana, Metapán',
   'Plomera certificada, especialista en tuberías PVC y cobre.',
    1, 'activo', 0, 'Validada'),
(4, 'Apopa, Nejapa',
    'Técnico general con experiencia en mantenimiento de hogar.',
    1, 'pendiente', 0, 'Pendiente de revisión de documentos');

-- -------------------------------------------------------------
-- Categorías por técnico
-- -------------------------------------------------------------
INSERT INTO `tecnico_categorias` (`user_id`, `id_categoria`) VALUES
(5, 1),   -- Roberto → Electricidad
(5, 5),   -- Roberto → Aires Acondicionados
(6, 2),   -- Ana     → Plomería
(6, 8);   -- Ana     → Limpieza

-- -------------------------------------------------------------
-- Solicitud de prueba
-- -------------------------------------------------------------
INSERT INTO `solicitudes`
    (`id_cliente`, `id_tecnico`, `titulo`, `descripcion`, `direccion`, `estado`) VALUES
(2, 5,
 'Reparar tomacorriente dañado',
 'El tomacorriente de la sala dejó de funcionar después de un corto circuito.',
 'Col. Escalón, Calle La Mascota #45, San Salvador',
 'aceptada');

-- Cotización para esa solicitud
INSERT INTO `cotizaciones` (`id_solicitud`, `precio_estimado`, `descripcion`, `estado`) VALUES
(1, 35.00, 'Revisión eléctrica + reemplazo de tomacorriente y cableado dañado.', 'aceptada');

-- Mensaje de prueba
INSERT INTO `mensajes` (`id_solicitud`, `remitente_tipo`, `remitente_id`, `contenido`) VALUES
(1, 'cliente', 2, 'Buenos días, ¿a qué hora podría venir?'),
(1, 'tecnico', 5, 'Hola Carlos, puedo estar a las 10am. ¿Le parece bien?');

SET FOREIGN_KEY_CHECKS = 1;