<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0.8cm; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #2c3e50;
            margin: 0;
        }

        /* Encabezado */
        .header-wrapper {
            width: 100%;
            border-bottom: 2px solid #c9a050;
            padding-bottom: 10px;
            height: 85px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-td {
            width: 50%;
            vertical-align: top;
        }

        .logo-img {
            height: 55px;
            width: auto;
            display: block;
            margin-bottom: 0;
        }

        .brand-text {
            font-size: 11px;
            font-weight: bold;
            color: #1a3344;
            margin-top: -8px;
            text-transform: uppercase;
        }

        .info-td {
            width: 50%;
            text-align: right;
            vertical-align: top;
            font-size: 10px;
            line-height: 1.4;
        }

        /* Título y Fechas */
        .report-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #1a3344;
            margin: 20px 0 5px 0;
            text-transform: uppercase;
        }

        .date-range {
            text-align: center;
            font-size: 11px;
            color: #7f8c8d;
            margin-bottom: 20px;
        }

        /* Tabla de Movimientos */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            background-color: #1a3344;
            color: white;
            padding: 8px;
            font-size: 10px;
            text-transform: uppercase;
            border: 1px solid #1a3344;
        }

        .data-table td {
            padding: 7px;
            border: 1px solid #eee;
            font-size: 10px;
            text-align: center;
        }

        .text-left { text-align: left; }

        /* Badge para tipos de movimiento */
        .badge {
            font-weight: bold;
            padding: 2px 5px;
            border-radius: 3px;
            text-transform: uppercase;
            font-size: 9px;
        }
        .entrada { color: #27ae60; }
        .salida { color: #c0392b; }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #bdc3c7;
        }
    </style>
</head>
<body>

    <div class="header-wrapper">
        <table class="header-table">
            <tr>
                <td class="logo-td">
                    <img src="<?= media(); ?>/images/logosayana.png" class="logo-img">
                    <div class="brand-text">
                        SAYANA<br>
                        <span style="font-weight: normal; color: #7f8c8d; font-size: 9px;">Gestión de Inventario y Movimientos</span>
                    </div>
                </td>
                <td class="info-td">
                    <strong>Generado por:</strong> <?= $_SESSION['userData']['nombre']; ?><br>
                    <strong>Fecha:</strong> <?= date('d/m/Y'); ?><br>
                    <strong>Hora:</strong> <?= date('g:i a'); ?>
                </td>
            </tr>
        </table>
    </div>

    <h1 class="report-title"><?= $data['page_title']; ?></h1>
    <p class="date-range">
        Rango de fechas: <strong><?= $data['fecha_inicio']; ?></strong> al <strong><?= $data['fecha_fin']; ?></strong>
    </p>

    <table class="data-table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th class="text-left">Producto</th>
                <th>Tipo</th>
                <th>Cantidad</th>
                <th>Usuario</th>
                <th class="text-left">Observaciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($data['reporteData'])){ 
                foreach ($data['reporteData'] as $movimiento) { 
                    $claseTipo = ($movimiento['tipo'] == 'Entrada') ? 'entrada' : 'salida';
            ?>
            <tr>
                <td><?= $movimiento['fecha']; ?></td>
                <td class="text-left"><?= $movimiento['producto']; ?></td>
                <td><span class="badge <?= $claseTipo; ?>"><?= $movimiento['tipo']; ?></span></td>
                <td><?= $movimiento['cantidad']; ?></td>
                <td><?= $movimiento['usuario']; ?></td>
                <td class="text-left"><?= $movimiento['observacion']; ?></td>
            </tr>
            <?php } } else { ?>
            <tr>
                <td colspan="6">No se registraron movimientos en este periodo.</td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="footer">
        Este documento es un reporte oficial del sistema Sayana.
    </div>

</body>
</html>