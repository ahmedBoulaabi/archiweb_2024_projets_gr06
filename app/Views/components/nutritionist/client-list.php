<div class="projects-section">
  <div class="projects-section-header">
    <p>Client List</p>
    <p class="time" id="currentDate">December, 12</p>
  </div>
  <div class="projects-section-line">

  </div>
  <div class="project-boxes jsListView" id="showClients">

  </div>
</div>

<!-- form pour message -->
<div id="open-modal-message" class="modal-window">
  <div style="height:50vh;">
    <a href=" #" title="Close" class="modal-close">Close</a>
    <h1>Send a message</h1>
    <br>
    <!-- Message form -->
    <form id="message-form" style="display: flex; align-items: center; justify-content: flex-end; gap: 12px; ">
      <input type="text" class="form-control" name="message" id="message" placeholder="Enter your message">
      <button type="submit" class="btn btn-primary">Send </button>
    </form>
    <!-- message précédents apparaitront ici -->
  </div>
</div>



<script src="<?= BASE_APP_DIR ?>/public/js/ajax.js"></script>


<script type="text/javascript">
  var id_clicked = -1
  $(document).ready(function() {
    console.log("dans client-list")
    const projectBoxesContainer = document.getElementById('showClients');

    projectBoxesContainer.addEventListener('click', function(event) {
      console.log("dans funciton")
      const clickedElement = event.target.closest('.project-box-wrapper');
      // Récupérer l'ID de l'élément cliqué
      if (clickedElement) {
        const id = clickedElement.dataset.id;
        console.log('ID de l\'élément cliqué :', id);
        id_clicked = id
      }

      if (event.target.id === "sendMessage") {
        // display conversation
        console.log("Vous avez cliqué sur l'élément 'Envoyer un message'");

      }
    });

    $("#message-form").on("submit", function(event) {
      event.preventDefault();
      var message = $("#message").val();
      console.log(message);
      var additionalData = "&content=" + message + "&targetID=" + id_clicked
      performAjaxRequest("POST", "sendMessage", additionalData, "", "");
    });

    var sessionId = '<?php echo $_SESSION['id'] ?>';
    var additionalData = "&nutri_id=" + sessionId;

    performAjaxRequest("GET", "getNutriClients", additionalData, "", "");
  });
</script>