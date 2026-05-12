<?php
$isLogged = !empty($_SESSION['user_id']);
$role = $_SESSION['role'] ?? null;
?>

<!-- TopNavBar Component -->
<header class="bg-surface dark:bg-on-surface docked full-width top-0 shadow-sm dark:shadow-none border-b border-outline-variant dark:border-outline z-50 sticky">
    <div class="flex justify-between items-center h-16 px-margin-mobile md:px-margin-desktop max-w-container-max-width mx-auto w-full">
        <!-- Brand Logo -->
        <a href="/" class="flex items-center gap-2 cursor-pointer transition-all duration-200 ease-in-out active:scale-95 hover:bg-surface-container-low dark:hover:bg-secondary-container rounded-lg p-1">
            <img alt="TuTécnico Logo" class="w-8 h-8 rounded-full bg-primary-container object-cover" data-alt="A clean, modern geometric logo icon for a home service or technician app." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCKGKUQuDyn9-DA0QTDuRGBr79zaWG_w4yaTUDc_emZHYorlgLxCtvnIOdldrdQHPmypH2GGKjiB_jjFdBeFod7iVMjNI0FOisd6MBXEIOyq_Vmiz9NkyfLpQVDE6dXUf9dJdV9bHsReIm58zL8ttWaaDoCu2aF1RYgIGAsCA9zOeM5K3Lkebt7gYY-877VR6x8-Kx0xXsSIGdYmIOqV-Y4r-wWJ4UBKy4LmwKRyErREfVtlGMqld_-UNit2fH8x3vs4ERBpg1OHJc"/>
            <span class="font-headline-md text-headline-md font-bold text-on-surface dark:text-surface">TuTécnico</span>
        </a>

        <!-- Navigation Links (Desktop) -->
        <nav class="hidden md:flex items-center gap-8">
            <a href="/" class="text-primary dark:text-primary-fixed font-bold pb-1 transition-colors">Inicio</a>

            <?php if ($isLogged): ?>
                <a href="/dashboard" class="text-on-surface-variant dark:text-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors pb-1">Dashboard</a>
                <a href="/perfil" class="text-on-surface-variant dark:text-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors pb-1">Perfil</a>
                <a href="/ejemplo" class="text-on-surface-variant dark:text-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors pb-1">Solicitudes</a>
            <?php else: ?>
                <a href="/login" class="text-on-surface-variant dark:text-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors pb-1">Ingresar</a>
                <a href="/registro" class="text-on-surface-variant dark:text-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors pb-1">Registrarse</a>
            <?php endif; ?>
        </nav>

        <!-- Trailing Actions -->
        <div class="flex items-center gap-4">
            <!-- Notifications Icon -->
            <?php if ($isLogged): ?>
                <button aria-label="Notifications" class="p-2 text-on-surface-variant hover:text-primary dark:text-surface-variant dark:hover:text-primary-fixed transition-colors hover:bg-surface-container-low dark:hover:bg-secondary-container rounded-full flex items-center justify-center transition-all duration-200 ease-in-out active:scale-95">
                    <span class="material-symbols-outlined" data-icon="notifications">notifications</span>
                </button>
            <?php endif; ?>

            <!-- Profile Avatar / Logout -->
            <?php if ($isLogged): ?>
                <div class="cursor-pointer hover:ring-2 hover:ring-primary-container rounded-full transition-all duration-200">
                    <img alt="User profile avatar" class="w-10 h-10 rounded-full object-cover border border-outline-variant" data-alt="User avatar" src="https://lh3.googleusercontent.com/aida-public/AB6AXuByAvDmqyfXHSG8AFti9e4MxE6-Jx7gygvDRKH6oDXr7s8rPmd292iKd1B1jt9d_pPGSL-JIgUOa3eiLFQj64rJKYekvoLzJ0AcGG8yRAY1ORcxMkDsfueT_HFsy-1mYenpC17JwpGCV4EulBvfZXm8-mPZnOF24-7D1xU700bvNKinn3YjLOul4uFk2rYZj9Dt740v0ssGWbM29WSI9OWIReVk-sj0Yy3K8TdtyjW5ZkC0pwqvfb4crxRrDTDbLD023oKDDixahLg"/>
                </div>
                <form action="/logout" method="POST" class="inline">
                    <button type="submit" class="px-4 py-2 bg-on-surface-variant text-surface rounded-lg font-label-md text-label-md hover:bg-on-surface transition-colors active:scale-95">Salir</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- Main Content Area (placeholder spacing managed by layout) -->

<!-- BottomNavBar Component (Mobile Only) -->
<nav class="bg-surface dark:bg-on-surface fixed bottom-0 w-full z-50 rounded-t-xl border-t border-outline-variant dark:border-outline shadow-[0_-4px_12px_0_rgba(0,0,0,0.08)] fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 py-2 md:hidden">
    <button class="flex flex-col items-center justify-center text-primary dark:text-primary-fixed bg-primary-container/20 dark:bg-primary-container/10 rounded-xl p-2 active:scale-90 transition-transform duration-150 w-16">
        <span class="material-symbols-outlined mb-1" data-icon="home" data-weight="fill" style="font-variation-settings: 'FILL' 1;">home</span>
        <span class="font-label-md text-[10px] leading-tight font-medium">Inicio</span>
    </button>
    <button class="flex flex-col items-center justify-center text-on-surface-variant dark:text-surface-variant p-2 hover:bg-surface-container-high dark:hover:bg-on-secondary-fixed-variant active:scale-90 transition-transform duration-150 rounded-xl w-16">
        <span class="material-symbols-outlined mb-1" data-icon="search">search</span>
        <span class="font-label-md text-[10px] leading-tight font-medium">Buscar</span>
    </button>
    <button class="flex flex-col items-center justify-center text-on-surface-variant dark:text-surface-variant p-2 hover:bg-surface-container-high dark:hover:bg-on-secondary-fixed-variant active:scale-90 transition-transform duration-150 rounded-xl w-16">
        <span class="material-symbols-outlined mb-1" data-icon="build">build</span>
        <span class="font-label-md text-[10px] leading-tight font-medium">Servicios</span>
    </button>
    <button class="flex flex-col items-center justify-center text-on-surface-variant dark:text-surface-variant p-2 hover:bg-surface-container-high dark:hover:bg-on-secondary-fixed-variant active:scale-90 transition-transform duration-150 rounded-xl w-16">
        <span class="material-symbols-outlined mb-1" data-icon="person">person</span>
        <span class="font-label-md text-[10px] leading-tight font-medium">Perfil</span>
    </button>
</nav>