<?php

require_once BASE_PATH . '/models/Notificacion.php';

class NotificacionController extends Controller
{
    public function getRecent(): void
    {
        $userId = (int) $_SESSION['user_id'];
        $role   = $_SESSION['role'] ?? '';
        $items  = Notificacion::getRecent($userId, 15);
        $count  = Notificacion::getUnreadCount($userId);

        $result = [];
        foreach ($items as $n) {
            $result[] = [
                'id'      => $n['id'],
                'leida'   => (bool) $n['leida'],
                'fecha'   => $n['fecha'],
                'texto'   => self::textoParaTipo($n, $role),
                'url'     => self::urlParaTipo($n['tipo'], (int) $n['referencia_id'], $role),
                'icono'   => self::iconoParaTipo($n['tipo']),
            ];
        }

        header('Content-Type: application/json');
        echo json_encode(['count' => $count, 'items' => $result]);
        exit;
    }

    public function markAllRead(): void
    {
        $userId = (int) $_SESSION['user_id'];
        Notificacion::markAllRead($userId);

        header('Content-Type: application/json');
        echo json_encode(['ok' => true]);
        exit;
    }

    private static function textoParaTipo(array $n, string $role): string
    {
        $tipo    = $n['tipo'];
        $titulo  = $n['solicitud_titulo'] ?? null;
        $cliente = $n['cliente_nombre']   ?? null;
        $tecnico = $n['tecnico_nombre']   ?? null;

        $t = fn(?string $s) => $s ? "'{$s}'" : 'una solicitud';

        return match($tipo) {
            'solicitud_nueva'      => 'Nueva solicitud: ' . $t($titulo) . ($cliente ? " de {$cliente}" : ''),
            'solicitud_aceptada'   => 'Tu perfil de técnico fue aprobado',
            'solicitud_cancelada'  => 'Solicitud ' . $t($titulo) . ' cancelada',
            'solicitud_completada' => ($tecnico ?? 'El técnico') . ' marcó como completado: ' . $t($titulo),
            'cotizacion_nueva'     => 'Cotización de ' . ($tecnico ?? 'el técnico') . ' para ' . $t($titulo),
            'cotizacion_aceptada'  => ($cliente ?? 'El cliente') . ' aceptó tu cotización para ' . $t($titulo),
            'cotizacion_rechazada' => ($cliente ?? 'El cliente') . ' rechazó la cotización para ' . $t($titulo),
            'mensaje_nuevo'        => 'Nuevo mensaje en ' . $t($titulo),
            'calificacion_nueva'   => 'Recibiste una nueva calificación',
            default                => 'Nueva notificación',
        };
    }

    private static function urlParaTipo(string $tipo, int $referenciaId, string $role): string
    {
        return match($tipo) {
            'solicitud_nueva',
            'cotizacion_aceptada',
            'cotizacion_rechazada' => '/dashboard/tecnico/solicitudes/' . $referenciaId,

            'solicitud_completada',
            'cotizacion_nueva',
            'solicitud_cancelada'  => $role === 'tecnico'
                                        ? '/dashboard/tecnico/solicitudes/' . $referenciaId
                                        : '/solicitudes/' . $referenciaId,

            'solicitud_aceptada'   => '/dashboard/tecnico',
            'calificacion_nueva'   => '/dashboard/tecnico',
            'mensaje_nuevo'        => $role === 'tecnico'
                                        ? '/dashboard/tecnico/solicitudes/' . $referenciaId
                                        : '/solicitudes/' . $referenciaId,
            default                => '/dashboard',
        };
    }

    private static function iconoParaTipo(string $tipo): string
    {
        return match($tipo) {
            'solicitud_nueva'      => 'assignment',
            'solicitud_aceptada'   => 'verified',
            'solicitud_cancelada'  => 'cancel',
            'solicitud_completada' => 'task_alt',
            'cotizacion_nueva'     => 'request_quote',
            'cotizacion_aceptada'  => 'check_circle',
            'cotizacion_rechazada' => 'do_not_disturb_on',
            'mensaje_nuevo'        => 'chat',
            'calificacion_nueva'   => 'star',
            default                => 'notifications',
        };
    }
}
