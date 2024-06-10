<?php

namespace Modules\Calendario\Actions;

use CController;
use CControllerResponseData;
use API;

class MaintenanceCalendar extends CController {

    public function init(): void {
        $this->disableCsrfValidation();
    }

    protected function checkInput(): bool {
        return true;
    }

    protected function checkPermissions(): bool {
        return true;
    }

    protected function doAction(): void {
        // Lógica para obter os dados das manutenções agendadas via API do Zabbix
        $maintenanceData = $this->getMaintenances();
        
        $data = ['maintenances' => $maintenanceData];
        $response = new CControllerResponseData($data);
        $this->setResponse($response);
    }

    private function getMaintenances(): array {
        $response = API::Maintenance()->get([
            'output' => ['maintenanceid', 'name', 'active_since', 'active_till', 'description'],
            'selectTimeperiods' => ['timeperiod_type', 'period', 'start_date', 'end_date']
        ]);

        $maintenances = [];
        foreach ($response as $maintenance) {
            $maintenances[] = [
                'id' => $maintenance['maintenanceid'],
                'name' => $maintenance['name'],
                'start' => date('c', $maintenance['active_since']),
                'end' => date('c', $maintenance['active_till']),
                'description' => $maintenance['description']
            ];
        }

        return $maintenances;
    }
}
