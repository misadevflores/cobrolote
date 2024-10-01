<?php include 'db_connect.php'; ?>
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
                        <b>Ingreso por moras </b>
                        <a class="btn btn-primary" href="javascript:void(0)" id="new_mora">
                            <i class="fa fa-plus"></i> Ingresar
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                       <th>Cliente</th>
                                       <th>fecha</th>
                                       <th>Ingreso x mora</th>
                                       <th>Deuda x mora</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $i = 1;
                                    $payments = $conn->query("SELECT p.*,m.fecha, m.monto as moraingreso, s.name as sname, ef.ef_no, s.id_no, ef.course_id FROM payments p 
                                            INNER JOIN student_ef_list ef ON ef.id = p.ef_id 
                                            INNER JOIN student s ON s.id = ef.student_id 
                                            INNER JOIN TbIngresoMora m on m.idpago=p.id
                                            ORDER BY p.payment_date DESC");
                                        while ($row = $payments->fetch_assoc()) :
                                    
                                    ?>
                                    <tr>
                                        <td><?= $row['sname']; ?></td>
                                        <td><?= $row['fecha']; ?></td>
                                        <td style="color:green;"><?= $row['moraingreso']; ?></td>
                                        <td  style="color:red;"><?= $row['fine']; ?></td>
                                    </tr>
                                    <?php endwhile; ?>
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

    $('#new_mora').click(function() {
        uni_modal("ingreso de mora ", "manage_mora.php", "mid-large");
    });
</script>
