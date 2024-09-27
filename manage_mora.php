<div class="container-fluid">
    <form action="" id="manage-student">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <div id="msg" class="form-group"></div>
        <div class="form-group">
            <label for="" class="control-label">CI</label>
            <input type="text" class="form-control" name="id_no" value="<?php echo isset($id_no) ? $id_no : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="" class="control-label">Nombre</label>
            <input type="text" class="form-control" name="name" value="<?php echo isset($name) ? $name : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="" class="control-label">Telefono / Celular</label>
            <input type="text" class="form-control" name="contact" value="<?php echo isset($contact) ? $contact : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="" class="control-label">Correo</label>
            <input type="email" class="form-control" name="email" value="<?php echo isset($email) ? $email : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="" class="control-label">Dirección</label>
            <textarea name="address" id="" cols="30" rows="3" class="form-control" required=""><?php echo isset($address) ? $address : '' ?></textarea>
        </div>
    </form>
</div>