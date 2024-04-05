function handleAjaxError(jqXHR, textStatus, errorThrown) {
  console.error("AJAX Error:", textStatus, errorThrown, jqXHR.responseText);
  Swal.fire({
    title: "AJAX error !",
    text: "Please try again. (" + textStatus + " : " + jqXHR.responseText + ")",
    icon: "error",
  });
}

function handleAjaxResponse(
  action,
  response,
  successTitle,
  successMessage,
  logout
) {
  switch (action) {
    case "login":
      console.log("on va vers first-login");
      redirectHref = "first-login";
      break;
    case "first-login":
      redirectHref = "dashboard";
      break;
    case "update":
      redirectHref = "update";
      break;
    case "addRecipe":
      redirectHref = "recipes-list";
      break;
    case "addNewUser":
      redirectHref = "dashboardAdmin?tab=usersList";
      $("#form-data")[0].reset();
      break;
    case "addNewRecipe":
      redirectHref = "dashboardAdmin?tab=recipesList";
      $("#recipe-form-data")[0].reset();
      break;
    case "updateRecipe":
      redirectHref = "dashboardAdmin?tab=recipesList";
      break;
    case "deleteRecipe":
      redirectHref = "dashboardAdmin?tab=recipesList";
      break;
    default:
      redirectHref = "login";
      break;
  }
  if (response.success) {
    Swal.fire({
      title: successTitle,
      text: successMessage,
      icon: "success",
    }).then(function () {
      if (
        redirectHref != "update" &&
        redirectHref != "recipes-list" &&
        action != "deleteUser" &&
        action != "insertPlan" &&
        action != "addNewRecipe" &&
        action != "updateRecipe"
      ) {
        window.location.href = redirectHref;
      } else if (redirectHref == "recipes-list") {
        window.parent.rafraichirPage();
      } else {
        console.log("refresh");
        window.location.reload(true);
      }
    });
    if (!logout && action != "deleteUser") {
      $("#form-data")[0].reset();
    }
  } else {
    console.log(response);
    Swal.fire({
      title: "Operation failed!",
      text: response.message,
      icon: "error",
    });
  }
}

function performAjaxRequest(
  requestType,
  action,
  additionalData,
  successTitle,
  successMessage
) {
  $.ajax({
    url: "index.php",
    type: requestType,
    data: $("#form-data").serialize() + "&action=" + action + additionalData,
    dataType: "json",
    success: function (response) {
      console.log("action:  " + action);

      switch (action) {
        case "showAllRecipes":
          $("#RecipeList").html(response.message);
          break;
        case "getNutriClients":
          $("#showClients").html(response.message);
          break;

        case "getUserProgress":
          console.log("res "+response.data);
          $("#total-clients").text(response.data.total_users);
          $("#in-progress").text(response.data.not_completed);
          $("#plans-finished").text(response.data.completed);
          $("#project-boxes").empty();
          var baseAppDir = document
            .getElementById("baseAppDir")
            .innerText.trim();
          response.data.users_progress.forEach(function (client) {
            var clientHtml = `
              <div class="project-box-wrapper">
                <div class="project-box" style="background-color: #fee4cb;">
                  <div class="project-box-header">
                    <span>${
                      client.plan_creation_date
                    }</span> <!-- Assuming 'date' is part of your client object -->
                    <div class="more-wrapper">
                      <!-- Button and SVG omitted for brevity -->
                    </div>
                  </div>
                  <div class="project-box-content-header">
                  <img src="${
                    client.img
                      ? baseAppDir + client.img
                      : "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=2550&q=80"
                  }" alt="profile image" class="img">

                    <p class="box-content-header">${client.fullname}</p>
                    <p class="box-content-subheader">${client.goal}</p>
                  </div>
                  <div class="box-progress-wrapper">
                    <p class="box-progress-header">Progress</p>
                    <div class="box-progress-bar">
                      <span class="box-progress" style="width: ${
                        client.plan_progress
                      }; background-color: #ff942e"></span>
                    </div>
                    <p class="box-progress-percentage">${
                      client.plan_progress
                    }</p>
                  </div>
                </div>
              </div>`;
            $(".project-boxes").append(clientHtml);
          });

          break;
        case "getAllUsers":
          $("#showUser").html(response.message);
          $("table").DataTable({ order: [0, "desc"] });
          break;
        case "getAllRecipes":
          $("#showRecipes").html(response.message);
          $("table").DataTable({ order: [0, "desc"] });
        case "countRegularUsers":
          $("#usersNumber").html(response.count);
          break;
        case "nutriRecipesCount":
          $("#nutriRecipesCount").html(response.count);
          break;
        case "countNutritionistUsers":
          $("#nutritionistNumber").html(response.count);
          break;
        case "nutriCurrentClients":
          $("#nutriCurrentClients").html(response.count);
          break;
        case "countRecipes":
          $("#countRecipes").html(response.count);
          break;
        case "planSearchForRecipe":
          var data = response.data;
          $("#plan-recipe-results").html(data);
          break;
        case "clientSearch":
          var data = response.data;
          $("#client-list-results").html(data);
          break;
        case "sendNotification":
          console.log(response.data);
          console.log("notification envoyée");

          var divID = additionalData.replace("&searchValue=", "");
          var divID = additionalData.replace("&receiverId=", "");

          console.log(divID);
          var userDiv = $("#user-" + divID);
          userDiv.addClass("temp-bg-color");

          setTimeout(function () {
            userDiv.removeClass("temp-bg-color");
          }, 2000);
          break;
        case "countNotification":
          const element = document.createElement("div");
          element.innerHTML = response.data;
          $("#notif-displayer").append(element);
          console.log("nombre de notifications: " + response.data);
          break;
        case "getUsersFromNotifications":
          $("#sender-notif-list").html(response.data);
          break;
        case "updateNotification":
          console.log("requestType log: " + response.requestType);
          var sender = response.data;
          console.log("nom de l'user sender: " + sender.fullname);
          console.log("success: " + response.success);

          if (response.requestType == "insert") {
            var statusText = "Accepted";
            var bgColor = "#75d44c";
          } else if (response.requestType == "delete") {
            var statusText = "Declined";
            var bgColor = "#F88F99";
          }

          $("#notif-user-" + sender.id).html(
            '<p style="width: 20%; margin: 10px 0">' +
              sender.fullname +
              "</p>" +
              '<p style="width: 20%; margin: 15px 0" id="status-request-<?php echo $row->id ?>">' +
              statusText +
              "</p>"
          );
          $("#notif-user-" + sender.id).css("background-color", bgColor);
          break;

        case "getUserDetails":
          Swal.fire({
            title: `<strong>User Info: ID(${response.data.id})</strong>`,
            icon: "info",
            html: `
              <div style="text-align: left;">
                <b>Full Name:</b> ${response.data.fullname}<br>
                <b>Email:</b> ${response.data.email}<br>
                <b>Gender:</b> ${response.data.gender}<br>
                <b>Creation Date:</b> ${response.data.creation_date}<br>
                <b>Goal:</b> ${response.data.goal}<br>
                <b>Age:</b> ${response.data.age}<br>
                <b>Role:</b> ${response.data.role}<br>
                <b>Height:</b> ${response.data.height} cm<br>
                <b>Weight:</b> ${response.data.weight} kg<br>
                <b>Daily Calorie Goal:</b> ${response.data.daily_caloriegoal} calories
              </div>
            `,
            showCancelButton: true,
          });
          break;
        case "getRecipeDetails":
          Swal.fire({
            title: `<strong>Recipe Details: ID(${response.data.id})</strong>`,
            icon: "info",
            html: `
                <div style="text-align: left;">
                  <b>Name:</b> ${response.data.name}<br>
                  <b>Calories:</b> ${response.data.calories}<br>
                  <b>Type:</b> ${response.data.type}<br>
                  <b>Visibility:</b> ${
                    response.data.visibility == 1 ? "Visible" : "Not Visible"
                  }<br>
                  <b>Creation Date:</b> ${response.data.creation_date}<br>
                  <b>Creator:</b> ${response.data.creator}<br>
                  <img src="${
                    response.data.image_url
                  }" alt="Recipe Image" style="max-width: 100%; margin-top: 10px;">
                </div>
              `,
            showCancelButton: true,
          });
          break;
        case "loadRecipeDetails":
          // Remplir les champs du modal avec les données reçues
          $("#edit_id").val(response.data.id);
          $("#edit_name").val(response.data.name);
          $("#edit_calories").val(response.data.calories);
          $("#edit_type").val(response.data.type);

          // Gérer l'image de la recette
          if (response.data.image_url) {
            $("#edit_imageUpload")
              .next(".custom-file-label")
              .html(response.data.image_url.split("/").pop());
          } else {
            $("#edit_imageUpload")
              .next(".custom-file-label")
              .html("Choose file...");
          }

          // Afficher le modal d'édition de la recette
          $("#editRecipeModal").modal("show");
          break;

        case "insertPlan":
          console.log(response.message);
          handleAjaxResponse(
            action,
            response,
            "Plan Added successfully",
            "",
            false
          );
          break;
        case "UserHavePlan":
          console.log(response.message);
          if (response.message === "PlanExist") {
            localStorage.setItem("recipes", JSON.stringify(response.data));
            lienActuel = window.location.href;
            if (
              lienActuel ==
              "https://localhost/archiweb_2024_projets_gr06/planning"
            ) {
              window.location.href =
                "https://localhost/archiweb_2024_projets_gr06/planning?period=" +
                response.planInfo["period"] +
                "&duration=" +
                response.planInfo["total_length"];
            }
            $("#userHavePlan").show();
            $("#userNotHavePlan").hide();
            $("#planNameId").html(response.planInfo["name"]);
            $("#periodValue").html(response.planInfo["period"]);
            $("#durationValue").html(response.planInfo["total_length"]);
          } else if (response.message === "noPlanExist") {
            $("#userHavePlan").hide();
            $("#userNotHavePlan").show();
          }
          break;
        default:
          console.log("Unhandled action: " + action);
          handleAjaxResponse(action, response, successTitle, successMessage);
          break;
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      handleAjaxError(jqXHR, textStatus, errorThrown);
    },
  });
}

function performAjaxRequestWithImg(
  requestType,
  action,
  additionalData,
  successTitle,
  successMessage
) {
  // creation FormData() object
  var formData = new FormData();
  var fileInput = document.getElementById("image_url");
  var name = document.getElementById("name");
  var calories = document.getElementById("calories");

  if (fileInput.files.length > 0) {
    formData.append("name", name.value);
    formData.append("calories", calories.value);
    formData.append("action", action);
    formData.append("file", fileInput.files[0]);
    formData.append("additionalData", additionalData);
  }
  $.ajax({
    url: "index.php",
    type: requestType,
    data: formData,
    processData: false,
    contentType: false,
    dataType: "JSON",
    success: function (response) {
      console.log("action:  " + action);
      handleAjaxResponse(
        action,
        response,
        successTitle,
        successMessage,
        action
      );
    },
    error: function (jqXHR, textStatus, errorThrown) {
      handleAjaxError(jqXHR, textStatus, errorThrown);
    },
  });
}

// DEBOUNCE (for search bars mainly, it only runs functions when a value is no longer being changed after X time)
function debounce(func, wait) {
  var timeout;

  return function () {
    var context = this,
      args = arguments;
    var later = function () {
      timeout = null;
      func.apply(context, args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

function performAjaxWithImage(formId, action, successTitle, successMessage) {
  var formData = new FormData(document.getElementById(formId));
  console.log(formId);
  formData.append("action", action); // Ensure your backend handles this action appropriately

  $.ajax({
    url: "index.php", // or the specific endpoint for user registration
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    dataType: "json",
    success: function (response) {
      handleAjaxResponse(action, response, successTitle, successMessage, false);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      handleAjaxError(jqXHR, textStatus, errorThrown);
    },
  });
}
