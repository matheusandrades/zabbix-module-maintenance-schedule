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
        background-color: #0a466a;
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
        margin-bottom: 10px;
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

    .details-row {
        display: none;
        background-color: #f9f9f9;
    }
 .delete-button {
    background-color: #ff0000; /* Cor vermelha */
    color: #ffffff; /* Texto branco */
    border: none;
    padding: 10;
    font-size: 14px;
    cursor: pointer;
    border-radius: 5px;
}

.delete-button:hover {
    background-color: #cc0000; /* Cor mais escura ao passar o mouse */
}
.edit-button {
    background-color: #007bff; /* Cor azul */
    color: #ffffff; /* Texto branco */
    border: none;
    padding: 10px 20px;
    font-size: 14px;
    cursor: pointer;
    border-radius: 5px;
    margin-left: 10px;
}

.edit-button:hover {
    background-color: #0056b3; /* Cor mais escura ao passar o mouse */
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

<div class="report-modal" id="reportModal">
    <span class="close" onclick="closeReport()">&times;</span>
    <h2 id="reportTitle">Relatório de Manutenções</h2>
    <div class="maintenance-table-container" id="maintenanceTableContainer">
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
            "maintenanceTitle": "Maintenance",
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
            "maintenanceTitle": "Manutenção",
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
            "reportButtonText": "Relatório",
            
        }
    };

    function applyTranslations(locale) {
        document.getElementById('statusPending').innerText = translations[locale].statusPending;
        document.getElementById('statusRunning').innerText = translations[locale].statusRunning;
        document.getElementById('statusExpired').innerText = translations[locale].statusExpired;
        document.getElementById('languageLabel').innerText = translations[locale].languageLabel;
        document.getElementById('popupMessage').innerText = translations[locale].popupMessage;
        document.getElementById('reportTitle').innerText = translations[locale].reportTitle;
        document.querySelector('.report-button').innerText = translations[locale].reportButtonText;
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
            var hosts = Array.isArray(maintenance.hosts) ? maintenance.hosts : (typeof maintenance.hosts === 'string' ? maintenance.hosts.split(',') : []);
            var { color, status, statusKey } = determineEventColor(startDate, endDate);
            var event = {
                id: maintenance.id,  // Adicionando ID da manutenção
                title: title,
                start: startDate.toISOString(),
                end: endDate.toISOString(),
                description: description,
                maintenanceDescription: maintenance.description || '',
                coletandoDados: coletandoDados,
                hosts: hosts,
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
    var status = event.extendedProps.status;

    $('#infoContent').html(`
        <div class="maintenance-info">
            <p class="maintenance-title">${translations[locale].maintenanceTitle} - ${status}</p>
            <p><strong>${translations[locale].title}:</strong> ${event.title}</p>
            <p><strong>${translations[locale].start}:</strong> ${start}</p>
            <p><strong>${translations[locale].end}:</strong> ${end}</p>
            <p><strong>${translations[locale].hosts}:</strong> ${event.extendedProps.hosts.join(', ')}</p>
            <p><strong>${translations[locale].description}:</strong> ${event.extendedProps.maintenanceDescription}</p>
            <p><strong>${translations[locale].dataCollection}:</strong> ${event.extendedProps.coletandoDados}</p>
            <p><strong>${translations[locale].status}:</strong> ${status}</p>
            <div id="error-message" style="color: red; display: none;"></div>
            <form id="deleteForm-${event.id}" onsubmit="deleteMaintenance(event, ${event.id})">
                <input type="hidden" name="maintenanceid" value="${event.id}">
                <button type="submit" id="deleteButton-${event.id}" class="delete-button">Deletar</button>
            </form>
        </div>
    `);

    $('.maintenance-details').css('right', '0');
}

function deleteMaintenance(event, maintenanceId) {
    event.preventDefault();
    var confirmation = confirm('Você tem certeza de que deseja deletar esta manutenção?');

    if (confirmation) {
        fetch('zabbix.php?action=maintenance.deletes', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ maintenanceid: maintenanceId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Manutenção deletada com sucesso.');
                location.reload();
            } else {
                document.getElementById('error-message').innerText = data.message;
                document.getElementById('error-message').style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            document.getElementById('error-message').innerText = 'Ocorreu um erro ao tentar deletar a manutenção.';
            document.getElementById('error-message').style.display = 'block';
        });
    }
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
                    <form method="post" action="zabbix.php?action=maintenance.deletes" onsubmit="return confirmDeletion()">
                        <input type="hidden" name="maintenanceid" value="${event.id}">
                        <button type="submit" id="deleteButton-${event.id}" class="delete-button">Deletar</button>
                    </form>
                </div>
            `;
        }).join('');
        $('#infoContent').html(infoContent);
        $('.maintenance-details').css('right', '0');
    }
}



function editMaintenance(id, title, start, end, hosts, description, dataCollection, status) {
    var locale = document.getElementById('language-select').value;

    $('#infoContent').html(`
        <div class="maintenance-info">
            <form id="editForm" method="post" action="zabbix.php?action=maintenance.update" onsubmit="convertDatesToUnix(event, '${id}')">
                <input type="hidden" name="maintenance[maintenanceid]" value="${id}">
                <label for="title-${id}">${translations[locale].title}:</label>
                <input type="text" id="title-${id}" name="maintenance[title]" value="${title}">
                
                <label for="start_date-${id}">${translations[locale].start}:</label>
                <input type="text" id="start_date-${id}" name="maintenance[start_date]" value="${start}">
                
                <label for="end-${id}">${translations[locale].end}:</label>
                <input type="text" id="end-${id}" name="maintenance[end]" value="${end}">
                
                <label for="hosts-${id}">${translations[locale].hosts}:</label>
                <input type="text" id="hosts-${id}" name="maintenance[hosts]" value="${hosts}">
                
                <label for="description-${id}">${translations[locale].description}:</label>
                <input type="text" id="description-${id}" name="maintenance[description]" value="${description}">
                
                <label for="dataCollection-${id}">${translations[locale].dataCollection}:</label>
                <input type="text" id="dataCollection-${id}" name="maintenance[dataCollection]" value="${dataCollection}">
                
                <label for="status-${id}">${translations[locale].status}:</label>
                <input type="text" id="status-${id}" name="maintenance[status]" value="${status}">
                
                <button type="submit" class="edit-button">Salvar</button>
            </form>
        </div>
    `);

    $('.maintenance-details').css('right', '0');
}

function convertDatesToUnix(event, id) {
    event.preventDefault();

    var form = document.getElementById('editForm');
    var startDateField = document.getElementById(`start_date-${id}`);
    var endDateField = document.getElementById(`end-${id}`);

    // Converta as datas para Unix timestamp
    var startDateUnix = moment(startDateField.value).unix();
    var endDateUnix = moment(endDateField.value).unix();

    startDateField.value = startDateUnix;
    endDateField.value = endDateUnix;

    form.submit();
}




function convertDatesToUnix(event, id) {
    event.preventDefault();

    var form = document.getElementById('editForm');
    var startDateField = document.getElementById(`start_date-${id}`);
    var endDateField = document.getElementById(`end-${id}`);

    // Converta as datas para Unix timestamp
    var startDateUnix = moment(startDateField.value).unix();
    var endDateUnix = moment(endDateField.value).unix();

    startDateField.value = startDateUnix;
    endDateField.value = endDateUnix;

    form.submit();
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-button').forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            var maintenanceId = this.querySelector('input[name="maintenanceid"]').value;
            var confirmation = confirm('Você tem certeza de que deseja deletar esta manutenção?');

            if (confirmation) {
                fetch('zabbix.php?action=maintenance.deletes', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ maintenanceid: maintenanceId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Manutenção deletada com sucesso.');
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Ocorreu um erro ao tentar deletar a manutenção.');
                });
            }
        });
    });
});

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

            var hosts = Array.isArray(maintenance.hosts) ? maintenance.hosts : (typeof maintenance.hosts === 'string' ? maintenance.hosts.split(',') : []);
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
                    <tr class="host-row" data-host="${host}">
                        <td>${host}</td>
                        <td>${hostMaintenanceData[host].total}</td>
                        <td>${hostMaintenanceData[host].expired}</td>
                        <td>${hostMaintenanceData[host].running}</td>
                        <td>${hostMaintenanceData[host].pending}</td>
                    </tr>
                    <tr class="details-row" id="details-${host}">
                        <td colspan="5">
                            <div class="details-content"></div>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>`;

        document.getElementById('maintenanceTableContainer').innerHTML = tableHtml;
        attachRowClickEvents();
    }

    function attachRowClickEvents() {
        document.querySelectorAll('.host-row').forEach(function(row) {
            row.addEventListener('click', function() {
                var host = this.getAttribute('data-host');
                var detailsRow = document.getElementById(`details-${host}`);
                if (detailsRow.style.display === 'none' || !detailsRow.style.display) {
                    showHostDetails(host, detailsRow.querySelector('.details-content'));
                    detailsRow.style.display = 'table-row';
                } else {
                    detailsRow.style.display = 'none';
                }
            });
        });
    }

    function showHostDetails(host, container) {
        var maintenances = <?php echo json_encode($data['maintenances']); ?>;
        var hostMaintenances = maintenances.filter(function(maintenance) {
            var hosts = Array.isArray(maintenance.hosts) ? maintenance.hosts : (typeof maintenance.hosts === 'string' ? maintenance.hosts.split(',') : []);
            return hosts.includes(host);
        });

        var locale = document.getElementById('language-select').value;
        var detailsHtml = hostMaintenances.map(function(maintenance) {
            var startDate = moment.unix(maintenance.start_date).format(translations[locale].dateFormat);
            var endDate = moment.unix(maintenance.start_date).add(maintenance.period, 'seconds').format(translations[locale].dateFormat);
            var status = determineEventColor(moment.unix(maintenance.start_date), moment.unix(maintenance.start_date).add(maintenance.period, 'seconds')).status;

            return `
                <div class="maintenance-info">
                    <p><strong>${translations[locale].title}:</strong> ${maintenance.name || 'No name'}</p>
                    <p><strong>${translations[locale].start}:</strong> ${startDate}</p>
                    <p><strong>${translations[locale].end}:</strong> ${endDate}</p>
                    <p><strong>${translations[locale].hosts}:</strong> ${Array.isArray(maintenance.hosts) ? maintenance.hosts.join(', ') : maintenance.hosts}</p>
                    <p><strong>${translations[locale].description}:</strong> ${maintenance.description || ''}</p>
                    <p><strong>${translations[locale].dataCollection}:</strong> ${maintenance.maintenance_type === "0" ? 'Yes' : 'No'}</p>
                    <p><strong>${translations[locale].status}:</strong> ${status}</p>
                </div>
            `;
        }).join('');

        container.innerHTML = detailsHtml;
    }

    function updateCharts(startDate, endDate) {
        renderMaintenanceTable(startDate, endDate);
    }
</script>
