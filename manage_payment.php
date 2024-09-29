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
	<form id="manage-payment">
		<div class="row">
			<div id="msg"></div>
			<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
			<div class="form-group col-12">
				<label for="" class="control-label">Cliente</label>
				<select name="ef_id" id="ef_id" class="custom-select input-sm select2">
					<option value=""></option>
					<?php
					$fees = $conn->query("SELECT ef.*,s.name as sname,s.id_no FROM student_ef_list ef inner join student s on s.id = ef.student_id order by s.name asc ");
					while ($row = $fees->fetch_assoc()) :

						$paid = $conn->query("SELECT sum(amount)
						as paid FROM payments
						where ef_id=" . $row['id']);

						$lastPaymentDate = $conn->query("SELECT payment_date 
						FROM payments 
						WHERE ef_id = " . $row['id'] . (isset($id) ? " and id!=$id " : '') . "
						ORDER BY date_created DESC 
						LIMIT 1");
						
						$paid = $paid->num_rows > 0 ? $paid->fetch_array()['paid'] : '';
						$lastPaymentDate = $lastPaymentDate->num_rows > 0 ? $lastPaymentDate->fetch_array()['payment_date'] : '';
						$balance = $row['total_fee'] - $paid;
						$registerDate= $row['register_date'];
					?>
						<option value="<?php echo $row['id'] ?>" monthly-fee="<?php echo $row['monthly_fee']?>" register-date ="<?php echo $registerDate ?>" last-payment-date="<?php echo $lastPaymentDate ?>" data-balance="<?php echo $balance ?>" <?php echo isset($ef_id) && $ef_id == $row['id'] ? 'selected' : '' ?>><?php echo  ucwords($row['sname']) .' | '. $row['ef_no']   ?></option>
					<?php endwhile; ?>
				</select>
			</div>
			<div class="form-group col-6">
			<label for="" class="control-label">Fecha de pago</label>
			<input type="date" name="payment_date" id="payment_date" class="form-control" value="<?php echo isset($payment_date) ? $payment_date : ''?>" required>
			<small id="last-payment-info" style="color: lightgray;"></small> <!-- Aquí se mostrará la última fecha de pago -->
			</div>
			<div class="form-group col-6">
				<label for="" class="control-label">Saldo Pendiente</label>
				<input type="text" class="form-control text-right" id="balance" value="" required readonly>
			</div>
			<div class="form-group col-6">
			<label for="" class="control-label">Monto a pagar</label>
			<input type="number" class="form-control text-right" id="amount" name="amount" min="0"
				oninput="if(this.value === '' || this.value < 0) this.value = 0; this.value = Math.abs(this.value)"
				value="<?php echo isset($amount) ? $amount : 0 ?>" required>
			</div>
			<div class="form-group col-6">
			<label for="" class="control-label">Multa por retraso</label>
			<input type="number" id="payment_fine" class="form-control text-right" name="fine" min="0"
				oninput="if(this.value === '' || this.value < 0) this.value = 0; this.value = Math.abs(this.value)" 
				value="<?php echo isset($fine) ? $fine : 0 ?>" required>
			</div>
			<div class="form-group col-6">
			<label for="" class="control-label">Tipo de cambio</label>
			<input type="number" id="exchange_rate" class="form-control text-right" name="registered_exchange_rate" min="0" step="0.01"
				oninput="if(this.value === '' || this.value < 0) this.value = 0; this.value = parseFloat(this.value).toFixed(2)" 
				value="<?php echo isset($registered_exchange_rate) ? $registered_exchange_rate : $exchange_rate ?>" required>
			</div>
			<div class="form-group col-6">
				<label for="" class="control-label">Total Bolivianos</label>
				<input type="number" class="form-control text-right" id="total_bs" value="0" required readonly>
			</div>
			<div class="form-group col-12">
				<label for="" class="control-label">Estado</label>
				<select name="estado" id="estado" class='form-control'>
					<option value="0">Pagado</option>
					<option value="1">Pendiente</option>
				</select>
			</div>
			<div class="form-group col-12">
				<label for="" class="control-label">Observaciones</label>
				<textarea name="remarks" id="" cols="30" rows="3" class="form-control" required=""><?php echo isset($remarks) ? $remarks : '' ?></textarea>
			</div>
		</div>
 	</form>
</div>

<script>
	var isEdit = true;

    if(isEdit && $('#ef_id').val() != ""){
		$('#ef_id').change(mangeUpdateCustomerSelectChange).change()
	}

	var registerDate;
	var lastPaymentDate;
	$('#amount, #payment_fine, #exchange_rate' ).on('input', function() {
		console.log("cambio")
		updateTotalBs();
	});

	$('#payment_date').change(function() {
    
    
    // Convertir las fechas de string a objetos Date
    var baseDate = lastPaymentDate ? new Date(lastPaymentDate) : new Date(registerDate);
    
    // Validar si la baseDate es una fecha válida
    if (isNaN(baseDate.getTime())) {
        console.error('Invalid base date:', baseDate);
        return; // Salir si la fecha base es inválida
    }
    
    var fineDay = 13;
    var selectedDate = new Date($(this).val()); // Fecha seleccionada

    // Validar si la fecha seleccionada es válida
    if (isNaN(selectedDate.getTime())) {
        console.error('Invalid selected date:', selectedDate);
        return; // Salir si la fecha seleccionada es inválida
    }

    // Calcular el día 13 del mes siguiente a baseDate
    var fineDate = new Date(baseDate.getFullYear(), baseDate.getMonth() + 1 , fineDay); // Día 13 del mes siguiente

    console.log('Next month fine date:', fineDate); // Debe mostrar una fecha válida

    var totalFine = 0;

    // Calcular la multa si la fecha seleccionada es después del día 13 del mes siguiente
    if (selectedDate > fineDate) {
        let Difference_In_Time = selectedDate.getTime() - fineDate.getTime();


        let Difference_In_Days = Math.round(Difference_In_Time / (1000 * 3600 * 24));
		
        totalFine = Math.min(Difference_In_Days, 30); // Asegurarse de no tener multas negativas
    }

    $('#payment_fine').val(totalFine);
    updateTotalBs();
	});



	$('.select2').select2({
		placeholder: 'Porfavor selecciona aquí',
		width: '100%'
	})

	
	$('#ef_id').change(manageCustomerSelectChange)


	function mangeUpdateCustomerSelectChange() {
    isEdit = false;
    var amount = $('#ef_id option[value="' + $(this).val() + '"]').attr('data-balance');
    var monthlyFee = $('#ef_id option[value="' + $(this).val() + '"]').attr('monthly-fee');
    registerDate = $('#ef_id option[value="' + $(this).val() + '"]').attr('register-date');
    lastPaymentDate = $('#ef_id option[value="' + $(this).val() + '"]').attr('last-payment-date');
    
    // Mostrar la fecha de último pago en el texto gris
    if (lastPaymentDate != "") {
        $('#last-payment-info').text('Última fecha de pago: ' + lastPaymentDate);
    } else {
        $('#last-payment-info').text('Registro inicial: ' + registerDate);
    }

    $('#payment_date').val(lastPaymentDate != "" ? lastPaymentDate : registerDate);

    $('#balance').val(parseFloat(amount).toLocaleString('en-US', {
        style: 'decimal',
        maximumFractionDigits: 2,
        minimumFractionDigits: 2
    }));

    updateTotalBs();
}

	function manageCustomerSelectChange(){
		var amount = $('#ef_id option[value="' + $(this).val() + '"]').attr('data-balance')
		var monthlyFee = $('#ef_id option[value="' + $(this).val() + '"]').attr('monthly-fee')
		registerDate= $('#ef_id option[value="' + $(this).val() + '"]').attr('register-date')
		lastPaymentDate = $('#ef_id option[value="' + $(this).val() + '"]').attr('last-payment-date')
		$('#payment_date').val(lastPaymentDate != "" ? lastPaymentDate : registerDate);
		$('#payment_fine').val(0)
		$('#amount').val(monthlyFee)

		if (lastPaymentDate != "") {
        $('#last-payment-info').text('Última fecha de pago: ' + lastPaymentDate);
		} else {
			$('#last-payment-info').text('Registro inicial: ' + registerDate);
		}
		$('#balance').val(parseFloat(amount).toLocaleString('en-US', {
			style: 'decimal',
			maximumFractionDigits: 2,
			minimumFractionDigits: 2
		}))

		updateTotalBs()
	}

	$('#manage-payment').submit(function(e) {
		e.preventDefault()
		start_load()
		$.ajax({
			url: 'ajax.php?action=save_payment',
			method: 'POST',
			data: $(this).serialize(),
			error: err => {
				alert_toast("Todas las cuotas para este lote ya fueron pagadas", 'ocurrio un error al guardar')
				end_load()
			},
			success: function(resp) {
				resp = JSON.parse(resp);
				// console.log(resp);
				if (resp.status == 1) {
					alert_toast("Datos guardados con éxito.", 'success')
					setTimeout(function() {
						var nw = window.open('receipt.php?ef_id=' + resp.ef_id + '&pid=' + resp.pid, "_blank", "width=900,height=600")
						setTimeout(function() {
							nw.print()
							setTimeout(function() {
								nw.close()
								location.reload()
							}, 500)
						}, 500)
					}, 500)
				}else if (resp == 2) {
					alert_toast("Todas las cuotas para este lote ya fueron pagadas", 'danger')
				}
			}
		})
	})

	function updateTotalBs() {
		let amount = parseFloat($('#amount').val()) || 0; // Si el valor es NaN, asigna 0
		let paymentFine = parseFloat($('#payment_fine').val()) || 0; // Si el valor es NaN, asigna 0
		let exchangeRate = parseFloat($('#exchange_rate').val()) || 0; // Si el valor es NaN, asigna 0

		// Calcular la suma y multiplicación
		let total = (amount + paymentFine) * exchangeRate;

		// Establecer el valor calculado en el input con el ID 'total_bs'
		$('#total_bs').val(total.toFixed(2));
	}
</script>