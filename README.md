# zabbix-module-maintenance-schedule

Para utilizar o modulo basta clona o repositorio no path do seu servidor zabbix **/usr/share/zabbix/modules/**.
Apos isso é necessario gerar um token da api do zabbix e alterar dentro do arquivo **/usr/share/zabbix/modules/zabbix-module-maintenance-schedule/views/maintenance.calendar.php** no campo "auth" e o **endpoint** localizado dentro do codigo, tenho ideia de utilizar os metodos do proprio zabbix para buscar as informações de manutenção lembrando que ele vai mostrar de acordo com o periodo da manutenção e nao da criação da manutenção em si, por falta de tempo talvez nao consigue atualizar tao rapido este codigo para realizar melhoraria mas fica aqui minha contribuição e em caso de duvidas estou a disposição.

![Calendario de Manutenção](/img/photo_5129936469539007683_w.jpg)
