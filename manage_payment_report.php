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


<div class="container-fluid" >
    <div class="col-lg-12">
        <div class="row mb-4 mt-4">
            <div class="col-md-12">
                <!-- Espacio para contenido adicional si es necesario -->
                 <button class="btn btn-info" id="btnImprimir">
                    imprimir
                 </button>
            </div>
        </div>
        <div class="row" id="contenedor">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <b>Reporte de pagos </b>
                      
                    </div>
                    <div class="card-body">
                    <div class="table-responsive">
                    <table class="table" border='1' bordercolor="#000">
                        <thead >
                            <tr>
                                <th>Fecha</th>
                                <th>Nombre</th>
                                <th>Terreno</th>
                                <th>N. Cuota</th>
                                <th>Couta</th>
                                <th>Multa</th>
                                <th>Abono Multa</th>
                                <th>T.C</th>
                                <th>Monto $</th>
                                <th>Monto Bs</th>
                                <th>Estado</th>
                               
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $ptotal = 0;
                        $acobrar=0;
                        $num = "320,8000.20"; // Cadena original
                        $num = str_replace(',', '', $num); // Elimina la coma
                        $num = (float)$num; // Convierte a floa
                        foreach ($pay_arr as $row) { 
                            $id = $row['id'];
                            // Asegúrate de que $id esté protegido contra inyecciones SQL
                            $id = $conn->real_escape_string($id); 
                        
                            $mora_result = $conn->query("SELECT SUM(monto) AS total FROM TbIngresoMora WHERE idpago='$id'"); 
                        
                            if ($mora_result) {
                                $ingrexmora = $mora_result->fetch_assoc();
                            } else {
                                echo "Error en la consulta: " . $conn->error;
                            }
                           
                            $moratotal=number_format($row['fine'], 2, '.', ' ') - number_format($ingrexmora['total'], 2, '.', ',');
                            $acobrar=number_format($row['amount'], 2) + number_format($row['fine'], 2) + $moratotal;
                            if($row['registered_exchange_rate']>0){$tc=$row['registered_exchange_rate'];}else{ $tc=7;}
                            ?>
                            <tr>
                                <td><?= strftime('%b %d, %y', strtotime($row['payment_date']))  ?></td>
                                <td><?= $row['sname'] ?></td>
                                <td><?php echo $row['ef_no'] ?></td>
                                <td><?php echo $row['fee_no'] ?></td>
                                <td>$.<?php echo number_format($row['amount'], 2, '.', ' ') ?></td>
                                <td style="color:red;">$.<?php echo number_format($row['fine'], 2, '.', ' ') ?></td>
                                 <td style="color:green;">$.<?= number_format($ingrexmora['total'], 2, '.', ','); ?></td> 
                                <td><?php echo $row['registered_exchange_rate'] ?></td>
                                <td>$.<?= number_format($row['amount'], 2, '.', ' '); ?></td>
                                <td>Bs.<?= number_format($row['amount'], 2, '.', ' ') * $tc; ?></td>
                                <td><?php if(intval($row['stado'])===0){ echo '<span class="badge badge-pill badge-success">Pagada</span>';}elseif(intval($row['stado'])===1){ echo '<span class="badge badge-pill badge-danger">Pendiente</span>';} ?></td>
                                
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
<script>
    document.getElementById('btnImprimir').addEventListener('click', function() {
        const contenedor = document.getElementById('contenedor').innerHTML;
        const ventanaImpresion = window.open('', '', 'height=600,width=800');
        ventanaImpresion.document.write('<html><head><title>Imprimir</title>');
        ventanaImpresion.document.write('</head><body >');
        ventanaImpresion.document.write(contenedor);
        ventanaImpresion.document.write('</body></html>');
        ventanaImpresion.document.close();
        ventanaImpresion.print();
    });
</script>