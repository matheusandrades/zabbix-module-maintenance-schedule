<?php

namespace Modules\Calendario\Actions;

use CController;
use CControllerResponseData;
use API;

class MaintenanceDeleteAction extends CController {

    public function init(): void {
        $this->disableCsrfValidation();
    }

    protected function checkInput(): bool {
        return true;
    }

    protected function checkPermissions(): bool {
        // Adicione a lógica de verificação de permissões aqui
        // Retornar false se o usuário não tiver permissão para deletar
        return $this->hasDeletePermission();
    }

    protected function doAction(): void {
        if (!$this->checkPermissions()) {
            $this->sendJsonResponse(['status' => 'error', 'message' => 'Você não tem permissão para deletar esta manutenção.']);
            return;
        }

        $maintenanceId = $_POST['maintenanceid'];

        try {
            $result = API::Maintenance()->delete([$maintenanceId]);

            if (!$result) {
                throw new \Exception('Falha ao deletar a manutenção ou Usuario sem permissão');
            }

            header('Location: /zabbix.php?action=maintenance.calendar');
            exit();
        } catch (\Exception $e) {
            $this->sendJsonResponse(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    private function hasDeletePermission(): bool {
        // Implementar a lógica para verificar se o usuário tem permissão para deletar
        // Exemplo fictício:
        // return (bool) $this->getUser()->hasPermission('delete_maintenance');
        return true; // Supondo que todos os usuários têm permissão
    }

    private function sendJsonResponse(array $data): void {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}

?>

