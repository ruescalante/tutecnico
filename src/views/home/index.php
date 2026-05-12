<div class="container">
	<h1>TuTecnico</h1>
	<p class="muted">Plataforma para clientes, técnicos y administración.</p>

	<div class="form-actions" style="margin-top: 1rem;">
		<?php if (!empty($_SESSION['user_id'])): ?>
			<a class="btn btn-primary" href="/dashboard">Ir al dashboard</a>
			<a class="btn" href="/ejemplo">Ver solicitudes</a>
		<?php else: ?>
			<a class="btn btn-primary" href="/login">Iniciar sesión</a>
			<a class="btn" href="/registro">Crear cuenta</a>
		<?php endif; ?>
	</div>
</div>
