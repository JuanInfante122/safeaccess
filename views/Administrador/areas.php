<?php
// Conexión a la base de datos
require_once('../../config/db.php');
$db = Database::connect();

// Manejar solicitudes AJAX
if (isset($_GET['area_id'])) {
    header('Content-Type: application/json');
    $area_id = intval($_GET['area_id']);

    // Personas en el área y su historial
    $peopleQuery = "SELECT e.emp_nombre, e.emp_apellidos, r.fecha_acceso, r.hora_salida 
                    FROM tbl_empleado e 
                    INNER JOIN tbl_registro_accesos r 
                    ON e.emp_documento = r.acceso_empleado 
                    WHERE r.acceso_area = $area_id";
    $peopleResult = $db->query($peopleQuery);
    $people = [];
    while ($row = $peopleResult->fetch_assoc()) {
        $people[] = $row;
    }

    // Aforo máximo y actual
    $aforoQuery = "SELECT area_aforo_max FROM tbl_area WHERE area_id = $area_id";
    $aforoResult = $db->query($aforoQuery);
    $aforoMax = $aforoResult->fetch_assoc()['area_aforo_max'];

    $currentQuery = "SELECT COUNT(*) AS empleados_presentes 
                     FROM tbl_registro_accesos 
                     WHERE acceso_area = $area_id AND hora_salida IS NULL";
    $currentResult = $db->query($currentQuery);
    $empleadosPresentes = $currentResult->fetch_assoc()['empleados_presentes'];

    echo json_encode([
        "people" => $people,
        "aforo_max" => $aforoMax,
        "aforo_actual" => $empleadosPresentes
    ]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Áreas</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f8f8;
            color: #333333;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        h2 {
            color: #222222;
            margin-bottom: 30px;
        }
        .area-card {
            cursor: pointer;
            margin: 10px;
            padding: 20px;
            background: #ffffff;
            border: 1px solid #ccc;
            border-radius: 10px;
            text-align: center;
            color: #333333;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, background 0.3s;
        }
        .area-card:hover {
            transform: scale(1.05);
            background: #f1f1f1;
        }
        .modal-content {
            background: #ffffff;
            color: #333333;
            border: 1px solid #ccc;
        }
        .modal-header, .modal-footer {
            border-color: #ddd;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        ul li {
            padding: 10px;
            background: #ffffff;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            color: #333333;
        }
        ul li:hover {
            background: #f1f1f1;
        }
        .time {
            font-weight: bold;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container text-center">
        <h2>Áreas Registradas</h2>
        <div class="row" id="areas-container">
            <?php
            $query = "SELECT area_id, area_nombre FROM tbl_area WHERE area_estado = 1";
            $result = $db->query($query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "
                    <div 
                        class='col-md-4 area-card' 
                        data-id='{$row['area_id']}' 
                        data-nombre='{$row['area_nombre']}'>
                        {$row['area_nombre']}
                    </div>";
                }
            } else {
                echo "<p class='text-center'>No hay áreas registradas.</p>";
            }
            ?>
        </div>
    </div>

    <div class="modal fade" id="areaModal" tabindex="-1" aria-labelledby="areaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="areaModalLabel">Detalles del Área</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="areaTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="tab-graph" data-bs-toggle="tab" data-bs-target="#tabGraphContent" type="button" role="tab">Gráfico de Aforo</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="tab-current" data-bs-toggle="tab" data-bs-target="#tabCurrentContent" type="button" role="tab">Personas en el Área</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="tab-history" data-bs-toggle="tab" data-bs-target="#tabHistoryContent" type="button" role="tab">Historial</button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tabGraphContent" role="tabpanel">
                            <canvas id="aforoChart"></canvas>
                        </div>
                        <div class="tab-pane fade" id="tabCurrentContent" role="tabpanel">
                            <ul id="currentList"></ul>
                        </div>
                        <div class="tab-pane fade" id="tabHistoryContent" role="tabpanel">
                            <ul id="historyList"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let aforoChart;

        document.addEventListener("DOMContentLoaded", () => {
            const container = document.getElementById("areas-container");

            container.addEventListener("click", event => {
                const target = event.target.closest(".area-card");
                if (target) {
                    const areaId = target.getAttribute("data-id");
                    const nombre = target.getAttribute("data-nombre");

                    openModal(areaId, nombre);
                }
            });

            setInterval(updateTimes, 1000); // Actualizar cada segundo
        });

        function openModal(areaId, nombre) {
            document.getElementById("areaModalLabel").textContent = nombre;

            fetch(`?area_id=${areaId}`)
                .then(response => response.json())
                .then(data => {
                    if (aforoChart) aforoChart.destroy();
                    const ctx = document.getElementById("aforoChart").getContext("2d");
                    aforoChart = new Chart(ctx, {
                        type: "pie",
                        data: {
                            labels: ["Ocupado", "Libre"],
                            datasets: [{
                                data: [data.aforo_actual, data.aforo_max - data.aforo_actual],
                                backgroundColor: ["#007bff", "#cccccc"]
                            }]
                        }
                    });

                    const currentList = document.getElementById("currentList");
                    currentList.innerHTML = "";
                    data.people.forEach(person => {
                        const li = document.createElement("li");
                        const startTime = new Date(person.fecha_acceso).toISOString();
                        const endTime = person.hora_salida ? new Date(person.hora_salida).toISOString() : null;

                        li.innerHTML = `
                            <strong>${person.emp_nombre} ${person.emp_apellidos}</strong> - 
                            <span class="time" data-start="${startTime}" data-end="${endTime || ""}">
                                Calculando...
                            </span>
                        `;
                        currentList.appendChild(li);
                    });

                    const historyList = document.getElementById("historyList");
                    historyList.innerHTML = "";
                    data.people.forEach(person => {
                        const li = document.createElement("li");
                        const duration = person.hora_salida
                            ? calculateDuration(new Date(person.fecha_acceso), new Date(person.hora_salida))
                            : "Actualmente en el área";
                        li.textContent = `${person.emp_nombre} ${person.emp_apellidos} - ${duration}`;
                        historyList.appendChild(li);
                    });
                });

            const modal = new bootstrap.Modal(document.getElementById("areaModal"));
            modal.show();
        }

        function updateTimes() {
            const timeElements = document.querySelectorAll('.time');
            timeElements.forEach(el => {
                const startTime = new Date(el.dataset.start);
                const endTime = el.dataset.end ? new Date(el.dataset.end) : new Date();
                const duration = calculateDuration(startTime, endTime);
                el.textContent = duration;
            });
        }

        function calculateDuration(start, end) {
            const diff = end - start;
            const hours = Math.floor(diff / 3600000);
            const minutes = Math.floor((diff % 3600000) / 60000);
            const seconds = Math.floor((diff % 60000) / 1000);
            return `${hours}h ${minutes}m ${seconds}s`;
        }
    </script>
</body>
</html>
