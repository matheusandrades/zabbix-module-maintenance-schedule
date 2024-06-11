<?php declare(strict_types = 0); ?>

<!-- Incluindo FullCalendar via CDN -->
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
        height: calc(100% - 100px);
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
        margin-bottom: 10px;
        border-radius: 5px;
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
        background-color: #FF0000;
    }

    .status-indicator .running {
        background-color: #008000;
    }

    .status-indicator .expired {
        background-color: #0000FF;
    }

    .title {
        flex-grow: 1;
        text-align: center;
    }

    .language-selector {
        margin-left: 20px;
        font-size: 0.8em;
    }

    .language-selector select {
        padding: 5px;
        font-size: 10px;
    }

    .export-button {
        margin-left: 20px;
        font-size: 12px;
        background-color: #fff;
        color: #0a466a;
        padding: 5;
        border: 1px solid #0a466a;
        border-radius: 3px;
        cursor: pointer;
    }

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
        cursor: pointer;
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
        50% {
            opacity: 0;
        }
    }

    .fc-event {
        color: #fff !important;
        padding: 2px 5px;
    }

    .fc-header-toolbar.fc-toolbar.fc-toolbar-ltr {
        margin-left: 100px !important;
    }

    #statusIndicators {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 10px;
    }

    #statusIndicators .status-indicator {
        margin: 0 20px;
    }

    /* Adicionando estilo para o botão de relatório */
    .report-button {
        margin-right: 20px;
        font-size: 12px;
        background-color: #fff;
        color: #0a466a;
        padding: 5;
        border: 1px solid #0a466a;
        border-radius: 3px;
        cursor: pointer;
    }

    /* Estilo para a janela modal de relatório */
    .report-modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #fff;
        border: 1px solid #0a466a;
        border-radius: 5px;
        padding: 20px;
        z-index: 1001;
        width: 80%;
        height: 80%;
        overflow: auto;
    }

    .report-modal .close {
        position: absolute;
        top: 10px;
        right: 10px;
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .report-modal h2 {
        margin-bottom: 10px; /* Reduzir o espaçamento abaixo do título */
    }

    .maintenance-table-container {
        overflow-x: auto;
    }

    .maintenance-table {
        width: 100%;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
    }

    .maintenance-table th, .maintenance-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .maintenance-table th {
        background-color: #0a466a;
        color: white;
    }

    .maintenance-table tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .status-label {
        padding: 5px 10px;
        border-radius: 5px;
        color: #fff;
        display: inline-block;
    }

    .status-label.pending {
        background-color: #FF0000;
    }

    .status-label.running {
        background-color: #008000;
    }

    .status-label.expired {
        background-color: #0000FF;
    }

    .maintenance-table-container {
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        background: #f9f9f9;
    }
</style>

<div id="calendarTitle">
    <div class="language-selector">
        <label for="language-select" id="languageLabel">Select Language:</label>
        <select id="language-select">
            <option value="pt-br">Português</option>
            <option value="en">English</option>
        </select>
    </div>
    <button class="report-button" onclick="showReport()">Relatório</button>
</div>
<div id="calendar"></div>

<div id="statusIndicators">
    <div class="status-indicator">
        <div class="pending"></div> <span id="statusPending">Pendente</span>
    </div>
    <div class="status-indicator">
        <div class="running"></div> <span id="statusRunning">Em execução</span>
    </div>
    <div class="status-indicator">
        <div class="expired"></div> <span id="statusExpired">Expirada</span>
    </div>
</div>

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

<!-- Modal para o relatório -->
<div class="report-modal" id="reportModal">
    <span class="close" onclick="closeReport()">&times;</span>
    <h2 id="reportTitle">Relatório de Manutenções</h2>
    <div class="maintenance-table-container" id="maintenanceTableContainer">
        <!-- Tabela será inserida aqui -->
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
    var calendar;
    var runningMaintenances = [];
    var translations = {
        "en": {
            "statusPending": "Pending",
            "statusRunning": "Running",
            "statusExpired": "Expired",
            "mainTitle": "Maintenance Calendar",
            "languageLabel": "Select Language:",
            "popupMessage": "There is ongoing maintenance!",
            "maintenanceTitle": "Maintenance in Progress",
            "title": "Title",
            "start": "Start",
            "end": "End",
            "hosts": "Hosts in Maintenance",
            "description": "Description",
            "dataCollection": "Data Collection",
            "status": "Status",
            "reportTitle": "Maintenance Report",
            "statusChartTitle": "Maintenance by Status",
            "hostChartTitle": "Maintenance by Hosts",
            "dateFormat": "MMMM Do YYYY, h:mm:ss a",
            "hostTableTitle": "Maintenance by Host",
	    "hostTableColumns": ["Host Name", "Total Maintenance", "Expired Maintenance", "Ongoing Maintenance", "Pending Maintenance"],
	    "reportButtonText": "Report"
        },
        "pt-br": {
            "statusPending": "Pendente",
            "statusRunning": "Em execução",
            "statusExpired": "Expirada",
            "mainTitle": "Calendário de Manutenção",
            "languageLabel": "Selecionar idioma:",
            "popupMessage": "Há uma manutenção em andamento!",
            "maintenanceTitle": "Manutenção em Andamento",
            "title": "Título",
            "start": "Início",
            "end": "Fim",
            "hosts": "Hosts em Manutenção",
            "description": "Descrição",
            "dataCollection": "Coleta de Dados",
            "status": "Status",
            "reportTitle": "Relatório de Manutenções",
            "statusChartTitle": "Manutenções por Status",
            "hostChartTitle": "Manutenções por Hosts",
            "dateFormat": "DD/MM/YYYY, HH:mm:ss",
            "hostTableTitle": "Manutenção por Host",
	    "hostTableColumns": ["Nome do Host", "Total de Manutenção", "Manutenção Expirada", "Manutenção em Andamento", "Manutenção Pendente"],
	    "reportButtonText": "Relatório"
        }
    };

function applyTranslations(locale) {
    document.getElementById('statusPending').innerText = translations[locale].statusPending;
    document.getElementById('statusRunning').innerText = translations[locale].statusRunning;
    document.getElementById('statusExpired').innerText = translations[locale].statusExpired;
    document.getElementById('languageLabel').innerText = translations[locale].languageLabel;
    document.getElementById('popupMessage').innerText = translations[locale].popupMessage;
    document.getElementById('reportTitle').innerText = translations[locale].reportTitle;
    document.querySelector('.report-button').innerText = translations[locale].reportButtonText;  // Adicionado
}

    function determineEventColor(startDate, endDate) {
        const now = moment();
        const locale = document.getElementById('language-select').value;
        if (now.isBefore(startDate)) {
            return { color: '#FF0000', status: translations[locale].statusPending, statusKey: 'pending' };
        } else if (now.isBetween(startDate, endDate)) {
            return { color: '#008000', status: translations[locale].statusRunning, statusKey: 'running' };
        } else {
            return { color: '#0000FF', status: translations[locale].statusExpired, statusKey: 'expired' };
        }
    }

    function initializeCalendar(maintenanceData, locale) {
    var events = [];
    var ongoingMaintenanceCount = 0;
    runningMaintenances = [];

    maintenanceData.forEach(function(maintenance) {
        var startDate = moment.unix(maintenance.start_date);
        var endDate = startDate.clone().add(maintenance.period, 'seconds');
        var title = `${maintenance.name || 'No name'} (${startDate.format(translations[locale].dateFormat)} - ${endDate.format(translations[locale].dateFormat)})`;
        var coletandoDados = maintenance.maintenance_type === "0" ? 'Yes' : 'No';
        var description = maintenance.description || '';
        var hosts = maintenance.hosts || '';
        var { color, status, statusKey } = determineEventColor(startDate, endDate);
        var event = {
            title: title,
            start: startDate.toISOString(),
            end: endDate.toISOString(),
            description: description,
            maintenanceDescription: maintenance.description || '',
            coletandoDados: coletandoDados,
            hosts: Array.isArray(hosts) ? hosts : hosts.split(','),
            backgroundColor: color,
            borderColor: color,
            status: status,
            statusKey: statusKey
        };
        events.push(event);

        if (statusKey === 'running') {
            runningMaintenances.push(event);
            ongoingMaintenanceCount++;
        }
    });

    if (ongoingMaintenanceCount > 0) {
        var popupMessage = `${translations[locale].popupMessage} (${ongoingMaintenanceCount})`;
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
        datesSet: function(info) {
            fetchMaintenanceData().then(function(maintenanceData) {
                updateCharts(info.start, info.end, maintenanceData);
            });
        },
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
        var locale = document.getElementById('language-select').value;
        var start = event.start ? moment(event.start).locale(locale).format(translations[locale].dateFormat) : '';
        var end = event.end ? moment(event.end).locale(locale).format(translations[locale].dateFormat) : '';
        $('#infoContent').html(`
            <div class="maintenance-info">
                <p class="maintenance-title">${translations[locale].maintenanceTitle}</p>
                <p><strong>${translations[locale].title}:</strong> ${event.title}</p>
                <p><strong>${translations[locale].start}:</strong> ${start}</p>
                <p><strong>${translations[locale].end}:</strong> ${end}</p>
                <p><strong>${translations[locale].hosts}:</strong> ${event.extendedProps.hosts.join(', ')}</p>
                <p><strong>${translations[locale].description}:</strong> ${event.extendedProps.maintenanceDescription}</p>
                <p><strong>${translations[locale].dataCollection}:</strong> ${event.extendedProps.coletandoDados}</p>  
                <p><strong>${translations[locale].status}:</strong> ${event.extendedProps.status}</p>
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
        var locale = document.getElementById('language-select').value;
        if (runningMaintenances.length > 0) {
            var infoContent = runningMaintenances.map(function(event, index) {
                var start = moment(event.start).locale(locale).format(translations[locale].dateFormat);
                var end = moment(event.end).locale(locale).format(translations[locale].dateFormat);
                return `
                    <div class="maintenance-info">
                        <p class="maintenance-title">${translations[locale].maintenanceTitle} ${index + 1}</p>
                        <p><strong>${translations[locale].title}:</strong> ${event.title}</p>
                        <p><strong>${translations[locale].start}:</strong> ${start}</p>
                        <p><strong>${translations[locale].end}:</strong> ${end}</p>
                        <p><strong>${translations[locale].hosts}:</strong> ${event.hosts.join(', ')}</p>
                        <p><strong>${translations[locale].description}:</strong> ${event.maintenanceDescription}</p>
                        <p><strong>${translations[locale].dataCollection}:</strong> ${event.coletandoDados}</p>  
                        <p><strong>${translations[locale].status}:</strong> ${event.status}</p>
                    </div>
                `;
            }).join('');
            $('#infoContent').html(infoContent);
            $('.maintenance-details').css('right', '0');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        var languageSelect = document.getElementById('language-select');
        languageSelect.addEventListener('change', function() {
            var locale = languageSelect.value;
            applyTranslations(locale);
            fetchMaintenanceData()
                .then(function(maintenanceData) {
                    var locale = languageSelect.value;
                    initializeCalendar(maintenanceData, locale);
                })
                .catch(function(error) {
                    console.error("Error fetching scheduled maintenance: " + error);
                });
        });

        var maintenances = <?php echo json_encode($data['maintenances']); ?>;
        var locale = languageSelect.value;
        applyTranslations(locale);
        initializeCalendar(maintenances, locale);
    });

    function fetchMaintenanceData() {
        // Função simulada para buscar dados de manutenção
        return new Promise(function(resolve, reject) {
            resolve(<?php echo json_encode($data['maintenances']); ?>);
        });
    }

    function showReport() {
        document.getElementById('reportModal').style.display = 'block';
        renderMaintenanceTable();
    }

    function closeReport() {
        document.getElementById('reportModal').style.display = 'none';
    }

    function renderMaintenanceTable(startDate = null, endDate = null) {
    var maintenances = <?php echo json_encode($data['maintenances']); ?>;
    var hostMaintenanceData = {};

    maintenances.forEach(function(maintenance) {
        var start = moment.unix(maintenance.start_date);
        var end = start.clone().add(maintenance.period, 'seconds');

        if (startDate && endDate && (start.isBefore(startDate) || end.isAfter(endDate))) {
            return;
        }

        var status = determineEventColor(start, end).statusKey;

        var hosts = Array.isArray(maintenance.hosts) ? maintenance.hosts : maintenance.hosts.split(',');
        hosts.forEach(function(host) {
            if (!hostMaintenanceData[host]) {
                hostMaintenanceData[host] = { total: 0, pending: 0, running: 0, expired: 0 };
            }
            hostMaintenanceData[host].total++;
            if (status === 'pending') {
                hostMaintenanceData[host].pending++;
            } else if (status === 'running') {
                hostMaintenanceData[host].running++;
            } else if (status === 'expired') {
                hostMaintenanceData[host].expired++;
            }
        });
    });

    var locale = document.getElementById('language-select').value;
    var tableHtml = `<table class="maintenance-table">
        <thead>
            <tr>
                ${translations[locale].hostTableColumns.map(col => `<th>${col}</th>`).join('')}
            </tr>
        </thead>
        <tbody>
            ${Object.keys(hostMaintenanceData).map(host => `
                <tr>
                    <td>${host}</td>
                    <td>${hostMaintenanceData[host].total}</td>
                    <td>${hostMaintenanceData[host].expired}</td>
                    <td>${hostMaintenanceData[host].running}</td>
                    <td>${hostMaintenanceData[host].pending}</td>
                </tr>
            `).join('')}
        </tbody>
    </table>`;

    document.getElementById('maintenanceTableContainer').innerHTML = tableHtml;
}


    function updateCharts(startDate, endDate) {
    renderMaintenanceTable(startDate, endDate);
}

</script>
