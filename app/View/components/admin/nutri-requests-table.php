<table>
  <thead>
    <tr>
      <th>User</th>
      <th>Email</th>
      <th>Date</th>
      <th>Status</th>
      <th>Action</th>
    </tr>
  </thead>

  <tbody>
    <?php foreach ($data as $row) : ?>
      <tr>
        <td>
          <img src="<?= BASE_APP_DIR ?><?= htmlspecialchars($row["img"]) ?>" alt="">
          <p><?= htmlspecialchars($row["fullname"]) ?></p>
        </td>
        <td><?= htmlspecialchars($row["email"]) ?></td>
        <td><?= htmlspecialchars($row["created_date"]) ?></td>
        <td>
          <?php if ($row["etat"] == "accepted") : ?>
            <span class="status completed">Accepted</span>
          <?php elseif ($row["etat"]  == "pending") : ?>
            <span class="status pending">Pending</span>
          <?php else : ?>
            <span class="status deleted">Refused</span>
          <?php endif; ?>
        </td>
        <td>
          <a href="#" title="Approve Request" class="text-success infoBtn" id="<?= htmlspecialchars($row["id"]) ?>"><i class='bx bx-check-circle'></i></a>
          <a href="" style="color: var(--danger)" class="delBtn" id="<?= htmlspecialchars($row["id"])  ?>"><i class='bx bxs-x-circle'></i></a>
          <a href=""><i class='bx bx-download'></i></a>
        </td>
      </tr>
    <?php endforeach; ?>

  </tbody>

</table>




</div>
</div>
</div>
<script src="<?= BASE_APP_DIR ?>/public/js/ajax.js"></script>
<script type="text/javascript">
  // Updated delete request using performAjaxRequest
  $("body").on("click", ".infoBtn", function(e) {
    e.preventDefault();
    var tr = $(this).closest('tr');
    var del_id = $(this).attr('id');

    Swal.fire({
      title: "Accept Request?",
      text: "Are you sure you want to accept this request?",
      icon: "question", // Changed icon from 'warning' to 'question' for neutrality.
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, accept it!", // Changed text to reflect the action better.
      cancelButtonText: "No, cancel!" // It's good to provide clear options.
    }).then((result) => {
      if (result.value) {
        performAjaxRequest("POST", "acceptRequest", "&acc_id=" + del_id, "Accepted!", "Request accepted successfully.");
      }
    });
  });


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
        performAjaxRequest("POST", "deleteRequest", "&del_id=" + del_id, "Deleted!", "User deleted successfully.");
      }
    });
  });
</script>