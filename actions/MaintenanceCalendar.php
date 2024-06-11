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
            'output' => ['maintenanceid', 'name', 'maintenance_type', 'active_since', 'active_till', 'description'],
            'selectTimeperiods' => ['timeperiod_type', 'period', 'start_date'],
            'selectHosts' => ['hostid', 'name']
        ]);

        $maintenances = [];
        foreach ($response as $maintenance) {
            $hosts = array_map(function($host) {
                return $host['name'];
            }, $maintenance['hosts']);
            
            foreach ($maintenance['timeperiods'] as $timeperiod) {
                $start_date = $timeperiod['start_date'];
                $period = $timeperiod['period'];
                $maintenances[] = [
                    'id' => $maintenance['maintenanceid'],
                    'name' => $maintenance['name'],
                    'start_date' => $start_date,
                    'period' => $period,
                    'description' => $maintenance['description'],
                    'hosts' => implode(', ', $hosts),
                    'maintenance_type' => $maintenance['maintenance_type']
                ];
            }
        }

        return $maintenances;
    }
}
?>
