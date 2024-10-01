<style>
  .logo {
    margin: auto;
    font-size: 20px;
    background: white;
    padding: 7px 11px;
    border-radius: 50% 50%;
    color: #000000b3;
  }
</style>
<div id="page">
</div>


<div id="loading"></div>
<nav class="navbar navbar-light fixed-top">
  <div class="container-fluid mt-2 mb-2">
    <div class="col-lg-12">
      <div class="float-right">
        <div class=" dropdown mr-4">
          <a class="dropdown-item" href="ajax.php?action=logout"><i class="fa fa-power-off"></i> Logout</a>
        </div>
        <a href="https://www.youtube.com/playlist?list=PLj8KEdMpxZ7wSHd0zYMh_Z8MW2oAVtm5Q" target="_blank"> Manual de uso</a>
      </div>
    </div>
  </div>

</nav>


<script>
  $('#manage_my_account').click(function() {
    uni_modal("Manage Account", "manage_user.php?id=<?php echo $_SESSION['login_id'] ?>&mtype=own")
  })
</script>