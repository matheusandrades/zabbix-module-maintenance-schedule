<?php

namespace Modules\MaintenanceCalendar\Actions;

use CController,
    CControllerResponseData;

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
        $maintenanceData = []; // Implemente aqui a lógica para obter os dados das manutenções agendadas
        
        $data = ['maintenance_data' => $maintenanceData];
        $response = new CControllerResponseData($data);
        $this->setResponse($response);
    }
}
