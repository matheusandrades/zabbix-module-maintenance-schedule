<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Calendar</title>
    <!-- Inclua os estilos do FullCalendar v5 -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/daygrid/main.min.css' rel='stylesheet' />
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/timegrid/main.min.css' rel='stylesheet' />
    <style>
        .fc-time {
            display: none !important;
        }
        /* Estilos para ocupar toda a tela */
        html, body {
            margin: 0;
            padding: 0;
            height: 100%; /* Ajuste para 100% para ocupar toda a tela */
        }
        /* Estilos para o calendário */
        #calendar {
            width: 80%; /* Redimensiona o calendário para 80% da largura da tela */
            margin: 0 auto; /* Centraliza o calendário na tela */
            height: calc(100% - 60px); /* Ajusta a altura do calendário */
        }
        /* Estilos para a janela modal */
        .maintenance-details {
            position: fixed;
            top: 0;
            right: -50%; /* Inicia fora da tela */
            width: 50%; /* Largura da seção de detalhes */
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Fundo semi-transparente */
            transition: right 0.3s ease; /* Transição suave ao abrir e fechar */
            z-index: 999; /* Coloca a seção acima do calendário */
        }
        .maintenance-details-content {
            background-color: #fff; /* Fundo branco para o conteúdo */
            height: 100%;
            overflow-y: auto; /* Adiciona barra de rolagem vertical se necessário */
            padding: 20px;
        }
        .maintenance-details-header {
            font-weight: bold; /* Título em negrito */
            font-size: 1.2em; /* Tamanho da fonte aumentado */
            margin-bottom: 10px; /* Espaçamento inferior */
        }
        .maintenance-title {
            background-color: #333; /* Fundo escuro para o título */
            color: #fff; /* Texto branco */
            padding: 10px;
            font-weight: bold;
            font-size: 1.2em;
        }
        .maintenance-info {
            background-color: #666; /* Fundo mais escuro para as informações */
            color: #fff; /* Texto branco */
            padding: 20px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        #calendarTitle {
            text-align: center;
            background-color: #076699; /* Fundo escuro para o título */
            color: #fff; /* Texto branco */
            padding: 10px;
            font-weight: bold;
            font-size: 1.5em; /* Tamanho da fonte aumentado */
        }
        /* Estilos para o seletor de idioma */
        .language-selector {
            text-align: center;
            margin: 10px 0;
        }
        .language-selector select {
            padding: 5px;
            font-size: 1em;
        }
    </style>
</head>
<body>
    <div id="calendarTitle">Maintenance Calendar</div>
    <div class="language-selector">
        <label for="language-select">Select Language:</label>
        <select id="language-select">
            <option value="en">English</option>
            <option value="pt-br">Português</option>
        </select>
    </div>
    <div id="calendar"></div>

    <div class="maintenance-details">
        <div class="maintenance-details-content">
            <span class="close" onclick="closeDetails()">&times;</span>
            <div class="maintenance-title">Event Details</div>
            <div class="maintenance-info" id="infoContent"></div>
        </div>
    </div>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/daygrid/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/timegrid/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/interaction/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/moment/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script>
        function fetchMaintenanceData() {
            return new Promise(function(resolve, reject) {
                var requestData = {
                    jsonrpc: "2.0",
                    method: "maintenance.get",
                    params: {
                        output: "extend",
                        selectHostGroups: "extend",
                        selectHosts: "extend",
                        selectTimeperiods: "extend",
                        selectTags: "extend"
                    },
                    auth: "b0cfaa1c8d4f323c03a70aca86644f91b386fffbd0e33734a2b9f6a279d96653",
                    id: 1
                };

                // Fazer a chamada à API do Zabbix
                $.ajax({
                    url: "http://127.0.0.1:8080/api_jsonrpc.php",
                    method: "POST",
                    contentType: "application/json",
                    data: JSON.stringify(requestData),
                    success: function(response) {
                        if (response.result) {
                            resolve(response.result);
                        } else {
                            reject("Error fetching scheduled maintenance.");
                        }
                    },
                    error: function(error) {
                        console.error("Error in Zabbix API call: " + JSON.stringify(error));
                        reject("Error fetching scheduled maintenance.");
                    }
                });
            });
        }

        function determineEventColor(startDate, endDate) {
            const now = moment();
            if (now.isBetween(startDate, endDate)) {
                const timeToEnd = endDate.diff(now, 'minutes');
                if (timeToEnd <= 20) {
                    return '#FFA500'; // Laranja
                } else {
                    return '#008000'; // Verde
                }
            } else {
                return '#FF0000'; // Vermelho
            }
        }

        var calendar;

        function initializeCalendar(maintenanceData, locale) {
            var events = [];

            maintenanceData.forEach(function(maintenance) {
                maintenance.timeperiods.forEach(function(period) {
                    var startDate = moment.unix(period.start_date);
                    var endDate = moment.unix(period.start_date).add(period.period, 'seconds');
                    var title = `${maintenance.name || 'No name'} (${startDate.format('L')} - ${endDate.format('L')})`;
                    var coletandoDados = maintenance.maintenance_type === "0" ? 'Yes' : 'No';
                    var description = maintenance.hosts.length > 0 ? maintenance.hosts.map(host => host.name).join(', ') : '';
                    var backgroundColor = determineEventColor(startDate, endDate);
                    events.push({
                        title: title,
                        start: startDate.toISOString(),
                        end: endDate.toISOString(),
                        description: description,
                        maintenanceDescription: maintenance.description || '',
                        coletandoDados: coletandoDados,
                        backgroundColor: backgroundColor
                    });
                });
            });

            var calendarEl = document.getElementById('calendar');
            if (calendar) {
                calendar.destroy();
            }
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: locale,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: events,
                eventClick: function(info) {
                    openDetails(info.event); 
                },
                eventDidMount: function(info) {
                    if (info.el.querySelector('.fc-list-event-title')) {
                        info.el.querySelector('.fc-list-event-title').style.backgroundColor = info.event.backgroundColor;
                    }
                },
                dayMaxEvents: true, 
                height: '100%', 
                expandRows: true 
            });

            calendar.render();
        }

        function openDetails(event) {
            var start = event.start ? moment(event.start).format('MMMM Do YYYY, h:mm:ss a') : '';
            var end = event.end ? moment(event.end).format('MMMM Do YYYY, h:mm:ss a') : '';
            $('#infoContent').html(`
                <div class="maintenance-info">
                    <p><strong>Title:</strong> ${event.title}</p>
                    <p><strong>Start:</strong> ${start}</p>
                    <p><strong>End:</strong> ${end}</p>
                    <p><strong>Hosts in Maintenance:</strong> ${event.extendedProps.description}</p>
                    <p><strong>Description:</strong> ${event.extendedProps.maintenanceDescription}</p>
                    <p><strong>Data Collection:</strong> ${event.extendedProps.coletandoDados}</p>  
                </div>
            `);
            $('.maintenance-details').css('right', '0'); // Exibe a seção de detalhes movendo-a para a direita
        }

        function closeDetails() {
            $('.maintenance-details').css('right', '-50%'); // Oculta a seção de detalhes movendo-a para fora da tela
        }

        document.addEventListener('DOMContentLoaded', function() {
            fetchMaintenanceData()
                .then(function(maintenanceData) {
                    var locale = document.getElementById('language-select').value;
                    initializeCalendar(maintenanceData, locale);
                })
                .catch(function(error) {
                    console.error("Error fetching scheduled maintenance: " + error);
                });

            document.getElementById('language-select').addEventListener('change', function() {
                fetchMaintenanceData()
                    .then(function(maintenanceData) {
                        var locale = document.getElementById('language-select').value;
                        initializeCalendar(maintenanceData, locale);
                    })
                    .catch(function(error) {
                        console.error("Error fetching scheduled maintenance: " + error);
                    });
            });
        });
    </script>
</body>
</html>

