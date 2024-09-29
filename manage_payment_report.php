<!-- Modal -->
<?php
 $id=$_GET['id'];

 include 'db_connect.php'; 
 date_default_timezone_set("America/La_Paz");
setlocale(LC_TIME, 'es_VE.UTF-8','esp');
$fees = $conn->query("SELECT ef.*,s.name as sname,s.id_no,concat(c.course,' - ',c.level) as `class` FROM student_ef_list ef inner join student s on s.id = ef.student_id inner join courses c on c.id = ef.course_id  where ef.id = $id");
foreach ($fees->fetch_array() as $k => $v) {
	$$k = $v;
}
$payments = $conn->query("SELECT p.*, s.name as sname, ef.ef_no, s.id_no, ef.course_id FROM payments p 
    INNER JOIN student_ef_list ef ON ef.id = p.ef_id 
    INNER JOIN student s ON s.id = ef.student_id 
WHERE ef_id = $id ORDER BY p.payment_date ASC");
$pay_arr = array();
while ($row = $payments->fetch_array()) {
	$pay_arr[$row['id']] = $row;
}
?>
<div class="container-fluid">
    <div class="col-lg-12">
        <div class="row mb-4 mt-4">
            <div class="col-md-12">
                <!-- Espacio para contenido adicional si es necesario -->
                 <button class="btn btn-info">
                    imprimir
                 </button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <b>Reporte de pagos </b>
                      
                    </div>
                    <div class="card-body">
                    <div class="table-responsive">
                    <table class="table" >
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Nombre</th>
                                <th>Terreno</th>
                                <th>Couta</th>
                                <th>Multa</th>
                                <th>Abono Multa</th>
                                <th>Tipo de cambio</th>
                                <th>Monto $</th>
                                <th>Monto Bs</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $ptotal = 0;
                        $acobrar=0;
                        foreach ($pay_arr as $row) {  
                            $acobrar=number_format($row['amount'], 2, ',', ' ') + number_format($row['fine'], 2, ',', ' ');
                            if($row['registered_exchange_rate']>0){$tc=$row['registered_exchange_rate'];}else{ $tc=7;}
                            ?>
                            <tr>
                                <td><?= strftime('%b %d, %y', strtotime($row['payment_date']))  ?></td>
                                <td><?= $row['sname'] ?></td>
                                <td><?php echo $row['ef_no'] ?></td>
                                <td><?php echo number_format($row['amount'], 2, ',', ' ') ?></td>
                                <td style="color:red;"><?php echo number_format($row['fine'], 2, ',', ' ') ?></td>
                                <td style="color:green;">$320,800</td>
                                <td><?php echo $row['registered_exchange_rate'] ?></td>
                                <td><?= number_format($acobrar, 2, ',', ' '); ?></td>
                                <td><?= number_format($acobrar, 2, ',', ' ') * $tc; ?></td>
                            </tr>
                        <?php } ?>
                            
                             
                         
                        </tbody>
                       
                    </table>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
