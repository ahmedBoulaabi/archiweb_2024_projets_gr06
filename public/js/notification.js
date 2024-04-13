$(document).ready(function () {

    var id_clicked = -1
    // on doit attacher l'évènement au parent, car les enfants ne sont pas encore créés
    $('#client-list-results').on('click', '.client-user', function () {
        const userId = $(this).data('user-id');
        performAjaxRequest(
            "POST",
            "sendNotification",
            "&receiverId=" + userId,
            "",
            ""
        );
    });

    // en cliquant sur Discussion dans le dashboard, ouvre le modal,
    // et y charge les messages avec le nutritioniste
    $('.messages').on('click', '.message-box', function (event) {
        const conversationId = $(this).data('id');
        id_clicked = conversationId
        var modal = document.querySelector('#open-modal-message');
        if (modal) {
            $(modal).modal('show');

            performAjaxRequest(
                "GET",
                "getMessagesFromAConvo",
                "&receiverId=" + conversationId,
                "",
                ""
            );
        } else {
            console.error("Le modal n'a pas été trouvé.");
        }
    });

    // envoit le message depuis le modal, et le met en plus directement dans l'html
    $("#message-form").on("submit", function (event) {
        event.preventDefault();
        var message = $("#message").val();
        var additionalData = "&content=" + message + "&targetID=" + id_clicked
        performAjaxRequest("POST", "sendMessage", additionalData, "", "");
    });




    // pour récupérer le nombre de notif, et les mettre en session
    function getNotif() {
        performAjaxRequest(
            "GET",
            "countNotification",
            "",
            "",
            ""
        );
    }

    // pour récupérer les users ayant envoyé des notifications 
    function getUserFromNotif() {
        performAjaxRequest(
            "GET",
            "getUsersFromNotifications",
            "",
            "",
            ""
        );
    }

    getNotif();
    getUserFromNotif();

    // pour effectuer une recherche
    function performSearch() {
        var inputValue = $('#client-list-search').val();
        performAjaxRequest(
            "GET",
            "clientSearch",
            "&searchValue=" + inputValue,
            function (data) {
                $("#client-list-results").html(data);
            },
            ""
        );
    }

    var debouncedSearch = debounce(performSearch, 700);

    $('#client-list-search').on('input', function () {
        debouncedSearch();
    });
});