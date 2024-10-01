<?php include 'db_connect.php'; 
date_default_timezone_set("America/La_Paz");
setlocale(LC_TIME, 'es_VE.UTF-8','esp');
?>
<div class="container-fluid">
    <div class="col-lg-12">
        <div class="row mb-4 mt-4">
            <div class="col-md-12">
                <!-- Espacio para contenido adicional si es necesario -->
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <b>Pagos </b>
                        <a class="btn btn-primary" href="javascript:void(0)" id="new_payment">
                            <i class="fa fa-plus"></i> Pago
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Fecha</th>
                                        <th>Nombre</th>
                                        <th>Terreno</th>
                                        <th>Monto Pagado</th>
                                        <th>Multa</th>
                                        <th>Tipo de cambio</th>
                                        <th>Total Bs.</th>
                                        <th>N. cuota</th>
                                        <th>Estado</th>
                                        <th class="text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $payments = $conn->query("SELECT p.*, s.name as sname, ef.ef_no, s.id_no, ef.course_id FROM payments p 
                                            INNER JOIN student_ef_list ef ON ef.id = p.ef_id 
                                            INNER JOIN student s ON s.id = ef.student_id 
                                            ORDER BY p.payment_date DESC");
                                    if ($payments->num_rows > 0) :
                                        while ($row = $payments->fetch_assoc()) :
                                            $paid = $conn->query("SELECT sum(amount) as paid FROM payments where ef_id=" . $row['id']);
                                            $paid = $paid->num_rows > 0 ? $paid->fetch_array()['paid'] : '';
                                            $property = $conn->query("SELECT course FROM courses where id =" . $row['course_id']);
                                            $property = $property->num_rows > 0 ? $property->fetch_array()['course'] : '';
                                    ?>
                                            <tr>
                                                <td class="text-center"><?php echo $i++ ?></td>
                                                <td> <?= strftime('%b %d, %y', strtotime($row['payment_date'])) ?><br/></td>
                                                <td><?php echo ucwords($row['sname']) ?></td>
                                                <td><?php echo $property ?></td>
                                                <td class="text-right"><?php echo number_format($row['amount'], 2,',', ' ') ?></td>
                                                <td><?php echo number_format($row['fine'], 2, ',', ' ') ?></td>
                                                <td><?php echo number_format($row['registered_exchange_rate'], 2, ',', ' ') ?></td>
                                                <td><?php echo number_format((($row['fine'] + $row['amount']) * $row['registered_exchange_rate']), 2, ',',' ') ?></td>
                                                <td><?php echo $row['fee_no'] ?></td>
                                                <td><?php if(intval($row['stado'])===0){ echo '<span class="badge badge-pill badge-success">Pagada</span>';}elseif(intval($row['stado'])===1){ echo '<span class="badge badge-pill badge-danger">Pendiente</span>';} ?></td>
                                                <td class="text-center">

                                               
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button class="btn btn-info view_payment" type="button" data-id="<?php echo $row['id'] ?>" data-ef_id="<?php echo $row['ef_id'] ?>">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-warning edit_payment" type="button" data-id="<?php echo $row['id'] ?>">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </button>
                                                    <button class="btn btn-danger delete_payment" data-id="<?php echo $row['id'] ?>">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                   
                                                    <a href="index.php?page=manage_payment_report&id=<?php echo $row['ef_id'] ?>" class="btn btn-primary">
                                                        <i class="fas fa-file-alt"></i>
                                                    </a>

                                                </div>

                                                </td>
                                            </tr>
                                        <?php
                                        endwhile;
                                         else :
                                        ?>
                                            <tr>
                                                <th class="text-center" colspan="11">Sin datos que mostrar.</th>
                                            </tr> 
                                        <?php
                                        endif;
                                    ?>
                                </tbody>
                            </table>
                        </div> <!-- Fin de la clase table-responsive -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    td {
        vertical-align: middle !important;
    }

    td p {
        margin: unset;
    }

    img {
        max-width: 100px;
        max-height: 150px;
    }
</style>

<script>
    $(document).ready(function() {
        $('table').dataTable();
    });

    $('#new_payment').click(function() {
        uni_modal("Nuevo Pago ", "manage_payment.php", "mid-large");
    });

    $('.view_payment').click(function() {
        uni_modal("Información de Pago", "view_payment.php?ef_id=" + $(this).attr('data-ef_id') + "&pid=" + $(this).attr('data-id'), "large");
    });

    $('.edit_payment').click(function() {
        uni_modal("Gestionar Pago", "manage_payment.php?id=" + $(this).attr('data-id'), "mid-large");
    });

    $('.delete_payment').click(function() {
        _conf("¿Deseas eliminar este pago? Esta accion no se puede desaser", "delete_payment", [$(this).attr('data-id')]);
    });

    $('.reporte_pago').click(function(){
        uni_modal("Reporte Pago", "manage_payment_report.php?id=" + $(this).attr('data-id'), "large");
    });

    function delete_payment($id) {
        start_load();
        $.ajax({
            url: 'ajax.php?action=delete_payment',
            method: 'POST',
            data: {
                id: $id
            },
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Datos eliminados exitósamente", 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                }
            }
        });
    }
</script>
