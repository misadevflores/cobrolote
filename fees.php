<?php include('db_connect.php'); ?>
<style>
	input[type=checkbox] {
		-ms-transform: scale(1.3); /* IE */
		-moz-transform: scale(1.3); /* FF */
		-webkit-transform: scale(1.3); /* Safari and Chrome */
		-o-transform: scale(1.3); /* Opera */
		transform: scale(1.3);
		padding: 10px;
		cursor: pointer;
	}

	td {
		vertical-align: middle !important;
	}

	td p {
		margin: unset
	}

	img {
		max-width: 100px;
		max-height: 150px;
	}
</style>

<div class="container-fluid">
	<div class="col-lg-12">
		<div class="row mb-4 mt-4">
			<div class="col-md-12"></div>
		</div>
		<div class="row">
			<!-- Table Panel -->
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<b>Pagos de Clientes </b>
						<span class="float:right">
							<a class="btn btn-primary col-sm-6 col-md-2 float-right" href="javascript:void(0)" id="new_fees">
								<i class="fa fa-plus"></i> Pagos
							</a>
							<!-- Botón de imprimir -->
							<button class="btn btn-success col-sm-6 col-md-2 float-right mr-2" type="button" id="print">
								<i class="fa fa-print"></i> Imprimir
							</button>
						</span>
					</div>
					<div class="card-body">
						<table class="table table-condensed table-bordered table-hover" id="report-list">
							<thead>
								<tr>
									<th class="text-center">#</th>
									<th class="">Nombre</th>
									<th class="">CI</th>
									<th class="">Terreno</th>
									<th class="">Costo total</th>
									<th class="">Cuota Mensual</th>
									<th class="">Pagado</th>
									<th class="">Balance</th>
									<th class="">Cantidad de cuotas</th>
									<th class="">Cuotas Pagadas</th>
									<th class="">Multa total</th>
									<th class="text-center">Acción</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$i = 1;
								$fees = $conn->query("SELECT ef.*,s.name as sname,s.id_no FROM student_ef_list ef inner join student s on s.id = ef.student_id order by s.name asc ");
								while ($row = $fees->fetch_assoc()) :
									$paid = $conn->query("SELECT sum(amount) as paid FROM payments where ef_id=" . $row['id']);
									$paid = $paid->num_rows > 0 ? $paid->fetch_array()['paid'] : '';
									$balance = $row['total_fee'] - $paid;
									$fine = $conn->query("SELECT sum(fine) as fine FROM payments where ef_id=" . $row['id']);
									$fine = $fine->num_rows > 0 ? $fine->fetch_array()['fine'] : '';
									$property = $conn->query("SELECT course FROM courses where id =" . $row['course_id']);
									$property = $property->num_rows > 0 ? $property->fetch_array()['course'] : '';
								?>
									<tr>
										<td class="text-center"><?php echo $i++ ?></td>
										<td><p><?php echo ucwords($row['sname']) ?></p></td>
										<td><p><?php echo $row['id_no'] ?></p></td>
										<td><p><?php echo $property ?></p></td>
										<td class="text-right"><p><?php echo number_format($row['total_fee'], 2,',',' ') ?></p></td>
										<td class="text-right"><p><?php echo number_format($row['monthly_fee'], 2, ',',' ') ?></p></td>
										<td class="text-right"><p><?php echo number_format($paid, 2, ',',' ') ?></p></td>
										<td class="text-right"><p><?php echo number_format($balance, 2, ',',' ') ?></p></td>
										<td class="text-right"><p><?php echo $row['number_of_payments'] ?></p></td>
										<td class="text-right"><p><?php echo $row['paid_fees'] ?></p></td>
										<td class="text-right"><p><?php echo number_format($fine, 2, ',',' ') ?></p></td>
										<td class="text-center">
											<button class="btn btn-primary view_payment" type="button" data-id="<?php echo $row['id'] ?>"><i class="fa fa-eye"></i></button>
											<button class="btn btn-info edit_fees" type="button" data-id="<?php echo $row['id'] ?>"><i class="fa fa-edit"></i></button>
											<!-- <button class="btn btn-danger delete_fees" type="button" data-id="<?php echo $row['id'] ?>"><i class="fa fa-trash-alt"></i></button> -->
										</td>
									</tr>
								<?php endwhile; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- Table Panel -->
		</div>
	</div>
</div>
<noscript>
    <style>
        table#report-list {
            width: 100%;
            border-collapse: collapse
        }

        table#report-list td,
        table#report-list th {
            border: 1px solid
        }

        p {
            margin: unset;
        }

        .text-center {
            text-align: center
        }

        .text-right {
            text-align: right
        }
    </style>
</noscript>
<script>
	$(document).ready(function() {
		$('table').dataTable()
	})

	$('.view_payment').click(function() {
		uni_modal("Información de Pagos", "view_payment.php?ef_id=" + $(this).attr('data-id') + "&pid=0", "mid-large")
	})

	$('#new_fees').click(function() {
		uni_modal("Registrar venta", "manage_fee.php", "mid-large")
	})

	$('.edit_fees').click(function() {
		uni_modal("Administrar los detalles de la venta", "manage_fee.php?id=" + $(this).attr('data-id'), "mid-large")
	})

	$('.delete_fees').click(function() {
		_conf("¿Deseas eliminar estas tarifas?", "delete_fees", [$(this).attr('data-id')])
	})

	function delete_fees($id) {
		start_load()
		$.ajax({
			url: 'ajax.php?action=delete_fees',
			method: 'POST',
			data: { id: $id },
			success: function(resp) {
				if (resp == 1) {
					alert_toast("Datos eliminados exitósamente", 'success')
					setTimeout(function() {
						location.reload()
					}, 1500)
				}
			}
		})
	}

	// Funcionalidad de impresión
	$('#print').click(function() {
		var _c = $('#report-list').clone(); // Clona la tabla
		var ns = $('noscript').append(_c); // Inserta la tabla clonada en un elemento <noscript>
		var nw = window.open('', '_blank', 'width=900,height=600'); // Abre una nueva ventana
		nw.document.write('<p class="text-center"><b>Reporte de Pagos de Clientes</b></p>'); // Agrega un título
		nw.document.write(ns.html()); // Añade la tabla al nuevo documento
		nw.document.close(); // Cierra la escritura del documento
		nw.print(); // Inicia la impresión
		setTimeout(() => {
			nw.close(); // Cierra la ventana después de imprimir
		}, 500);
	});
</script>
