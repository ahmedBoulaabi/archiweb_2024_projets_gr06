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

<style>
  .recipe-consumed {
    opacity: 0.3;
  }
</style>
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
        <input type="text" class="form-control" name="client-list-search" id="client-list-search"
          placeholder="<?php echo $titleNotifIcon ?>">

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

        <div class="text-bg text-center d-flex align-items-center justify-content-center position-absolute"
          id="notif-displayer"
          style="font-size: 16px; height:30px; width:30px; border-radius: 100%; left: -40%; top:40%; z-index:0; background-color: #252624;">
        </div>
      </a>
      <a href="#open-modal" title="<?php echo $titleNotifIcon ?>">
        <img src=" <?= BASE_APP_DIR ?>/public/images/icons/bell.png" style="z-index:2;"
          alt="<?php echo $titleNotifIcon ?>" />
      </a>
    </div>

    <!-- BASE_APP_DIR to JS -->
    <p id="BASE_APP_DIR" style="display:none">
      <?= BASE_APP_DIR ?>
    </p>

    <!-- REST OF THE PAGE CONTENT -->
    <div class="px-5 pt-5">
      <div class="container-fluid">
        <div class="row gap-4">
          <div class="col-lg-6" style="width: 500px;">
            <h5 class="fw-bold">Overview</h5>
            <div class="container bg-main rounded-3" style="height: 300px;position:relative;">
              <div class="project-box-wrapper">
                <div class="project-box" style="">
                  <div class="project-box-header">
                    <span>
                      <?= date('D, F d, Y') ?>
                    </span>
                    <div class="more-wrapper">
                      <a href="<?= BASE_APP_DIR ?>/planning" class="project-btn-more">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical">
                          <circle cx="12" cy="12" r="1" />
                          <circle cx="12" cy="5" r="1" />
                          <circle cx="12" cy="19" r="1" />
                        </svg>
                      </a>
                    </div>
                  </div>
                  <div class="project-box-content-header">
                    <p class="box-content-header" id="planNameId" style="font-size: 22px">No Active Plan</p>
                    <p class="box-content-subheader">Caloric Tracker</p>
                  </div>
                  <div class="box-progress-wrapper">
                    <p class="box-progress-header">Progress</p>
                    <div class="box-progress-bar">
                      <span class="box-progress" style="width: 0%; background-color: #ff942e"></span>
                    </div>
                    <p class="box-progress-percentage" id="progress-val">0%</p>
                  </div>
                  <div class="project-box-footer">
                    <div></div>
                    <div class="days-left" id="days-left" style="color: #fff;">
                      0 Days Left
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-6" style="max-width: 600px;">
            <h5 class="fw-bold">Timeline</h5>
            <canvas class="container bg-main rounded-3" id="timeline-graph" style="max-height: 300px;">

            </canvas>
          </div>
          <?php if ($_SESSION['role'] == "Regular") : ?>
            <div class="col-lg-6 messages-section" style="max-width: 500px;">
              <h5 class="fw-bold ">Discussion</h5>
              <div href="#" class="container bg-main rounded-3 messages" id="discussion-class" style="height: 120px;">

              </div>
            </div>
          <?php endif; ?>


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


          <!-- HERE WE PUT THE DAILY MEALS -->
          <div class="col-12" style="">
            <h5 class="fw-bold">Daily Tracking</h5>
            <div class="container-fluid bg-main rounded-3 pb-4" id="plan-container" style="min-height: 300px;">
              <p class="text-bg fw-bold" id="no-plan" style="margin-left: 28px; padding-top: 28px;">No Active Plan</p>
              <!-- <p class="text-bg fw-bold" style="margin-left: 28px; padding-top: 28px;">Today</p> -->
              <!-- <div class="bg-gray rounded p-3 d-flex flex-wrap flex-row gap-4 container-fluid" id="recipes-container"
                style="width: 100%">
                <?php
                include VIEWSDIR . DS . 'components' . DS . 'user' . DS . 'dashboard' . DS . 'meal.php';
                ?>

                <div class="d-flex flex-column justify-content-center bg-bg p-4 rounded" style="width: fit-content; width: 250px">
                  <img style="width: 60px; height: 60px; object-fit: cover; border-radius: 100%; margin-left: 50%; transform: translateX(-50%);" src="<?= BASE_APP_DIR ?>/public/images/icons/plus.png" alt="Icon of a plus" />
                  <p class="fw-bold text-main text-center" style="font-size: 20px; padding-top: 0px;">Add new Item</p>
                </div>
              </div> -->

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


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script type="text/javascript">
  performAjaxRequest(
    "POST",
    "UserHavePlan",
    "",
    "",
    ""
  );

  var recipes = JSON.parse(localStorage.getItem('recipes')) || [];
  var planInfo = JSON.parse(localStorage.getItem('planInfo')) || [];

  var totalDays = planInfo.total_length ? planInfo.total_length : 0;
  var caloriesPerDay = new Array(totalDays).fill(0);


  recipes.forEach(recipe => {
    if (recipe.date && recipe.calories) {
      if (recipe.date - 1 < caloriesPerDay.length) {
        // console.log(recipe)
        if (recipe.consumed == 1) {
          caloriesPerDay[recipe.date - 1] += parseInt(recipe.calories, 10);
        }
      }
    }
  });


  var labels = caloriesPerDay.map((_, index) => `Day ${index + 1}`);

  // console.log(labels)

  document.addEventListener('DOMContentLoaded', function () {
    var ctx = document.getElementById('timeline-graph').getContext('2d');
    var timelineChart = new Chart(ctx, {
      type: 'bar', // Type of graph
      data: {
        labels: labels, // Days of the plan
        datasets: [{
          label: 'Calories Consumed per Day',
          data: caloriesPerDay, // Total calories per day
          backgroundColor: '#ff942eaa',
          borderColor: '#ff942e',
          borderWidth: 2,
          borderRadius: 4
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: 'Calories',
              color: 'white', // Title color
              font: {
                size: 10 // Adjust title font size
              }
            },
            ticks: {
              color: 'white', // Ticks color
              stepSize: 100, // Set scale to increment by 500 calories per tick
              font: {
                size: 8 // Adjust tick label font size
              }
            },
            grid: {
              color: 'rgba(255, 255, 255, 0.1)' // Grid line color
            }
          },
          x: {
            title: {
              display: true,
              text: 'Day',
              color: 'white', // Title color
              font: {
                size: 10 // Adjust title font size
              }
            },
            ticks: {
              color: 'white', // Ticks color
              font: {
                size: 8 // Adjust tick label font size
              }
            },
            grid: {
              color: 'rgba(255, 255, 255, 0.1)' // Grid line color
            }
          }
        },
        plugins: {
          legend: {
            display: true,
            position: 'top',
            labels: {
              color: 'white', // Legend labels color
              font: {
                size: 10 // Adjust legend font size
              }
            }
          },
        },
        maintainAspectRatio: false, // Ensures chart does not maintain the default aspect ratio
      }
    });
  });



  // RECIPES DISPLAY
  document.addEventListener('DOMContentLoaded', function () {
    const recipes = JSON.parse(localStorage.getItem('recipes')) || [];
    const planInfo = JSON.parse(localStorage.getItem('planInfo')) || {};
    const totalDays = planInfo.total_length || 0;
    const planContainer = document.getElementById('plan-container');
    const BASE_APP_DIR = document.getElementById("BASE_APP_DIR").innerText.trim();

    const today = new Date();
    const creationDate = new Date(planInfo.creation_date);
    const diffTime = today - creationDate;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    const currentDay = Math.min(diffDays, totalDays);

    for (let day = 1; day <= currentDay; day++) {
      document.getElementById("no-plan").style.display = "none";
      let dayDiv = document.createElement('div');
      dayDiv.className = 'container-fluid bg-main rounded-3 pb-4';
      dayDiv.style = 'min-height: 300px;';

      let dayLabel = day === currentDay ? 'Today' : `Day ${day}`;
      let dayTitle = document.createElement('p');
      dayTitle.className = 'text-bg fw-bold';
      dayTitle.style = 'margin-left: 28px; padding-top: 28px;';
      dayTitle.textContent = dayLabel;

      let recipesContainer = document.createElement('div');
      recipesContainer.className = 'bg-gray rounded p-3 d-flex flex-wrap flex-row gap-4 container-fluid';
      recipesContainer.style = 'width: 100%';
      recipesContainer.id = `day-${day}-recipes`;

      dayDiv.appendChild(dayTitle);
      dayDiv.appendChild(recipesContainer);

      console.log(recipes)

      let dayRecipes = recipes.filter(recipe => recipe.date === day);
      dayRecipes.forEach(recipe => {
        var recipeElement = document.createElement('div');
        recipeElement.className = 'flex flex-column justify-content-start bg-bg p-4 rounded';
        recipeElement.style = 'width: fit-content; max-width: 250px; min-width: 250px; align-items:center; cursor:pointer';
        if (recipe.consumed == 1) {
          recipeElement.className += " recipe-consumed"
        }
        recipeElement.setAttribute('data-recipe-id', recipe.id);

        var imgPath = recipe.image_url ? `${BASE_APP_DIR}/public/images/recipesImages/${recipe.image_url}` :
          "https://www.allrecipes.com/thmb/5SdUVhHTMs-rta5sOblJESXThEE=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/11691-tomato-and-garlic-pasta-ddmfs-3x4-1-bf607984a23541f4ad936b33b22c9074.jpg";

        recipeElement.innerHTML = `
                <img style="width: 200px; height: 200px; object-fit: cover; border-radius: 100%;" src="${imgPath}" />
                <div class="mt-4">
                    <p style="margin: 0;">${recipe.calories || "400"} Cal</p>
                    <p class="fw-bold" style="font-size: 20px; padding-top: 0px">${recipe.name || "Default Recipe Name"}</p>
                </div>
            `;
        recipesContainer.appendChild(recipeElement);

        // Attach click event listener to each recipe card
        recipeElement.addEventListener('click', function () {
          console.log(`Recipe ${recipe.id} clicked`);
          var additionalData = "&recipe_id=" + recipe.id;
          recipeElement.classList.toggle("recipe-consumed");
          performAjaxRequest(
            "POST",
            "toggleRecipeConsumed",
            additionalData,
            "Recipe consumed status toggled successfully!",
            ""
          );
        });
      });

      // Add Item
      // let addItemDiv = document.createElement('div');
      // addItemDiv.className = 'd-flex flex-column justify-content-center bg-bg p-4 rounded';
      // addItemDiv.style = 'width: fit-content; width: 250px; min-height:342px';
      // addItemDiv.innerHTML = `
      //       <img style="width: 60px; height: 60px; object-fit: cover; border-radius: 100%; margin-left: 50%; transform: translateX(-50%);" src="${BASE_APP_DIR}/public/images/icons/plus.png" alt="Icon of a plus" />
      //       <p class="fw-bold text-main text-center" style="font-size: 20px; padding-top: 0px;">Add new Item</p>
      //   `;
      // recipesContainer.appendChild(addItemDiv);

      planContainer.prepend(dayDiv);
    }
  });
  
$(document).ready(function() {
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