<?php include 'db_connect.php' ?>
<?php
if (isset($_GET['id'])) {
	$qry = $conn->query("SELECT * FROM student_ef_list where id = {$_GET['id']} ");
	foreach ($qry->fetch_array() as $k => $v) {
		$$k = $v;
	}
}
?>
<div class="container-fluid">
	<form id="manage-fees">
		<div id="msg"></div>
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		
		<div class="form-group">
			<label for="" class="control-label">Cliente</label>
			<select name="student_id" id="student_id" class="custom-select input-sm select2">
				<option value=""></option>
				<?php
				$student = $conn->query("SELECT * FROM student order by name asc ");
				while ($row = $student->fetch_assoc()) :
				?>
					<option value="<?php echo $row['id'] ?>" <?php echo isset($student_id) && $student_id == $row['id'] ? 'selected' : '' ?>><?php echo ucwords($row['name']) . ' | ' . $row['id_no'] ?></option>
				<?php endwhile; ?>
			</select>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Terreno</label>
			<select name="course_id" id="course_id" class="custom-select input-sm select2">
				<option value=""></option>
				<?php
				$student = $conn->query("SELECT *,concat(course,'-',level) as class FROM courses order by course asc ");
				while ($row = $student->fetch_assoc()) :
				?>
					<option value="<?php echo $row['id'] ?>" data-amount="<?php echo $row['total_amount'] ?>" <?php echo isset($course_id) && $course_id == $row['id'] ? 'selected' : '' ?>><?php echo $row['class'] ?></option>
				<?php endwhile; ?>
			</select>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Fecha de registro</label>
			<input type="date" name="register_date" id="register_date" class="form-control"> 
		</div>
		<div class="form-group">
			<label for="" class="control-label">Costo total</label>
			<input type="text" id="total_fee" class="form-control text-right" name="total_fee" value="<?php echo isset($total_fee) ?  number_format($total_fee, 2, ',', ' ') : '' ?>" required readonly>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Cuota Mensual</label>
			<input type="number" id="monthly_fee" class="form-control text-right" name="monthly_fee"  min="0" value="0"
			oninput="if(this.value === '' || this.value < 0) this.value = 0; this.value = Math.abs(this.value)" required>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Numero de cuotas</label>
			<input type="number" id="number_of_payments" class="form-control text-right" name="number_of_payments"   min="0" value="0"
			oninput="if(this.value === '' || this.value < 0) this.value = 0; this.value = Math.abs(this.value)" required>
		</div>
	</form>
</div>

<script>
	$('.select2').select2({
		placeholder: 'Por favor seleccione aquí',
		width: '100%'
	})
	$('#course_id').change(function() {
		var amount = $('#course_id option[value="' + $(this).val() + '"]').attr('data-amount')
		$('[name="total_fee"]').val(parseFloat(amount).toLocaleString('fr-FR', {
			style: 'decimal',
			maximumFractionDigits: 2,
			minimumFractionDigits: 2
		}))
		calculateNumberOfPayment();
	})
	$('#manage-fees').submit(function(e) {
		e.preventDefault()
		start_load()
		$.ajax({
			url: 'ajax.php?action=save_fees',
			method: 'POST',
			data: $(this).serialize(),
			error: err => {
				console.log(err)
				end_load()
			},
			success: function(resp) {

				if (resp == 1) {
					location.reload();
					alert_toast("Datos guardados exitósamente", 'success')
					setTimeout(function() {
						location.reload()
					}, 1000)
				} else if (resp == 2) {
					$('#msg').html('<div class="alert alert-danger">El cliente seleccionado ya se encuentro asociado al terreno</div>')
					end_load()
				}
			}
		})
	})

	$("#monthly_fee").on("input", function(){
		calculateNumberOfPayment();
	})
	function calculateNumberOfPayment(){

		if($("#total_fee").val() !== "" && $("#monthly_fee").val() !== "") {
    const totalFee = $("#total_fee").val();
    const totalFeeParsed = parseInt(totalFee.replace(/,/g, ''), 10);
    const monthlyFee = parseInt($("#monthly_fee").val(), 10);

    // Realiza la división y redondea el resultado al entero más cercano
    const numberOfPayments = Math.round(totalFeeParsed / monthlyFee);
    
    // Establece el valor redondeado en el campo de número de pagos
    $("#number_of_payments").val(numberOfPayments);
} else {
    $("#number_of_payments").val("");
}
		
	}
</script>