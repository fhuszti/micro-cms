$(function() {
    function flagging() {
        var confirmFlagButtons = $('.confirmFlagButton');

        confirmFlagButtons.on('click', function(e) {
            e.preventDefault();

            var parentId = $(this).attr('id').split('-')[1],
                modal = $('#flagDialog'+parentId),
                flagButton = $('#flagButton-'+parentId);

            $.ajax({
                type: 'POST',
                url: "commentaire/flag",
                timeout: 3000,
                data: {'id': parentId},
                success: function() {
                    // we toggle the modal back to hidden
                    modal.modal('hide').on('hidden.bs.modal', function() {
                        // we change the button to the "flagged" version
                        flagButton.replaceWith("<button type='button' class='btn btn-warning disabled' title='Commentaire déjà signalé'>Déjà signalé</button>");
                    });
                },
                error: function(xhr, status, error) {
                    var err = xhr.responseText;
                    alert('erreur');
                }
            });
        });
    }


    function responding() {
        var respondButtons = $('.respondButton');

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


    function deleting() {
        var deleteButtons = $('.confirmDeleteButton');

        deleteButtons.on('click', function(e) {
            e.preventDefault();

            var commentId = $(this).attr('id').split('-')[1],
                modal = $('#deleteDialog'+commentId),
                content = $('#commentContent-'+commentId),
                footer = $('#commentFooter-'+commentId);

            $.ajax({
                type: 'POST',
                url: "commentaire/supprimer",
                timeout: 3000,
                data: {'id': commentId},
                success: function() {
                    // we toggle the modal back to hidden
                    //we replace DOM stuff inside a callback because bug on modal backdrop otherwise
                    modal.modal('hide').on('hidden.bs.modal', function() {
                        // we change the content to the "deleted" version
                        content.replaceWith('<div class="row" id="commentContent-'+commentId+'"><p>[Commentaire supprimé par son auteur]</p></div>');

                        // we change the footer to the "deleted" version
                        footer.replaceWith('<footer class="row" id="commentFooter-'+commentId+'"><hr class="marginTopless marginBottomless"></footer>');
                    });
                },
                error: function(xhr, status, error) {
                    var err = xhr.responseText;
                    alert('erreur');
                }
            });
        });
    }


    flagging();
    responding();
    deleting();
});
