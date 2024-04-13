<main>
  <div class="header">
    <div class="left">
      <h1>Dashboard</h1>

    </div>

  </div>

  <!-- Insights -->
  <ul class="insights">
    <li>
      <i class='bx bx-user'></i>
      <span class="info">
        <h3 id="usersNumber">0</h3>
        <p>Number users</p>
      </span>
    </li>
    <li>
      <i class='bx bxs-face'></i>
      <span class="info">
        <h3 id="nutritionistNumber">0</h3>
        <p>Number nutritionist</p>
      </span>
    </li>
    <li>
      <i class='bx bxs-pizza'></i>
      <span class="info">
        <h3 id="countRecipes">0</h3>
        <p>Number recipes</p>
      </span>
    </li>

  </ul>
  <!-- End of Insights -->

  <div class="bottom-data">
    <!-- Orders -->
    <div class="orders">
      <div class="header">
        <i class='bx bx-receipt'></i>
        <h3>Nutritionist Requests</h3>
        <!-- <i class='bx bx-filter'></i>
        <i class='bx bx-search'></i> -->
      </div>
      <div id="showNutriRequests">

      </div>

    </div>
    <!-- End of Orders -->

    <!-- Reminders -->

    </ul>
  </div>
  <!-- End of Reminders -->
  </div>
</main>

<script src="<?= BASE_APP_DIR ?>/public/js/ajax.js"></script>


<script type="text/javascript">
  $(document).ready(function() {

    performAjaxRequest(
      "GET",
      "getNutriRequests",
      "",
      "",
      ""
    );


    performAjaxRequest(
      "GET",
      "countRegularUsers",
      "",
      "",
      ""
    );
    performAjaxRequest(
      "GET",
      "countNutritionistUsers",
      "",
      "",
      ""
    );
    performAjaxRequest(
      "GET",
      "countRecipes",
      "",
      "",
      ""
    );
  });
</script>