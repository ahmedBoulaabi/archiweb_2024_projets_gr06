function getMessageBoxHtml(conversation) {
  return `
    <div class="message-box" data-id=${conversation.interlocutorID} style="cursor:pointer;">
      <img src=${conversation.img} alt="profile image">
      <div class="message-content">
        <div class="message-header">
          <div class="name">${conversation.fullname}</div>

        </div>
        <p class="message-line">
          ${conversation.lastMessage.contenu}
        </p>
        <span class="message-line time">
        ${conversation.lastMessage.etat == 0 ? conversation.lastMessage.date_envoi : ""}
        </span>
      </div>
    </div>
  `;
}

function displayNoNutritionist(idNutri = null) {
  if (idNutri == null) {
    $("#discussion-class").html(`
    <div class="no-nutritionist-wrapper" style="cursor:pointer;">
      <div class="no-nutritionist-content">
        <div class="no-nutritionist-header">
          <h3 class="no-nutritionist-title" style="color:white;">No Nutritionist</h3>
        </div>
      </div>
    </div>
  `);
  } else {
    $("#discussion-class").html(`    <div class="message-box" data-id=${idNutri} style="cursor:pointer;">
    <div class="message-content">
      <div class="message-header">
        <div class="name">No conversation yet</div>

      </div>
      <p class="message-line">
        Start a conversation
      </p>
    </div>
  </div>`);

  }


  // Ajouter les styles CSS pour centrer le message
  $(".no-nutritionist-wrapper").css({
    "display": "flex",
    "justify-content": "center",
    "align-items": "center",
    "height": "100%"
  });

  $(".no-nutritionist-content").css({
    "text-align": "center"
  });
}

function formatMessage(message, ownID) {
  //console.log(message);
  var ownMsg = ownID == message.expediteur_id

  return `
  <div class="d-flex flex-column ${ownMsg ? 'align-items-end' : 'align-items-start'} mb-4">
    <div class="d-flex ${ownMsg ? 'justify-content-end' : 'justify-content-start'}">
      <div class="msg_cotainer" ${ownMsg ? 'style="display:inline;background-color:#4287f5;"' : ''}>
        ${message.contenu}
      </div>
    </div>
    <div class="d-flex ${ownMsg ? 'justify-content-end' : 'justify-content-start'}">
      <span class="msg_time${ownMsg ? '_send' : ''}">
        ${message.date_envoi}
      </span>
    </div>
  </div>
`;
}
function displayConversations(response) {
  var data = response.data;
  var ownID = response.ownID
  var role = response.role
  var discussions = {};
  var interlocutorFullname;

  data.forEach(function (message) {
    var interlocutorID;

    // Déterminer l'identifiant de l'interlocuteur
    if (message.expediteur_id == ownID) {
      interlocutorID = message.destinataire_id;
    } else {
      interlocutorID = message.expediteur_id;
    }
    interlocutorFullname = message.interlocutor_fullname;
    profilePicture = message.interlocutor_img;
    goal = message.interlocutor_goal;


    // Vérifier si la discussion existe déjà dans l'objet
    if (!discussions[interlocutorID]) {
      discussions[interlocutorID] = {
        messages: [],
        lastMessage: null,
        fullname: interlocutorFullname,
        img: profilePicture,
        interlocutorID: interlocutorID,
        goal: goal
      };
    }

    discussions[interlocutorID].messages.push(message);

    // indique quel est le dernier message (il sera affiché)
    if (!discussions[interlocutorID].lastMessage || message.date_envoi > discussions[interlocutorID].lastMessage.date_envoi) {
      discussions[interlocutorID].lastMessage = message;
    }
  });

  // tri pour que les messages apparaissent bien
  Object.values(discussions).forEach(function (discussion) {
    discussion.messages.sort(function (a, b) {
      return new Date(a.date_envoi) - new Date(b.date_envoi);
    });
  });

  console.log(discussions);
  var listeConvHTML = "";

  Object.values(discussions).forEach(discussion => {
    listeConvHTML += getMessageBoxHtml(discussion)
  });
  $(".messages").html(listeConvHTML)
  $("#discussion-class").html(listeConvHTML)
}


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
        action != "deleteClient" &&
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
    if (!logout && action != "deleteUser" && action != "deleteClient") {
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

      switch (action) {
        case "showAllRecipes":
          $("#RecipeList").html(response.message);
          break;
        case "getNutriClients":
          $("#showClients").html(response.message);
          break;
        case "getNutriRequests":
          $("#showNutriRequests").html(response.message);
          $("table").DataTable({ order: [0, "desc"] });
          break;

        case "getUserProgress":
          console.log("res " + response.data);
          $("#total-clients").text(response.data.total_users);
          $("#in-progress").text(response.data.not_completed);
          $("#plans-finished").text(response.data.completed);
          $("#project-boxes").empty();
          var baseAppDir = document
            .getElementById("baseAppDir")
            .innerText.trim();
          const backgroundColors = {
            'gain-weight-normal': '#c8f7dc',
            'lose-weight-fast': '#ffd3e2',
            'lose-weight-normal': '#e9e7fd'
          };

          const spanColors = {
            'gain-weight-normal': '#34c471',
            'lose-weight-fast': '#df3670',
            'lose-weight-normal': '#4f3ff0'
          };
          response.data.users_progress.sort(function (a, b) {
            var progressA = parseFloat(a.plan_progress.replace('%', ''));
            var progressB = parseFloat(b.plan_progress.replace('%', ''));
            return progressB - progressA;
          });

          response.data.users_progress.forEach(function (client) {
            if (client.plan_creation_date === null || client.plan_creation_date === undefined) {
              return; // Skip this client and move on to the next one
            }


            const goal = client.goal; // Assuming row.goal contains the goal information
            const backgroundColor = backgroundColors[goal] || '#ffffff';
            const spanColor = spanColors[goal] || '#ffffff';

            var clientHtml = `
              <div class="project-box-wrapper">
                <div class="project-box" style="background-color: ${backgroundColor};">
                  <div class="project-box-header">
                    <span>${client.plan_creation_date
              }</span> <!-- Assuming 'date' is part of your client object -->
                    <div class="more-wrapper">
                      <!-- Button and SVG omitted for brevity -->
                    </div>
                  </div>
                  <div class="project-box-content-header">
                  <img src="${client.img
                ? baseAppDir + client.img
                : "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=2550&q=80"
              }" alt="profile image" class="img">

                    <p class="box-content-header">${client.fullname}</p>
                    <p class="box-content-subheader">${client.goal}</p>
                  </div>
                  <div class="box-progress-wrapper">
                    <p class="box-progress-header">Progress</p>
                    <div class="box-progress-bar">
                      <span class="box-progress" style="width: ${client.plan_progress
              }; background-color: ${spanColor}"></span>
                    </div>
                    <p class="box-progress-percentage">${client.plan_progress
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

          Swal.fire({
            title: "Notification sent!",
            text: response.message,
            icon: "success",
          });
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
          Swal.fire({
            title: "Notification " + statusText,
            text: response.message,
            icon: "success",
          });
          break;

        case "getUserDetails":
          Swal.fire({

            title: `<strong>User Info: ID(${response.data.id})</strong>`,
            icon: "info",
            html: `
  < div style = "text-align: left;" >
    <b>Full Name:</b> ${response.data.fullname} <br>
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
                  <b>Visibility:</b> ${response.data.visibility == 1 ? "Visible" : "Not Visible"
              }<br>
                  <b>Creation Date:</b> ${response.data.creation_date}<br>
                  <b>Creator:</b> ${response.data.creator}<br>
                  <img src="${response.data.image_url
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

          if (response.message === "PlanExist") {
            localStorage.setItem("recipes", JSON.stringify(response.data));
            localStorage.setItem("planInfo", JSON.stringify(response.planInfo));
            // console.log(response.data);
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

            let creationDate = new Date(response.planInfo["creation_date"]);
            let currentDate = new Date();
            let differenceInTime =
              currentDate.getTime() - creationDate.getTime();
            let differenceInDays = differenceInTime / (1000 * 3600 * 24);
            let totalLength = parseInt(response.planInfo["total_length"], 10);
            let daysLeft = Math.ceil(totalLength - differenceInDays);
            let progressPercentage = (differenceInDays / totalLength) * 100;
            progressPercentage = Math.min(100, Math.max(0, progressPercentage));

            $("#userHavePlan").show();
            $("#userNotHavePlan").hide();
            $("#planNameId").html(response.planInfo["name"]);
            $("#periodValue").html(response.planInfo["period"]);
            $("#durationValue").html(response.planInfo["total_length"]);
            $("#days-left").html(daysLeft + " Days Left");
            $("#progress-val").html(progressPercentage.toFixed(2) + "%");
            $(".box-progress").css(
              "width",
              progressPercentage.toFixed(2) + "%"
            );
            console.log(response.planInfo);
          } else if (response.message === "noPlanExist") {
            $("#userHavePlan").hide();
            $("#userNotHavePlan").show();
          }
          break;
        case "toggleRecipeConsumed":
          var success = response.success;
          if (success) {
            Swal.fire({
              title: "Toggle Consume",
              text: "You have toggled consume on this recipe!",
              icon: "success",
            }).then((result) => {
              if (result.isConfirmed) {
                // window.location.reload(true);
                performAjaxRequest("POST", "UserHavePlan", "", "", "");
              }
            });
          }
          break;
        case "getDiscussion":
          console.log(response)
          if (response.success) {
            if (response.role == "NoNutritionist") {
              displayNoNutritionist(null) // cas où il n'y a pas de nutritioniste
            } else {
              displayConversations(response)
            }
          } else {
            if (response.data == "empty") {
              if (response.role == "Regular") {
                displayNoNutritionist(response.nutriID) // cas où il n'y a pas encore de message 
              } else {
                displayNoNutritionist(response.clientID) // cas où il n'y a pas encore de message 

              }
            }
            console.log("La requête n'a pas réussie.");
            console.log(response.data)
          }
          break;

        case "getMessagesFromAConvo":
          wholeDiscussion = ""
          console.log(response.success)
          console.log("voilà la data " + response.data)
          if (response.success) {
            for (numMessage in response.data) { // ajout des différents messages dans la div de conversation
              if (response.data[numMessage].etat != 1) { // ignore le message par défaut
                wholeDiscussion = formatMessage(response.data[numMessage], response.ownID) + wholeDiscussion
              }
            }
            $('#conversationMessages').html(wholeDiscussion)
          }


          console.log(response)
          // mettre le nom de l'interlocuteur pour savoir à qui on parle
          const modalTitle = document.querySelector('.modal-title');
          if (modalTitle && response.data[0].interlocutor_fullname) {
            console.log("nom interlocutor: " + response.data[0].interlocutor_fullname);
            modalTitle.innerHTML = "Send a message to " + response.data[0].interlocutor_fullname;
          }
          break;

        case 'sendMessage':
          var ownID = response.ownID
          var message = {
            'date_envoi': "Just now",
            'expediteur_id': ownID,
            'contenu': response.data
          }
          newMessage = formatMessage(message, ownID)
          $('#conversationMessages').prepend(newMessage)
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
