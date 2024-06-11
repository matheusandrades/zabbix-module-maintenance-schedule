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
        padding: 10;
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
</style>

<div id="calendarTitle">
    <div class="language-selector">
        <label for="language-select" id="languageLabel">Select Language:</label>
        <select id="language-select">
	    <option value="pt-br">Português</option>
            <option value="en">English</option>
        </select>
    </div>
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
            "dateFormat": "MMMM Do YYYY, h:mm:ss a"
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
            "dateFormat": "DD/MM/YYYY, HH:mm:ss"
        }
    };

    function applyTranslations(locale) {
        document.getElementById('statusPending').innerText = translations[locale].statusPending;
        document.getElementById('statusRunning').innerText = translations[locale].statusRunning;
        document.getElementById('statusExpired').innerText = translations[locale].statusExpired;
        document.getElementById('languageLabel').innerText = translations[locale].languageLabel;
        document.getElementById('popupMessage').innerText = translations[locale].popupMessage;
    }

    function determineEventColor(startDate, endDate) {
        const now = moment();
        const locale = document.getElementById('language-select').value;
        if (now.isBefore(startDate)) {
            return { color: '#FF0000', status: translations[locale].statusPending };
        } else if (now.isBetween(startDate, endDate)) {
            return { color: '#008000', status: translations[locale].statusRunning };
        } else {
            return { color: '#0000FF', status: translations[locale].statusExpired };
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
            var { color, status } = determineEventColor(startDate, endDate);
            var event = {
                title: title,
                start: startDate.toISOString(),
                end: endDate.toISOString(),
                description: description,
                maintenanceDescription: maintenance.description || '',
                coletandoDados: coletandoDados,
                hosts: hosts,
                backgroundColor: color,
                borderColor: color,
                status: status
            };
            events.push(event);

            if (status === translations[locale].statusRunning) {
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
                <p><strong>${translations[locale].hosts}:</strong> ${event.extendedProps.hosts}</p>
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
                        <p><strong>${translations[locale].hosts}:</strong> ${event.hosts}</p>
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

    function exportCSV() {
        var locale = document.getElementById('language-select').value;
        var maintenanceData = <?php echo json_encode($data['maintenances']); ?>;
        var csvContent = "data:text/csv;charset=utf-8,";
        csvContent += `${translations[locale].title},${translations[locale].start},${translations[locale].end},${translations[locale].hosts},${translations[locale].description},${translations[locale].dataCollection},${translations[locale].status}\n`;

        maintenanceData.forEach(function(maintenance) {
            var startDate = moment.unix(maintenance.start_date);
            var endDate = startDate.clone().add(maintenance.period, 'seconds');
            var title = `${maintenance.name || 'No name'} (${startDate.format(translations[locale].dateFormat)} - ${endDate.format(translations[locale].dateFormat)})`;
            var coletandoDados = maintenance.maintenance_type === "0" ? 'Yes' : 'No';
            var description = maintenance.description || '';
            var hosts = maintenance.hosts || '';
            var { status } = determineEventColor(startDate, endDate);
            csvContent += `${title},${startDate.format(translations[locale].dateFormat)},${endDate.format(translations[locale].dateFormat)},${hosts},${description},${coletandoDados},${status}\n`;
        });

        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "maintenance_schedule.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
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
</script>
