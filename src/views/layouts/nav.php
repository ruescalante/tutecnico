<?php
$isLogged = !empty($_SESSION['user_id']);
$role = $_SESSION['role'] ?? null;
$userPhoto = $_SESSION['user_photo'] ?? null;
$userName = $_SESSION['user_name'] ?? '';
$userInitial = $userName ? strtoupper(mb_substr($userName, 0, 1)) : '?';
$_navCurrentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

function navIsActive(string $path): bool {
    $current = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    return $path === '/' ? $current === '/' : str_starts_with($current, $path);
}

function navLinkClass(string $path): string {
    return navIsActive($path)
        ? 'text-primary dark:text-primary-fixed font-bold pb-1 transition-colors'
        : 'text-on-surface-variant dark:text-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors pb-1';
}
?>


<header class="bg-surface dark:bg-on-surface docked full-width top-0 shadow-sm dark:shadow-none border-b border-outline-variant dark:border-outline z-50 sticky">
    <div class="flex justify-between items-center h-16 px-margin-mobile md:px-margin-desktop max-w-container-max-width mx-auto w-full">
       
        <a href="/" class="flex items-center gap-2 cursor-pointer transition-all duration-200 ease-in-out active:scale-95 hover:bg-surface-container-low dark:hover:bg-secondary-container rounded-lg p-1">
            <img alt="TuTécnico Logo" class="w-8 h-8 rounded-full bg-primary-container object-cover" data-alt="A clean, modern geometric logo icon for a home service or technician app." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCKGKUQuDyn9-DA0QTDuRGBr79zaWG_w4yaTUDc_emZHYorlgLxCtvnIOdldrdQHPmypH2GGKjiB_jjFdBeFod7iVMjNI0FOisd6MBXEIOyq_Vmiz9NkyfLpQVDE6dXUf9dJdV9bHsReIm58zL8ttWaaDoCu2aF1RYgIGAsCA9zOeM5K3Lkebt7gYY-877VR6x8-Kx0xXsSIGdYmIOqV-Y4r-wWJ4UBKy4LmwKRyErREfVtlGMqld_-UNit2fH8x3vs4ERBpg1OHJc"/>
            <span class="font-headline-md text-headline-md font-bold text-on-surface dark:text-surface">TuTécnico</span>
        </a>

        
        <nav class="hidden md:flex items-center gap-8">
            <a href="/" class="<?= navLinkClass('/') ?>">Inicio</a>

            <?php if ($isLogged): ?>
                <a href="/dashboard" class="<?= navLinkClass('/dashboard') ?>">Dashboard</a>
                <a href="/perfil" class="<?= navLinkClass('/perfil') ?>">Perfil</a>
                <a href="/ejemplo" class="<?= navLinkClass('/ejemplo') ?>">Solicitudes</a>
            <?php else: ?>
                <a href="/login" class="<?= navLinkClass('/login') ?>">Ingresar</a>
                <a href="/registro" class="<?= navLinkClass('/registro') ?>">Registrarse</a>
            <?php endif; ?>
        </nav>

        <div class="flex items-center gap-2 md:gap-4">
            <button id="burgerBtn" class="md:hidden p-2 rounded-full text-on-surface-variant hover:bg-surface-container-low dark:hover:bg-secondary-container transition-colors active:scale-95" aria-label="Abrir menú">
                <span class="material-symbols-outlined">menu</span>
            </button>

            <?php if ($isLogged): ?>
                <button aria-label="Notifications" class="hidden md:flex p-2 text-on-surface-variant hover:text-primary dark:text-surface-variant dark:hover:text-primary-fixed transition-colors hover:bg-surface-container-low dark:hover:bg-secondary-container rounded-full items-center justify-center transition-all duration-200 ease-in-out active:scale-95">
                    <span class="material-symbols-outlined" data-icon="notifications">notifications</span>
                </button>
            <?php endif; ?>

            <?php if ($isLogged): ?>
                <div class="hidden md:flex items-center gap-3">
                    <div class="cursor-pointer hover:ring-2 hover:ring-primary-container rounded-full transition-all duration-200">
                        <?php if ($userPhoto): ?>
                            <img alt="Foto de perfil" class="w-10 h-10 rounded-full object-cover border border-outline-variant"
                                 src="<?= htmlspecialchars($userPhoto) ?>"/>
                        <?php else: ?>
                            <span class="w-10 h-10 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center font-bold text-base border border-outline-variant select-none">
                                <?= htmlspecialchars($userInitial) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <form action="/logout" method="POST" class="inline">
                        <button type="submit" class="px-4 py-2 bg-on-surface-variant text-surface rounded-lg font-label-md text-label-md hover:bg-on-surface transition-colors active:scale-95">Salir</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>


<div id="mobileMenu" class="hidden fixed inset-0 z-[200]" aria-modal="true" role="dialog">
    
    <div id="menuBackdrop" class="absolute inset-0 bg-scrim/50 backdrop-blur-sm"></div>
    
    <div class="absolute top-0 right-0 h-full w-72 bg-surface dark:bg-on-surface shadow-2xl flex flex-col">
        
        <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant dark:border-outline">
            <span class="font-bold text-lg text-on-surface dark:text-surface">Menú</span>
            <button id="closeMenuBtn" class="p-2 rounded-full hover:bg-surface-container-low dark:hover:bg-secondary-container text-on-surface-variant dark:text-surface-variant transition-colors" aria-label="Cerrar menú">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        
        <?php if ($isLogged): ?>
        <div class="flex items-center gap-3 px-6 py-4 border-b border-outline-variant dark:border-outline">
            <?php if ($userPhoto): ?>
                <img src="<?= htmlspecialchars($userPhoto) ?>" class="w-10 h-10 rounded-full object-cover border border-outline-variant" alt="Avatar"/>
            <?php else: ?>
                <span class="w-10 h-10 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center font-bold text-base border border-outline-variant select-none">
                    <?= htmlspecialchars($userInitial) ?>
                </span>
            <?php endif; ?>
            <div>
                <p class="font-semibold text-on-surface dark:text-surface"><?= htmlspecialchars($userName) ?></p>
                <p class="text-xs text-on-surface-variant dark:text-surface-variant capitalize"><?= htmlspecialchars($role ?? '') ?></p>
            </div>
        </div>
        <?php endif; ?>

        
        <nav class="flex flex-col gap-1 px-4 py-4 flex-1 overflow-y-auto">
            <a href="/" class="flex items-center gap-3 px-4 py-3 rounded-xl <?= navIsActive('/') ? 'bg-primary-container text-on-primary-container font-semibold' : 'text-on-surface-variant dark:text-surface-variant hover:bg-surface-container-high dark:hover:bg-on-secondary-fixed-variant' ?> transition-colors">
                <span class="material-symbols-outlined text-xl">home</span> Inicio
            </a>
            <?php if ($isLogged): ?>
            <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl <?= navIsActive('/dashboard') ? 'bg-primary-container text-on-primary-container font-semibold' : 'text-on-surface-variant dark:text-surface-variant hover:bg-surface-container-high dark:hover:bg-on-secondary-fixed-variant' ?> transition-colors">
                <span class="material-symbols-outlined text-xl">dashboard</span> Dashboard
            </a>
            <a href="/perfil" class="flex items-center gap-3 px-4 py-3 rounded-xl <?= navIsActive('/perfil') ? 'bg-primary-container text-on-primary-container font-semibold' : 'text-on-surface-variant dark:text-surface-variant hover:bg-surface-container-high dark:hover:bg-on-secondary-fixed-variant' ?> transition-colors">
                <span class="material-symbols-outlined text-xl">person</span> Perfil
            </a>
            <a href="/ejemplo" class="flex items-center gap-3 px-4 py-3 rounded-xl <?= navIsActive('/ejemplo') ? 'bg-primary-container text-on-primary-container font-semibold' : 'text-on-surface-variant dark:text-surface-variant hover:bg-surface-container-high dark:hover:bg-on-secondary-fixed-variant' ?> transition-colors">
                <span class="material-symbols-outlined text-xl">assignment</span> Solicitudes
            </a>
            <?php else: ?>
            <a href="/login" class="flex items-center gap-3 px-4 py-3 rounded-xl text-on-surface-variant dark:text-surface-variant hover:bg-surface-container-high dark:hover:bg-on-secondary-fixed-variant transition-colors">
                <span class="material-symbols-outlined text-xl">login</span> Ingresar
            </a>
            <a href="/registro" class="flex items-center gap-3 px-4 py-3 rounded-xl text-on-surface-variant dark:text-surface-variant hover:bg-surface-container-high dark:hover:bg-on-secondary-fixed-variant transition-colors">
                <span class="material-symbols-outlined text-xl">person_add</span> Registrarse
            </a>
            <?php endif; ?>
        </nav>

        <!-- Logout -->
        <?php if ($isLogged): ?>
        <div class="px-6 pb-6 pt-4 border-t border-outline-variant dark:border-outline">
            <form action="/logout" method="POST">
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-error-container text-on-error-container hover:bg-error hover:text-on-error transition-colors font-semibold">
                    <span class="material-symbols-outlined text-xl">logout</span> Salir
                </button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
(function () {
    var burger   = document.getElementById('burgerBtn');
    var menu     = document.getElementById('mobileMenu');
    var backdrop = document.getElementById('menuBackdrop');
    var closeBtn = document.getElementById('closeMenuBtn');

    function openMenu()  { menu.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
    function closeMenu() { menu.classList.add('hidden');    document.body.style.overflow = ''; }

    if (burger)   burger.addEventListener('click', openMenu);
    if (closeBtn) closeBtn.addEventListener('click', closeMenu);
    if (backdrop) backdrop.addEventListener('click', closeMenu);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeMenu(); });
}());
</script>
