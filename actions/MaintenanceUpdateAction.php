<?php

namespace Modules\Calendario\Actions;

use CController;
use CControllerResponseData;
use API;

class MaintenanceUpdateAction extends CController {

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
        // Certifique-se de que nenhum dado é enviado ao navegador antes do redirecionamento
        ob_start();

        $data = $_POST['maintenance'];

        // Verifique se as chaves existem no array $data
        if (!isset($data['maintenanceid'], $data['title'], $data['start_date'], $data['end'], $data['hosts'], $data['description'], $data['period'])) {
            ob_end_clean();
            throw new \Exception('Missing required fields');
        }

        // Processar os dados recebidos
        $maintenanceId = $data['maintenanceid'];
        $title = $data['title'];
        $start_date = intval($data['start_date']);
        $end = intval($data['end']);
        $period = intval($data['period']);
        $hosts = json_decode($data['hosts'], true); // Hosts já devem estar no formato adequado
        $description = $data['description'];
        $dataCollection = isset($data['dataCollection']) ? $data['dataCollection'] : '';
        $status = isset($data['status']) ? $data['status'] : '';

        // Criar os parâmetros para a requisição de atualização
        $params = [
            'maintenanceid' => $maintenanceId,
            'name' => $title,
            'active_since' => $start_date,
            'active_till' => $end,
            'description' => $description,
            'hosts' => array_map(function($host) {
                return ['hostid' => trim($host['hostid'])];
            }, $hosts),
            'timeperiods' => [
                [
                    'timeperiod_type' => 0,
                    'start_date' => $start_date,
                    'period' => $period
                ]
            ]
        ];

        // Adicionar logging para depuração
        error_log("Updating maintenance with params: " . json_encode($params));

        $result = API::Maintenance()->update($params);

        if (!$result) {
            error_log("Failed to update maintenance. API response: " . json_encode($result));
            ob_end_clean();
            throw new \Exception('Failed to update maintenance');
        }

        ob_end_clean();
        $this->setResponse(new CControllerResponseData(['status' => 'success']));
        header('Location: /zabbix.php?action=maintenance.calendar');
        exit();
    }
}

?>
p
