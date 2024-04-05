<?php
$messageDisplay = '';
if ($_SESSION['role'] == "Regular") {
  $messageDisplay = <<<HTML
  <h1>Nutritionist list</h1>
  <div>Search any nutritionist.</div>

  HTML;

  $titleNotifIcon = "Search for a nutritionist";
} else if ($_SESSION['role'] == "Nutritionist") {
  $messageDisplay = <<<HTML
  <h1>Client list</h1>
  <div>Search any client.</div>
  HTML;

  $titleNotifIcon = "Search for a client";
} else {
  $messageDisplay = <<<HTML
  <h1>Not for admins</h1>
  <div>Really no point.</div>
  HTML;
  $titleNotifIcon = "Search for a nutritionist or a client";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="<?= BASE_APP_DIR ?>/public/css/colors.css" />
  <link rel="stylesheet" href="<?= BASE_APP_DIR ?>/public/css/globals.css" />
  <link rel="stylesheet" href="<?= BASE_APP_DIR ?>/public/css/dashboard.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body>
  <!-- HEADER -->
  <?php
  include_once VIEWSDIR . DS . 'components' . DS . 'header.php';
  ?>
  <!-- BODY -->
  <div class="bg-bg" style="min-height: 100vh; padding-left: 180px">

    <div id="open-modal" class="modal-window">
      <div>
        <a href="#" title="Close" class="modal-close">Close</a>
        <?php echo $messageDisplay ?>
        <br>


        <!-- Search bar -->
        <input type="text" class="form-control" name="client-list-search" id="client-list-search" placeholder="<?php echo $titleNotifIcon ?>">

        <!-- Results -->
        <div id="client-list-results" class="pt-4" style="max-height:350px; overflow:scroll;">

        </div>
      </div>
    </div>
    <div id="open-modal-notifs" class="modal-window">
      <div>
        <a href="#" title="Close" class="modal-close">Close</a>
        <h1>Notifications</h1>
        <div>You can accept your requests here.</div>
        <br>

        <!-- Results -->
        <div id="sender-notif-list" class="pt-4" style="max-height:350px; overflow:scroll;">

        </div>
      </div>
    </div>

    <!-- BELL NOTIFICATIONS ICON -->
    <div class="position-absolute" style="right: 20px; top: 20px">
      <a href="#open-modal-notifs" id="click-to-show-notif">

        <div class="text-bg text-center d-flex align-items-center justify-content-center position-absolute" id="notif-displayer" style="font-size: 16px; height:30px; width:30px; border-radius: 100%; left: -40%; top:40%; z-index:0; background-color: #252624;"></div>
      </a>
      <a href="#open-modal" title="<?php echo $titleNotifIcon ?>">
        <img src=" <?= BASE_APP_DIR ?>/public/images/icons/bell.png" style="z-index:2;" alt="<?php echo $titleNotifIcon ?>" />
      </a>

    </div>

    <!-- REST OF THE PAGE CONTENT -->
    <div class="px-5 pt-5">
      <div class="container-fluid">
        <div class="row gap-4">
          <div class="col-lg-6" style="width: 500px;">
            <h5 class="fw-bold">Overview</h5>
            <div class="container bg-main rounded-3" style="height: 300px;">

            </div>
          </div>

          <div class="col-lg-6" style="max-width: 500px;">
            <h5 class="fw-bold">Timeline</h5>
            <div class="container bg-main rounded-3" style="height: 300px;">

            </div>
          </div>
          <?php if ($_SESSION['role'] == "Regular") : ?>
            <div class="col-lg-6 messages-section" style="max-width: 500px;">
              <h5 class="fw-bold ">Discussion</h5>
              <div href="#" class="container bg-main rounded-3 messages" id="discussion-class" style="height: 300px;">

              </div>
            </div>
          <?php endif; ?>


          <!-- form pour message -->
          <div id="open-modal-message" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Send a message</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body" style="height: 50vh;">
                  <!-- Message form -->
                  <form id="message-form" style="display: flex; align-items: center; justify-content: flex-end; gap: 12px;">
                    <input type="text" class="form-control" name="message" id="message" placeholder="Enter your message">
                    <button type="submit" class="btn btn-primary">Send</button>
                  </form>
                  <div id="conversationMessages" class="card-body msg_card_body">
                    <!-- messages apparaitront ici -->

                  </div>

                </div>
              </div>
            </div>
          </div>


          <!-- HERE WE PUT THE DAILY MEALS -->
          <div class="col-12" style="">
            <h5 class="fw-bold">Daily Tracking</h5>
            <div class="container-fluid bg-main rounded-3 pb-4" style="min-height: 300px;">
              <p class="text-bg fw-bold" style="margin-left: 28px; padding-top: 28px;">Today</p>
              <div class="bg-gray rounded p-3 d-flex flex-wrap flex-row gap-4 container-fluid" style="width: 100%">
                <?php
                include VIEWSDIR . DS . 'components' . DS . 'user' . DS . 'dashboard' . DS . 'meal.php';
                ?>

                <div class="d-flex flex-column justify-content-center bg-bg p-4 rounded" style="width: fit-content; width: 250px">
                  <img style="width: 60px; height: 60px; object-fit: cover; border-radius: 100%; margin-left: 50%; transform: translateX(-50%);" src="<?= BASE_APP_DIR ?>/public/images/icons/plus.png" alt="Icon of a plus" />
                  <p class="fw-bold text-main text-center" style="font-size: 20px; padding-top: 0px;">Add new Item</p>
                </div>
              </div>

            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= BASE_APP_DIR ?>/public/js/ajax.js"></script>
  <script src="<?= BASE_APP_DIR ?>/public/js/notification.js"></script>

</body>

</html>