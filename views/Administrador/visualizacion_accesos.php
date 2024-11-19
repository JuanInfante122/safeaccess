<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Accesos</title>
    <style>
        /* Reset básico */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            font-size: 2rem;
            color: #007bff;
            margin-bottom: 20px;
        }

        .filters {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }

        .filters input, .filters select, .filters button {
            flex: 1;
            min-width: 150px;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ced4da;
            border-radius: 8px;
            outline: none;
            transition: all 0.3s ease;
        }

        .filters input:focus, .filters select:focus, .filters button:hover {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .filters button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
            flex: none;
            max-width: 150px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 0.9rem;
        }

        table thead {
            background-color: #007bff;
            color: white;
        }

        table thead th {
            text-transform: uppercase;
            padding: 12px 10px;
        }

        table tbody tr {
            border-bottom: 1px solid #dee2e6;
        }

        table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        table tbody td {
            padding: 12px 10px;
            text-align: center;
        }

        table tbody tr:hover {
            background-color: #e9ecef;
        }

        .no-data {
            text-align: center;
            font-size: 1.1rem;
            color: #6c757d;
        }

        /* Estilos de estados */
        .entry {
            color: #28a745;
            font-weight: bold;
        }

        .exit {
            color: #dc3545;
            font-weight: bold;
        }

        .hidden {
            display: none;
        }

        /* Responsividad */
        @media (max-width: 768px) {
            .filters {
                flex-direction: column;
            }

            .filters input, .filters select, .filters button {
                flex: none;
                max-width: 100%;
            }

            table {
                font-size: 0.8rem;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.5rem;
            }

            table thead th, table tbody td {
                padding: 8px 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Registro de Accesos</h1>

        <div class="filters">
            <input type="text" id="documentFilter" placeholder="Buscar por documento">
            <select id="areaFilter">
                <option value="">Seleccionar área</option>
                <?php
                // Conexión para obtener áreas
                $conn = new mysqli("localhost", "root", "", "safeacces");
                $conn->query("SET lc_time_names = 'es_ES'");

                $areas = $conn->query("SELECT area_id, area_nombre FROM tbl_area WHERE area_estado = 1");
                while ($area = $areas->fetch_assoc()) {
                    echo "<option value='{$area['area_id']}'>{$area['area_nombre']}</option>";
                }
                ?>
            </select>
            <select id="statusFilter">
                <option value="">Filtrar por estado</option>
                <option value="entrada">Dentro de áreas</option>
                <option value="salida">Fuera de áreas</option>
            </select>
            <button type="button" id="filterButton">Filtrar</button>
        </div>

        <table id="accessTable">
            <thead>
                <tr>
                    <th>Empleado</th>
                    <th>Área</th>
                    <th>Hora de Entrada</th>
                    <th>Hora de Salida</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Obtener los registros de accesos
                $sql = "SELECT 
                            CONCAT(e.emp_nombre, ' ', e.emp_apellidos) AS empleado_nombre,
                            a.area_nombre AS area_nombre,
                            r.acceso_empleado,
                            a.area_id,
                            DATE_FORMAT(r.fecha_acceso, '%W, %d de %M de %Y %r') AS formatted_entrada,
                            CASE 
                                WHEN r.hora_salida IS NOT NULL 
                                THEN DATE_FORMAT(r.hora_salida, '%W, %d de %M de %Y %r') 
                                ELSE NULL 
                            END AS formatted_salida
                        FROM tbl_registro_accesos r
                        INNER JOIN tbl_empleado e ON r.acceso_empleado = e.emp_documento
                        INNER JOIN tbl_area a ON r.acceso_area = a.area_id
                        ORDER BY r.fecha_acceso DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr data-documento='{$row['acceso_empleado']}' data-area='{$row['area_id']}' data-status='" . ($row['formatted_salida'] ? 'salida' : 'entrada') . "'>
                                <td>{$row['empleado_nombre']}</td>
                                <td>{$row['area_nombre']}</td>
                                <td class='entry'>" . ($row['formatted_entrada'] ?: 'N/A') . "</td>
                                <td class='exit'>" . ($row['formatted_salida'] ?: 'N/A') . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='no-data'>No hay accesos registrados</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <script>
        document.getElementById('filterButton').addEventListener('click', () => {
            const documentFilter = document.getElementById('documentFilter').value.trim();
            const areaFilter = document.getElementById('areaFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;

            const rows = document.querySelectorAll('#accessTable tbody tr');

            rows.forEach(row => {
                const documento = row.getAttribute('data-documento');
                const area = row.getAttribute('data-area');
                const status = row.getAttribute('data-status');

                let matches = true;

                if (documentFilter && !documento.includes(documentFilter)) {
                    matches = false;
                }
                if (areaFilter && area !== areaFilter) {
                    matches = false;
                }
                if (statusFilter && status !== statusFilter) {
                    matches = false;
                }

                row.classList.toggle('hidden', !matches);
            });
        });
    </script>
</body>
</html>
