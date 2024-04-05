$(document).ready(function () {

    var id_clicked = -1
    // on doit attacher l'évènement au parent, car les enfants ne sont pas encore créés
    $('#client-list-results').on('click', '.client-user', function () {
        const userId = $(this).data('user-id');
        console.log(userId + " clicked");
        performAjaxRequest(
            "POST",
            "sendNotification",
            "&receiverId=" + userId,
            "",
            ""
        );
    });

    $('.messages').on('click', '.message-box', function (event) {
        const conversationId = $(this).data('id');
        id_clicked = conversationId
        console.log(conversationId + " clicked");
        console.log("dans modal ouverture");
        var modal = document.querySelector('#open-modal-message');
        if (modal) {
            console.log("modal existe")
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

    $("#message-form").on("submit", function (event) {
        event.preventDefault();
        var message = $("#message").val();
        console.log(message);
        var additionalData = "&content=" + message + "&targetID=" + id_clicked
        performAjaxRequest("POST", "sendMessage", additionalData, "", "");
    });

    /*
        $('#discussion-class').click(function (e) {
            e.preventDefault();
            const conversationId = $(this).data('id');
            id_clicked = conversationId
            console.log(conversationId + " clicked");
            console.log("dans modal ouverture");
            var modal = document.querySelector('#open-modal-message');
            if (modal) {
                console.log("modal existe")
                $(modal).modal('show');
            } else {
                console.error("Le modal n'a pas été trouvé.");
            }
        });
        */

    function getDiscussion() {
        performAjaxRequest(
            "GET",
            "getDiscussion",
            "",
            "",
            ""
        );
    }


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
    getDiscussion();

    // pour effectuer une recherche
    function performSearch() {
        var inputValue = $('#client-list-search').val();
        performAjaxRequest(
            "GET",
            "clientSearch",
            "&searchValue=" + inputValue,
            function (data) {
                console.log(data);
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