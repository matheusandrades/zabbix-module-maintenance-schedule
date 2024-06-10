<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Calendar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/daygrid/main.min.css' rel='stylesheet' />
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/timegrid/main.min.css' rel='stylesheet' />
    <style>
        .fc-time {
            display: none !important;
        }

        html, body {
            margin: 0;
            padding: 0;
            height: 100%; 
        }

        #calendar {
            width: 80%; 
            margin: 0 auto; 
            height: calc(100% - 60px); 
        }
    
        .maintenance-details {
            position: fixed;
            top: 0;
            right: -50%;
            width: 50%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            transition: right 0.3s ease;
            z-index: 999;
        }
        .maintenance-details-content {
            background-color: #fff;
            height: 100%;
            overflow-y: auto;
            padding: 20px;
        }
        .maintenance-details-header {
            font-weight: bold;
            font-size: 1.2em;
            margin-bottom: 10px;
        }
        .maintenance-title {
            background-color: #333;
            color: #fff;
            padding: 10px;
            font-weight: bold;
            font-size: 1.2em;
        }
        .maintenance-info {
            background-color: #666;
            color: #fff;
            padding: 20px;
            margin-bottom: 10px; /* Adicionando espaçamento entre blocos */
            border-radius: 5px; /* Adicionando bordas arredondadas para cada bloco */
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        #calendarTitle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #0a466a;
            color: #fff;
            padding: 10px;
            font-weight: bold;
            font-size: 1.5em;
        }
        .status-indicator {
            display: flex;
            align-items: center;
        }
        .status-indicator div {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            margin-right: 5px;
        }
        .status-indicator .pending {
            background-color: #FF0000; /* Vermelho */
        }
        .status-indicator .running {
            background-color: #008000; /* Verde */
        }
        .status-indicator .expired {
            background-color: #0000FF; /* Azul */
        }
        .title {
            flex-grow: 1;
            text-align: center;
        }
        .language-selector {
            margin-left: 20px;
            font-size: 0.8em; /* Reduzindo o tamanho da fonte */
        }
        .language-selector select {
            padding: 5px;
            font-size: 10px;
        }
        /* Estilo para o popup */
        .popup {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #2c8100;
            color: #fff;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            cursor: pointer; /* Adicionando cursor pointer */
        }
        .popup.show {
            display: block;
        }
        .popup .close-popup {
            float: right;
            color: #fff;
            cursor: pointer;
            margin-left: 10px;
        }
        .popup.blinking {
            animation: blinker 5s linear infinite;
        }
        @keyframes blinker {  
            50% { opacity: 0; }
        }
        .fc-event {
            color: #fff !important;
            padding: 2px 5px;
        }
        .fc-header-toolbar.fc-toolbar.fc-toolbar-ltr {
            margin-left: 100px !important;
        }
    </style>
</head>
<body>
    <div id="calendarTitle">
        <div class="status-indicator">
            <div class="pending"></div> Pendente
            <div class="running"></div> Em execução
            <div class="expired"></div> Expirada
        </div>
        <div class="title">Maintenance Calendar</div>
        <div class="language-selector">
            <label for="language-select">Select Language:</label>
            <select id="language-select">
                <option value="en">English</option>
                <option value="pt-br">Português</option>
            </select>
        </div>
    </div>
    <div id="calendar"></div>

    <div class="maintenance-details">
        <div class="maintenance-details-content">
            <span class="close" onclick="closeDetails()">&times;</span>
            <div id="infoContent"></div>
        </div>
    </div>

    <div class="popup" id="maintenancePopup" onclick="showRunningMaintenances()">
        <span id="popupMessage">Há uma manutenção em andamento!</span>
        <span class="close-popup" onclick="closePopup()">&times;</span>
    </div>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/daygrid/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/timegrid/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/interaction/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/moment/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script>
        var calendar;
        var runningMaintenances = [];

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
            if (now.isBefore(startDate)) {
                return { color: '#FF0000', status: 'Pendente' }; // Vermelho
            } else if (now.isBetween(startDate, endDate)) {
                return { color: '#008000', status: 'Em execução' }; // Verde
            } else {
                return { color: '#0000FF', status: 'Expirada' }; // Azul
            }
        }

        function initializeCalendar(maintenanceData, locale) {
            var events = [];
            var ongoingMaintenanceCount = 0;
            runningMaintenances = [];

            maintenanceData.forEach(function(maintenance) {
                maintenance.timeperiods.forEach(function(period) {
                    var startDate = moment.unix(period.start_date);
                    var endDate = moment.unix(period.start_date).add(period.period, 'seconds');
                    var title = `${maintenance.name || 'No name'} (${startDate.format('DD/MM/YYYY')} - ${endDate.format('DD/MM/YYYY')})`;
                    var coletandoDados = maintenance.maintenance_type === "0" ? 'Yes' : 'No';
                    var description = maintenance.hosts.length > 0 ? maintenance.hosts.map(host => host.name).join(', ') : '';
                    var { color, status } = determineEventColor(startDate, endDate);
                    var event = {
                        title: title,
                        start: startDate.toISOString(),
                        end: endDate.toISOString(),
                        description: description,
                        maintenanceDescription: maintenance.description || '',
                        coletandoDados: coletandoDados,
                        backgroundColor: color,
                        borderColor: color,
                        status: status
                    };
                    events.push(event);

                    if (status === 'Em execução') {
                        runningMaintenances.push(event);
                        ongoingMaintenanceCount++;
                    }
                });
            });

            if (ongoingMaintenanceCount > 0) {
                var popupMessage = `Há ${ongoingMaintenanceCount} manutenção(ões) em andamento!`;
                var popupElement = document.getElementById('maintenancePopup');
                document.getElementById('popupMessage').innerText = popupMessage;
                popupElement.classList.add('show', 'blinking');
            }

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
                    info.el.style.backgroundColor = info.event.backgroundColor;
                    info.el.style.borderColor = info.event.borderColor;
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
                    <p><strong>Status:</strong> ${event.extendedProps.status}</p>
                </div>
            `);
            $('.maintenance-details').css('right', '0'); 
        }

        function closeDetails() {
            $('.maintenance-details').css('right', '-50%'); 
        }

        function closePopup() {
            var popupElement = document.getElementById('maintenancePopup');
            popupElement.classList.remove('show', 'blinking');
        }

        function showRunningMaintenances() {
            if (runningMaintenances.length > 0) {
                var infoContent = runningMaintenances.map(function(event, index) {
                    var start = moment(event.start).format('MMMM Do YYYY, h:mm:ss a');
                    var end = moment(event.end).format('MMMM Do YYYY, h:mm:ss a');
                    return `
                        <div class="maintenance-info">
                            <p class="maintenance-title">Manutenção em Andamento ${index + 1}</p>
                            <p><strong>Title:</strong> ${event.title}</p>
                            <p><strong>Start:</strong> ${start}</p>
                            <p><strong>End:</strong> ${end}</p>
                            <p><strong>Hosts in Maintenance:</strong> ${event.description}</p>
                            <p><strong>Description:</strong> ${event.maintenanceDescription}</p>
                            <p><strong>Data Collection:</strong> ${event.coletandoDados}</p>  
                            <p><strong>Status:</strong> ${event.status}</p>
                        </div>
                    `;
                }).join('');
                $('#infoContent').html(infoContent);
                $('.maintenance-details').css('right', '0');
            }
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

