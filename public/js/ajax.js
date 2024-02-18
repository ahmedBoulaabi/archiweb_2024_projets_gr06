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
      if (redirectHref != "update" && redirectHref != "recipes-list") {
        window.location.href = redirectHref;
      } else if (redirectHref == "recipes-list") {
        window.parent.rafraichirPage();
      } else {
        window.location.reload(true);
      }
    });
    if (!logout) {
      $("#form-data")[0].reset();
    }
  } else {
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
      console.log("action: 1111  " + action);

      if (action == "showAllRecipes") {
        $("#RecipeList").html(response.message);
      } else if (action == "showAllUsers") {
        $("#showUser").html(response.message);
        $("table").DataTable({ order: [0, "desc"] });
      } else if (action == "countRegularUsers") {
        $("#usersNumber").html(response.count);
      } else if (action == "countNutritionistUsers") {
        $("#nutritionistNumber").html(response.count);
      } else if (action == "planSearchForRecipe") {
        var data = response.data;
        console.log(data);
        $("#plan-recipe-results").html(data);
      } else {
        console.log("action: " + action);
        handleAjaxResponse(
          action,
          response,
          successTitle,
          successMessage,
          action == "logout"
        );
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
      console.log("action: 1111  " + action);
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
