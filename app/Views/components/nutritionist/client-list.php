<div class="projects-section">
  <div class="projects-section-header">
    <p>Client List</p>
    <p class="time" id="currentDate">December, 12</p>
  </div>
  <div class="projects-section-line">

  </div>
  <div class="project-boxes jsListView messages" id="showClients">
    <!-- les clients apparaitront ici -->
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


<script type="text/javascript">
  var id_clicked = -1
  $(document).ready(function() {
    const projectBoxesContainer = document.getElementById('showClients');
    var sessionId = '<?php echo $_SESSION['id'] ?>';
    var additionalData = "&nutri_id=" + sessionId;

    performAjaxRequest("GET", "getNutriClients", additionalData, "", "");
  });
</script>