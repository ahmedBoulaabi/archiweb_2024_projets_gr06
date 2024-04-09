<table>
  <thead>
    <tr>
      <th>User</th>
      <th>Email</th>
      <th>Role</th>
      <th>Status</th>
      <th>Action</th>
    </tr>
  </thead>

  <tbody>
    <?php foreach ($data as $row) : ?>
      <tr>
        <td>
          <img src="<?= BASE_APP_DIR ?><?= htmlspecialchars($row->img) ?>" alt="">
          <p><?= htmlspecialchars($row->fullname) ?></p>
        </td>
        <td><?= htmlspecialchars($row->email) ?></td>
        <td><?= htmlspecialchars($row->role) ?></td>
        <td>
          <?php if ($row->active == 1) : ?>
            <span class="status completed">Active</span>
          <?php elseif ($row->active == 2) : ?>
            <span class="status pending">Demande en cours</span>
          <?php else : ?>
            <span class="status deleted">Inactive</span>
          <?php endif; ?>
        </td>
        <td>
          <a href="#" title="View Details" class="text-success infoBtn" id="<?= $row->id ?>"><i class='bx bxs-user-detail'></i></a>
          <a href="#" title="Edit" class="editBtn" id="<?= $row->id ?>"><i class='bx bxs-edit'></i></a>
          <a href="" style="color: var(--danger)" class="delBtn" id="<?= $row->id ?>"><i class='bx bxs-trash'></i></a>
        </td>
      </tr>
    <?php endforeach; ?>

  </tbody>

</table>

<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editUserModalLabel">Edit User Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form action="" method="post" id="edit-user-form" enctype="multipart/form-data">
      <form action="" method="post" id="edit-user-form" enctype="multipart/form-data">
  <input type="text" id="edit_user_id" name="edit_user_id">
  <div class="form-group">
    <label for="edit_fname">First Name</label>
    <input type="text" class="form-control" id="edit_fname" name="edit_fname" required>
  </div>
  <div class="form-group">
    <label for="edit_email">Email</label>
    <input type="email" class="form-control" id="edit_email" name="edit_email" required>
  </div>
  <div class="form-group">
    <label for="edit_gender">Gender</label>
    <select class="form-control" id="edit_gender" name="edit_gender" required>
      <option value="male">Male</option>
      <option value="female">Female</option>
      <option value="other">Other</option>
    </select>
  </div>
  <div class="form-group">
    <label for="edit_goal">Goal</label>
    <select class="form-control" id="edit_goal" name="edit_goal" required>
      <option value="lose-weight-fast">Lose weight fast</option>
      <!-- Ajoute ici d'autres objectifs selon les besoins -->
    </select>
  </div>
  <div class="form-group">
    <label for="edit_age">Age</label>
    <input type="number" class="form-control" id="edit_age" name="edit_age" required>
  </div>
  <div class="form-group">
    <label for="edit_role">Role</label>
    <input type="text" class="form-control" id="edit_role" name="edit_role" required>
  </div>
  <div class="form-group">
    <label for="edit_height">Height (cm)</label>
    <input type="number" class="form-control" id="edit_height" name="edit_height" required>
  </div>
  <div class="form-group">
    <label for="edit_weight">Weight (kg)</label>
    <input type="number" class="form-control" id="edit_weight" name="edit_weight" required>
  </div>
  <div class="form-group">
    <label for="edit_caloriesgoal">Daily Calorie Goal</label>
    <input type="number" class="form-control" id="edit_caloriesgoal" name="edit_caloriesgoal" required>
  </div>
  <div class="form-group">
    <label for="edit_imageUpload">Profile Picture</label>
    <input type="file" id="edit_imageUpload" name="edit_imageUpload" accept=".png, .jpg, .jpeg" class="form-control">
  </div>
  <input type="submit" name="updateUser" id="updateUser" value="Save Changes" class="btn btn-secondary btn-block">
</form>

      </div>
    </div>
  </div>
</div>

<script src="<?= BASE_APP_DIR ?>/public/js/ajax.js"></script>
<script type="text/javascript">
  $(document).ready(function() {



    // Updated delete request using performAjaxRequest
    $("body").on("click", ".delBtn", function(e) {
      e.preventDefault();
      var tr = $(this).closest('tr');
      var del_id = $(this).attr('id');

      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
      }).then((result) => {
        if (result.value) {
          performAjaxRequest("POST", "deleteUser", "&del_id=" + del_id, "Deleted!", "User deleted successfully.");
        }
      });
    });


    //show user details
    $("body").on("click", ".infoBtn", function(e) {
      e.preventDefault();
      info_id = $(this).attr('id');
      var additionalData = "&info_id=" + info_id;

      performAjaxRequest("GET", "getUserDetails", additionalData, "", "");
    })

    $("#updateUser").click(function(e) {
      e.preventDefault(); // Prévenir la soumission par défaut du formulaire
      if ($("#edit-user-form")[0].checkValidity()) {
        var formData = new FormData($("#edit-user-form")[0]); // Créer un objet FormData à partir du formulaire
        performAjaxWithImage('edit-user-form', 'updateUser', 'Updated!', 'User updated successfully.');
      }
    });

    $("body").on("click", ".editBtn", function(e) {
    e.preventDefault();
    var edit_id = $(this).attr('id'); // L'ID de l'utilisateur à modifier
    var additionalData = "&info_id=" + edit_id;
    // Utilise 'performAjaxRequest' pour charger les détails de l'utilisateur dans la modal
    
    performAjaxRequest("GET", "loadUserDetails", additionalData,"","");   
    $("#editUserModal").modal("show");
    
  });


  });
</script>