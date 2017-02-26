$(function() {
    function flagging() {
        var confirmFlagButtons = $('.confirmFlagButton');

        confirmFlagButtons.on('click', function(e) {
            e.preventDefault();

            var parentId = $(this).attr('id'),
                modal = $('#flagDialog'+parentId),
                flagButton = $('#flagButton'+parentId);

            $.ajax({
                type: 'POST',
                url: "commentaire/flag",
                timeout: 3000,
                data: {'id': parentId},
                success: function() {
                    // we toggle the modal back to hidden
                    modal.modal('toggle');

                    // we change the button to the "flagged" version
                    flagButton.replaceWith("<button type='button' class='btn btn-warning disabled' title='Commentaire déjà signalé'>Déjà signalé</button>" );
                },
                error: function(xhr, status, error) {
                    var err = xhr.responseText;
                    alert('erreur');
                }
            });
        });
    }


    function responding() {
        var respondButtons = $('.respondButtons');

        respondButtons.on('click', function() {
            var parentId = $(this).attr('id').split('-')[1],
                form = $('form[name="commentForm-'+parentId+'"]'),
                formDiv = form.parent();

            if (formDiv.css('display') == 'none')
                formDiv.slideDown();
            else
                formDiv.slideUp();
        });
    }


    flagging();
    responding();
});
