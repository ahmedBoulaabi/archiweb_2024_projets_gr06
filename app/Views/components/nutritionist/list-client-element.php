<?php
// Define an associative array with background colors based on the goal
$backgroundColors = [
  'gain-weight-normal' => '#c8f7dc',
  'lose-weight-fast' => '#ffd3e2',
  'lose-weight-normal' => '#e9e7fd'
];

$spanColors = [
  'gain-weight-normal' => '#34c471',
  'lose-weight-fast' => '#df3670',
  'lose-weight-normal' => '#4f3ff0'
];
foreach ($usersProgress as $row) :
  //var_dump($row->plan_progress);



  // Get the background color based on the goal
  // Get the background color based on the goal
  $goal = !is_null($row["goal"]) ? htmlspecialchars($row["goal"]) : null;

  $backgroundColor = isset($backgroundColors[$goal]) ? $backgroundColors[$goal] : '#FFFFFF';
  $spanColor = isset($spanColors[$goal]) ? $spanColors[$goal] : '#FFFFFF';

?>

  <style>
    .popup-btn-edit {
      background-color: rgba(0, 40, 120, 1);
      color: white;
      padding: 6px;
      padding-left: 15px;
      margin-bottom: 4px;
      border-radius: 6px;
      cursor: pointer;
      transition: all 150ms ease-in-out;
    }

    .popup-btn-edit:hover {
      opacity: 0.5;
      transition: all 150ms ease-in-out;
    }

    .popup-btn-delete {
      background-color: rgba(220, 0, 0, 1);
      color: white;
      padding: 6px;
      padding-left: 15px;
      margin-bottom: 0;
      border-radius: 6px;
      cursor: pointer;
      transition: all 150ms ease-in-out;
    }

    .popup-btn-delete:hover {
      opacity: 0.5;
      transition: all 150ms ease-in-out;
    }
  </style>
  <div class="project-box-wrapper">
    <div class="project-box" style="background-color: <?= $backgroundColor ?>;">
      <div class="project-box-header">
        <?php if (!is_null($row["plan_creation_date"])) : ?>
          <span><?= htmlspecialchars($row["plan_creation_date"]) ?></span>
        <?php endif; ?>
        <div class="more-wrapper">
          <button class="project-btn-more">
            <div class="position-absolute container hidden options-container" style="width:200px; height: 100px; padding:6px; z-index: 10; background-color:white; top:-15px; left:-200px; border-radius:6px">
              <p class="popup-btn-edit">    <a class="dropdown-item" href='nutritionist-dashboard?tab=clientPlan&clientId=<?= $row->id ?>'>See plan</a></p>
              <p class="popup-btn-delete" id="<?= htmlspecialchars($row["user_id"]) ?>">Delete Client</p>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical">
              <circle cx="12" cy="12" r="1" />
              <circle cx="12" cy="5" r="1" />
              <circle cx="12" cy="19" r="1" />
            </svg>
          </button>
        </div>
      </div>
      <div class="project-box-content-header" style="display: flex; align-items: center;">
        <img src="<?= BASE_APP_DIR ?><?= htmlspecialchars($row["img"]) ?>" alt="profile image" class="img" style="max-width: 50px; height: auto; margin-right: 10px;">
        <div>
          <p class="box-content-header"><?= htmlspecialchars($row["fullname"]) ?> </p>
          <?php if (!is_null($row["goal"])) : ?>
            <p class="box-content-subheader"><?= htmlspecialchars($row["goal"]) ?> </p>
          <?php endif; ?>
        </div>
      </div>
      <div class="box-progress-wrapper" style="margin-left: auto; max-width: 1150px;">
        <?php if ($row["plan_progress"] !== '0.00%') : ?>
          <p class="box-progress-header">Progress</p>
          <div class="box-progress-bar">
            <span class="box-progress" style="width: <?= htmlspecialchars($row["plan_progress"]) ?>; background-color: <?= $spanColor ?>;"></span>
          </div>
          <p class="box-progress-percentage"><?= htmlspecialchars($row["plan_progress"]) ?></p>
        <?php else : ?>
          <p class="box-progress-header">This client doesn't have a plan</p>
        <?php endif; ?>
      </div>


      <div class="project-box-footer">
        <!-- <div class="participants">
          <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=2550&q=80" alt="participant">
          <img src="https://images.unsplash.com/photo-1503023345310-bd7c1de61c7d?ixid=MXwxMjA3fDB8MHxzZWFyY2h8MTB8fG1hbnxlbnwwfHwwfA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=900&q=60" alt="participant">
          <button class="add-participant" style="color: <?= $spanColor ?>;">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus">
              <path d="M12 5v14M5 12h14" />
            </svg>
          </button>
        </div> -->
        <!-- <div class="days-left" style="color: <?= $spanColor ?>;">
          2 Days Left
        </div> -->
      </div>
    </div>
  </div>
<?php endforeach; ?>


<script src="<?= BASE_APP_DIR ?>/public/js/ajax.js"></script>
<script type="text/javascript">
  $(document).ready(function() {



    // Updated delete request using performAjaxRequest
    $("body").on("click", ".popup-btn-delete", function(e) {
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
          performAjaxRequest("POST", "deleteClient", "&del_id=" + del_id, "Deleted!", "Client deleted successfully.");
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

  });
</script>