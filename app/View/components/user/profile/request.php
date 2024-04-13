<!-- PROMOTION REQUEST -->
<div class="container-fluid pt-4">
  <h4>Promotion Request</h4>
  <p>Send a request to the administrator to change your role to a nutritionist.</p>
  <!-- FORM -->
  <form>
    <!-- REQUEST BUTTON -->
    <div class="d-grid gap-2 col-6 mt-4">
      <button id="request-btn" class="btn btn-primary bg-main border-main" type="submit">
        Request Role Change to Nutritionist
      </button>
    </div>
  </form>
</div>


<script type="text/javascript">
  $("#request-btn").click(function(e) {
      e.preventDefault();

      performAjaxRequest(
        "POST",
        "requestPromotion",
        "",
        "Request Sent!",
        ""
      );


    });
</script>