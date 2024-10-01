<?php include 'db_connect.php' ?>
<?php

$qry = $conn->query("SELECT * FROM configuration where id = 1 ");
$exchange_rate = $qry->fetch_array()["exchange_rate"];

if (isset($_GET['id'])) {
	$qry = $conn->query("SELECT * FROM payments where id = {$_GET['id']} ");
	foreach ($qry->fetch_array() as $k => $v) {
		$$k = $v;
	}
}
?>
<div class="container-fluid">
	<form id="manage-mora">
		<div class="row">
			<div id="msg"></div>
			<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
			<div class="form-group col-12">
				<label for="" class="control-label">Cliente</label>
				<select name="idpago" id="idpago" class="custom-select input-sm select2">
					<option value=""></option>
					<?php
					$mora = $conn->query("SELECT p.*, s.name as sname, ef.ef_no, s.id_no, ef.course_id FROM payments p 
                                            INNER JOIN student_ef_list ef ON ef.id = p.ef_id 
                                            INNER JOIN student s ON s.id = ef.student_id 
                                            ORDER BY p.payment_date DESC ");
					while ($row = $mora->fetch_assoc()) :
					
					?>
						<option value="<?php echo $row['id'] ?>" ><?php echo $row['payment_date'] ?>  - <?php echo $row['sname']; ?></option>
					<?php endwhile; ?>
				</select>
			</div>
			<div class="form-group col-6">
			<label for="" class="control-label">Fecha de pago</label>
			    <input type="date" name="fecha" id="fecha" class="form-control" value="<?php echo isset($payment_date) ? $payment_date : ''?>" required>
			</div>
		
			<div class="form-group col-6">
			    <label for="" class="control-label">Monto $</label>
                <input type="text" class="form-control" name="monto" id="monto" onKeyUp="DolarBs(this);">
            </div>

			<div class="form-group col-6">
			    <label for="" class="control-label">Monto bs</label>
                <input type="text" class="form-control" name="montobs" id="montobs">
            </div>
			
			
		</div>
 	</form>
</div>
<script>
	$('#manage-mora').submit(function(e) {
		e.preventDefault()
		start_load()
		$.ajax({
			url: 'ajax.php?action=save_mora',
			method: 'POST',
			data: $(this).serialize(),
			error: err => {
				alert_toast("Todas las cuotas para este lote ya fueron pagadas", 'ocurrio un error al guardar')
				end_load()
			},
			success: function(resp) {
				resp = JSON.parse(resp);
				if (resp  == 1) {
					alert_toast("Datos guardados con éxito.", 'success');
							setTimeout(function() {
								location.reload()
							}, 500)
					
				}else if (resp == 2) {
					alert_toast("Todas las cuotas para este lote ya fueron pagadas", 'danger')
				}
			}
		})
	})
	function DolarBs(valor){
		let montobs = parseFloat($('#monto').val()) || 0; // Si el valor es NaN, asigna 0
		let paymentFine = parseFloat($('#payment_fine').val()) || 0; // Si el valor es NaN, asigna 0
		let exchangeRate = 7; // Si el valor es NaN, asigna 0

		// Calcular la suma y multiplicación
		let total = (valor.value) * exchangeRate;
		// Establecer el valor calculado en el input con el ID 'total_bs'
		$('#montobs').val(total.toFixed(2));
	}
</script>
