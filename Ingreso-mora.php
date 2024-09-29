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
                        <a class="btn btn-primary" href="javascript:void(0)" id="new_mora_ingreso">
                            <i class="fa fa-plus"></i> Ingresar
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                       
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    
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

    $('#new_mora_ingreso').click(function() {
        uni_modal("Nuevo Pago ", "manage_mora.php", "mid-large");
    });

    $('.view_payment').click(function() {
        uni_modal("Información de Pago", "view_payment.php?ef_id=" + $(this).attr('data-ef_id') + "&pid=" + $(this).attr('data-id'), "mid-large");
    });

    $('.edit_payment').click(function() {
        uni_modal("Gestionar Pago", "manage_payment.php?id=" + $(this).attr('data-id'), "mid-large");
    });

    $('.delete_payment').click(function() {
        _conf("¿Deseas eliminar este pago?", "delete_payment", [$(this).attr('data-id')]);
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
