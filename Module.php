<?php
namespace Modules\MaintenanceCalendar;

use Zabbix\Core\CModule,
    APP,
    CMenuItem;

class Module extends CModule {
    public function init(): void {
        APP::Component()->get('menu.main')
            ->findOrAdd(_('Reports'))
                ->getSubmenu()
                    ->insertAfter(_('Notification'),
                        (new CMenuItem(_('Calendario de Manutenções')))->setAction('maintenance.calendar')
                    );
    }
}

