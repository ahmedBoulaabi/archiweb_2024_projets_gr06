<style>
  .img {
    width: 50px;
    /* Adjust the width as needed */
    height: 50px;
    /* Adjust the height as needed */
    border-radius: 50%;
    /* Makes the image circular */
    object-fit: cover;
    /* Ensures the image covers the entire container */
  }
</style>
<div class="projects-section">
  <div class="projects-section-header">
    <p>Clients to manage</p>
    <p class="time">
      <?= date('F d, Y') ?>
    </p>
  </div>
  <div class="projects-section-line">
    <div class="projects-status">
      <div class="item-status">
        <span class="status-number" id="in-progress">0</span>
        <span class="status-type">In Progress</span>
      </div>
      <div class="item-status">
        <span class="status-number" id="plans-finished">0</span>
        <span class="status-type">Plans Finished</span>
      </div>
      <div class="item-status">
        <span class="status-number" id="total-clients">0</span>
        <span class="status-type">Total Clients</span>
      </div>
    </div>

    <p id="baseAppDir" style="display: none;">
      <?= BASE_APP_DIR ?>
    </p>

  </div>
  <div class="project-boxes jsGridView" id="project-boxes">


  </div>
</div>
<div class="messages-section">
  <button class="messages-close">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle">
      <circle cx="12" cy="12" r="10" />
      <line x1="15" y1="9" x2="9" y2="15" />
      <line x1="9" y1="9" x2="15" y2="15" />
    </svg>
  </button>
  <div class="projects-section-header">
    <p>Client Messages</p>
  </div>
  <div class="messages">

  </div>
</div>

<!-- form pour message -->
<div id="open-modal-message" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Send a message</h5>

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
<script src="<?= BASE_APP_DIR ?>/public/js/ajax.js"></script>

<script>
  $(document).ready(function() {
    var sessionId = '<?php echo $_SESSION['id'] ?>';
    var additionalData = "&nutri_id=" + sessionId;
    console.log(additionalData)
    performAjaxRequest("GET", "getUserProgress", additionalData, "", "");

    function getDiscussion() {
      performAjaxRequest(
        "GET",
        "getDiscussion",
        "",
        "",
        ""
      );
    }

    getDiscussion()
  });
</script>