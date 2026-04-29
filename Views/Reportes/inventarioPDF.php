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

        /* Contenedor de encabezado con altura controlada */
        .header-wrapper {
            width: 100%;
            border-bottom: 2px solid #c9a050;
            padding-bottom: 10px;
            height: 100px; /* Forzamos altura para que nada se mueva */
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Ajuste crítico del LOGO */
        .logo-td {
            width: 50%;
            vertical-align: top;
        }

        .logo-img {
            height: 60px; /* Tamaño fijo para que no crezca */
            width: auto;
            display: block;
        }

        .brand-text {
            font-size: 11px;
            font-weight: bold;
            color: #1a3344;
            margin-top: 5px;
            text-transform: uppercase;
        }

        /* Ajuste de la información de la derecha */
        .info-td {
            width: 50%;
            text-align: right;
            vertical-align: top;
            font-size: 10px;
            line-height: 1.4;
        }

        .report-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #1a3344;
            margin: 30px 0 20px 0;
            text-transform: uppercase;
        }

        /* Tabla de datos */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table th {
            background-color: #1a3344;
            color: white;
            padding: 10px;
            font-size: 10px;
            text-transform: uppercase;
        }
        .data-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
            font-size: 10px;
            text-align: center;
        }
        .total-row {
            background-color: #fcf8e3;
            font-weight: bold;
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
                        SAYANA  <br>
                        <span style="font-weight: normal; color: #7f8c8d; font-size: 9px;">Gestión de Inventario</span>
                    </div>
                </td>
                <td class="info-td">
                    <div style="margin-top: 5px;">
                        <strong>Generado el:</strong> <?= date('d/m/Y'); ?><br>
                        <strong>Hora:</strong> <?= date('g:i a'); ?><br>
                        <strong>Responsable:</strong> <?= $_SESSION['userData']['nombre']; ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <h1 class="report-title"><?= $data['page_title']; ?></h1>

    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th style="text-align: left;">Producto</th>
                <th>Stock</th>
                <th>Costo Unit.</th>
                <th>Subtotal</th>
                <th>Proveedor</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $totalInversion = 0;
            foreach ($data['reporteData'] as $producto) { 
                $totalInversion += $producto['inversion_total'];
            ?>
            <tr>
                <td><?= $producto['idproducto']; ?></td>
                <td style="text-align: left;"><?= $producto['nombre']; ?></td>
                <td><strong><?= $producto['stock']; ?></strong></td>
                <td><?= formatMoneda($producto['precio_costo']); ?></td>
                <td><?= formatMoneda($producto['inversion_total']); ?></td>
                <td><?= $producto['proveedor']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" style="text-align: right; padding: 15px;">TOTAL INVERSIÓN:</td>
                <td><?= SMONEY.' '.formatMoneda($totalInversion); ?></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

</body>
</html>