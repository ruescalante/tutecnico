<?php
$isLogged = !empty($_SESSION['user_id']);
$role = $_SESSION['role'] ?? null;
$userPhoto = $_SESSION['user_photo'] ?? null;
$userName = $_SESSION['user_name'] ?? '';
$userInitial = $userName ? strtoupper(mb_substr($userName, 0, 1)) : '?';
$_navCurrentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$unreadCount = 0;
if ($isLogged) {
    require_once BASE_PATH . '/models/Notificacion.php';
    $unreadCount = Notificacion::getUnreadCount((int) $_SESSION['user_id']);
}

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
            <a href="/tecnicos" class="<?= navLinkClass('/tecnicos') ?>">Buscar Técnicos</a>

            <?php if ($isLogged): ?>
                <a href="/dashboard" class="<?= navLinkClass('/dashboard') ?>">Dashboard</a>
                <a href="/perfil" class="<?= navLinkClass('/perfil') ?>">Perfil</a>
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
                <div class="hidden md:block relative" id="notifWrapper">
                    <button id="notifBtn" aria-label="Notificaciones" class="relative p-2 text-on-surface-variant hover:text-primary dark:text-surface-variant dark:hover:text-primary-fixed transition-colors hover:bg-surface-container-low dark:hover:bg-secondary-container rounded-full flex items-center justify-center duration-200 ease-in-out active:scale-95">
                        <span class="material-symbols-outlined">notifications</span>
                        <?php if ($unreadCount > 0): ?>
                        <span id="notifBadge" class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-error text-on-error text-[10px] font-bold rounded-full flex items-center justify-center px-1 leading-none">
                            <?= $unreadCount > 99 ? '99+' : $unreadCount ?>
                        </span>
                        <?php else: ?>
                        <span id="notifBadge" class="hidden absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-error text-on-error text-[10px] font-bold rounded-full flex items-center justify-center px-1 leading-none"></span>
                        <?php endif; ?>
                    </button>

                    <div id="notifPanel" class="hidden absolute right-0 top-full mt-2 w-80 bg-surface dark:bg-on-surface border border-outline-variant dark:border-outline rounded-2xl shadow-2xl z-[300] overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-outline-variant dark:border-outline">
                            <span class="font-semibold text-on-surface dark:text-surface text-sm">Notificaciones</span>
                            <button id="markAllReadBtn" class="text-xs text-primary dark:text-primary-fixed hover:underline font-medium">Marcar todas como leídas</button>
                        </div>
                        <div id="notifList" class="max-h-80 overflow-y-auto divide-y divide-outline-variant dark:divide-outline">
                            <p class="text-center text-on-surface-variant text-sm py-8">Cargando...</p>
                        </div>
                        <div class="px-4 py-2 border-t border-outline-variant dark:border-outline text-center">
                            <a href="/dashboard" class="text-xs text-primary dark:text-primary-fixed hover:underline">Ver dashboard</a>
                        </div>
                    </div>
                </div>
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
            <a href="/tecnicos" class="flex items-center gap-3 px-4 py-3 rounded-xl <?= navIsActive('/tecnicos') ? 'bg-primary-container text-on-primary-container font-semibold' : 'text-on-surface-variant dark:text-surface-variant hover:bg-surface-container-high dark:hover:bg-on-secondary-fixed-variant' ?> transition-colors">
                <span class="material-symbols-outlined text-xl">search</span> Buscar Técnicos
            </a>
            <?php if ($isLogged): ?>
            <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl <?= navIsActive('/dashboard') ? 'bg-primary-container text-on-primary-container font-semibold' : 'text-on-surface-variant dark:text-surface-variant hover:bg-surface-container-high dark:hover:bg-on-secondary-fixed-variant' ?> transition-colors">
                <span class="material-symbols-outlined text-xl">dashboard</span> Dashboard
            </a>
            <a href="/perfil" class="flex items-center gap-3 px-4 py-3 rounded-xl <?= navIsActive('/perfil') ? 'bg-primary-container text-on-primary-container font-semibold' : 'text-on-surface-variant dark:text-surface-variant hover:bg-surface-container-high dark:hover:bg-on-secondary-fixed-variant' ?> transition-colors">
                <span class="material-symbols-outlined text-xl">person</span> Perfil
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

(function () {
    var btn         = document.getElementById('notifBtn');
    var panel       = document.getElementById('notifPanel');
    var list        = document.getElementById('notifList');
    var badge       = document.getElementById('notifBadge');
    var markAllBtn  = document.getElementById('markAllReadBtn');

    if (!btn) return;

    var isOpen = false;

    function timeAgo(dateStr) {
        var d = new Date(dateStr.replace(' ', 'T') + 'Z');
        var diff = Math.max(0, Math.floor((Date.now() - d.getTime()) / 1000));
        if (diff < 60)    return 'ahora mismo';
        if (diff < 3600)  return Math.floor(diff / 60) + ' min';
        if (diff < 86400) return Math.floor(diff / 3600) + ' h';
        return Math.floor(diff / 86400) + ' d';
    }

    function renderItems(items) {
        if (!items.length) {
            list.innerHTML = '<p class="text-center text-on-surface-variant text-sm py-8">Sin notificaciones</p>';
            return;
        }
        list.innerHTML = items.map(function (n) {
            var unreadClass = n.leida
                ? 'bg-transparent'
                : 'bg-primary-container/20 dark:bg-primary/10';
            return '<a href="' + n.url + '" class="flex items-start gap-3 px-4 py-3 hover:bg-surface-container-low dark:hover:bg-secondary-container transition-colors ' + unreadClass + '">' +
                '<span class="material-symbols-outlined text-lg text-primary dark:text-primary-fixed mt-0.5 shrink-0">' + n.icono + '</span>' +
                '<div class="flex-1 min-w-0">' +
                    '<p class="text-sm text-on-surface dark:text-surface leading-snug">' + n.texto + '</p>' +
                    '<p class="text-xs text-on-surface-variant dark:text-surface-variant mt-0.5">' + timeAgo(n.fecha) + '</p>' +
                '</div>' +
                (!n.leida ? '<span class="w-2 h-2 rounded-full bg-primary dark:bg-primary-fixed shrink-0 mt-1.5"></span>' : '') +
            '</a>';
        }).join('');
    }

    function updateBadge(count) {
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    function loadNotifs() {
        fetch('/notificaciones/recientes', { credentials: 'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                renderItems(data.items);
                updateBadge(data.count);
                if (data.count > 0) {
                    fetch('/notificaciones/marcar-leidas', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    }).then(function () { updateBadge(0); });
                }
            })
            .catch(function () {
                list.innerHTML = '<p class="text-center text-on-surface-variant text-sm py-8">Error al cargar</p>';
            });
    }

    function openPanel() {
        panel.classList.remove('hidden');
        isOpen = true;
        loadNotifs();
    }

    function closePanel() {
        panel.classList.add('hidden');
        isOpen = false;
    }

    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        isOpen ? closePanel() : openPanel();
    });

    document.addEventListener('click', function (e) {
        if (isOpen && !panel.contains(e.target) && e.target !== btn) {
            closePanel();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && isOpen) closePanel();
    });

    if (markAllBtn) {
        markAllBtn.addEventListener('click', function (e) {
            e.preventDefault();
            fetch('/notificaciones/marcar-leidas', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function () {
                updateBadge(0);
                loadNotifs();
            });
        });
    }
}());
</script>
