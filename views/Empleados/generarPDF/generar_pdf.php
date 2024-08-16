<?php
session_start();
// Construir la ruta base utilizando la constante __DIR__
$base_url = rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/';
require_once('../../../config/db.php');
require('fpdf.php'); // Asegúrate de que la librería FPDF esté en la misma carpeta o especifica la ruta correcta

if(isset($_GET['pdf'])) {
    // Obtén el valor del PDF seleccionado
    $pdfType = $_GET['pdf'];

    // Verifica si el empleado está autenticado
    if(isset($_SESSION['username'])) {
        // Obtén el documento del empleado de la sesión
        $documentoEmpleado = $_SESSION['username'];

        // Conectar a la base de datos
        $db = Database::connect();
        if ($db->connect_error) {
            die("Conexión fallida: " . $db->connect_error);
        }

        // Consulta el nombre del empleado en la base de datos
        $stmt = $db->prepare("SELECT emp_nombre, emp_apellidos, emp_cargo FROM tbl_empleado WHERE emp_documento = ?");
        $stmt->bind_param("s", $documentoEmpleado);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Si se encuentra el empleado, obtén su nombre
            $row = $result->fetch_assoc();
            $employeeName = $row['emp_nombre'] . ' ' . $row['emp_apellidos'];
            $cargo = $row['emp_cargo'];
        } else {
            // Si no se encuentra el empleado, muestra un mensaje de error
            $employeeName = "Nombre no encontrado";
            $cargo = "Cargo no encontrado";
        }

        // Cierra la conexión a la base de datos
        $stmt->close();
        $db->close();
    } else {
        // Si el empleado no está autenticado, muestra un mensaje de error
        $employeeName = "Empleado no autenticado";
        $cargo = "Cargo no disponible";
    }

    // Inicializa el objeto FPDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);

    // Verifica el tipo de PDF seleccionado
    if($pdfType === 'pdf1') {
        // PDF de registro de horas trabajadas
        $bossName = "María García";
        $companyName = "Empresa XYZ";
        $currentDate = date('Y-m-d');

        // Agrega el encabezado
        $pdf->Cell(0, 10, $companyName, 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->Cell(0, 10, 'Registro de Horas Trabajadas', 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->Cell(0, 10, 'Nombre del Empleado: ' . $employeeName, 0, 1);
        $pdf->Cell(0, 10, 'Nombre del Jefe: ' . $bossName, 0, 1);
        $pdf->Cell(0, 10, 'Fecha: ' . $currentDate, 0, 1); // Muestra la fecha actual
        $pdf->Ln(5);

        // Define los datos de ejemplo para el registro de horas trabajadas
        $data = array(
            array('Fecha', 'Hora de entrada', 'Hora de salida'),
            array('2024-03-25', '08:00', '16:00'),
            array('2024-03-26', '07:30', '15:30'),
            array('2024-03-27', '08:15', '16:15')
        );

        // Agrega la tabla de datos al PDF
        foreach($data as $row) {
            $pdf->Cell(40, 10, $row[0], 1);
            $pdf->Cell(40, 10, $row[1], 1);
            $pdf->Cell(40, 10, $row[2], 1);
            $pdf->Ln();
        }
    } elseif($pdfType === 'pdf2') {
        // PDF de certificado laboral
        $companyName = "Empresa XYZ";
        $currentDate = date('Y-m-d');
        $representativeName = "Nombre y Apellido del Representante";
        $representativeDocument = "Documento de Identidad del Representante";

        // Agrega el encabezado
        $pdf->Cell(0, 10, $companyName, 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->Cell(0, 10, 'Certificado Laboral', 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->Cell(0, 10, 'Nombre del Empleado: ' . $employeeName, 0, 1);
        $pdf->Cell(0, 10, 'Cédula: ' . $documentoEmpleado, 0, 1);
        $pdf->Cell(0, 10, 'El (la) suscrito representante de la empresa: ' . $representativeName, 0, 1);
        $pdf->Cell(0, 10, 'Cargo del representante: ' . $cargo, 0, 1);
        $pdf->Ln(5);
        $pdf->Cell(0, 10, 'CERTIFICA', 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->Cell(0, 10, 'Que el (la) señor (a) ' . $employeeName . ', colombiano, identificado (a) con la cédula de ciudadanía No.' . $documentoEmpleado . ' y residente en ___________________________,', 0, 1);
        $pdf->Cell(0, 10, 'labora (ó) para esta empresa desde _________ del año 20___ hasta _________ del año 20___, con un contrato a tiempo indefinido, desempeñando el cargo de: ____________________.', 0, 1);
        $pdf->Cell(0, 10, 'Durante este tiempo, la persona ha devengado un salario mensual de: ___________________ pesos.', 0, 1);
        $pdf->Ln(10);
        $pdf->Cell(0, 10, 'Certificación que se expide por solicitud de la parte interesada en ___________, a los ___ días del mes de ______________ del 20____.', 0, 1);
        $pdf->Ln(10);
        $pdf->Cell(0, 10, 'Atento y Cordial.', 0, 1);
        $pdf->Ln(10);
        $pdf->Cell(0, 10, $representativeName, 0, 1);
        $pdf->Cell(0, 10, 'Datos de contacto', 0, 1);
        $pdf->Cell(0, 10, $representativeDocument, 0, 1);
    }

    // Agrega espacio para la firma del jefe
    $pdf->Ln(10);
    $pdf->Output();
}
?>
