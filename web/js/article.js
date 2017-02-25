$(function() {
    var confirmFlagButton = $('.confirmFlagButton');

    confirmFlagButton.on('click', function(e) {
            e.preventDefault();

            var parentId = $(this).attr('id'),
                modal = $('#flagDialog'+parentId);

            $.ajax({
                type: 'POST',
                url: "commentaire/flag",
                timeout: 3000,
                data: {'id': parentId},
                success: function() {
                    // we toggle the modal back to hidden
                    modal.modal('toggle');

                    // we change the button to the "flagged" version
                    $(this).text('Déjà signalé');
                },
                error: function(xhr, status, error) {
                    var err = xhr.responseText;
                    alert('erreur');
                }
            });
        });
});
