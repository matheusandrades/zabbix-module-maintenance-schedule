<?php
namespace Modules\Calendario;

use Zabbix\Core\CModule;
use APP;
use CMenuItem;

class Module extends CModule {
    public function init(): void {
        APP::Component()->get('menu.main')
            ->findOrAdd(_('Reports'))
                ->getSubmenu()
                    ->insertAfter(_('Notification'),
                        (new CMenuItem(_('Calendário de Manutenções')))->setAction('maintenance.calendar')
                    );
    }
}

