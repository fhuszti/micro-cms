$(function() {
    //Report a comment
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
                        flagButton.replaceWith("<button type='button' class='btn btn-warning btn-sm disabled' title='Commentaire déjà signalé'>Déjà signalé</button>");
                    });
                },
                error: function(xhr, status, error) {
                    var err = xhr.responseText;
                    alert('erreur');
                }
            });
        });
    }


    //Respond to a comment
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


    //Delete a comment
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
                success: function(urlFromController) {
                    // we toggle the modal back to hidden
                    //we replace DOM stuff inside a callback because bug on modal backdrop otherwise
                    modal.modal('hide').on('hidden.bs.modal', function() {
                        // we change the content to the "deleted" version
                        content.replaceWith('<div class="row" id="commentContent-'+commentId+'"><p>[Commentaire supprimé par son auteur]</p></div>');

                        // we change the footer to the "deleted" version
                        footer.replaceWith('<footer class="row" id="commentFooter-'+commentId+'"><hr class="marginTopless marginBottomless"></footer>');
                    });
                },
                error: function() {
                    $('<div class="alert alert-danger col-xs-12">Une erreur s\'est produite lors de la suppression du commentaire.<br />Veuillez réessayer plus tard.</div>').insertBefore(content);
                }
            });
        });
    }


    //Edit a comment
    function editing() {
        //Attach click event to edit buttons
        var editButtons = $('.editButton');

        editButtons.on('click', function() {
            var commentId = $(this).attr('id').split('-')[1],
                form = $('form[name="editForm-'+commentId+'"]'),
                formDiv = form.parent(),
                content = $('#commentContent-'+commentId);

            //We toggle the form
            if (formDiv.css('display') == 'none') {
                formDiv.slideDown();
                content.hide();
            }
            else {
                formDiv.hide();
                content.slideDown();
            }
        });

        //Attach click event to submit buttons to use AJAX
        var confirmEditButtons = $('.confirmEditButton');

        confirmEditButtons.on('click', function(e) {
            e.preventDefault();

            var commentId = $(this).attr('id').split('-')[1],
                form = $('form[name="editForm-'+commentId+'"]'),
                formDiv = form.parent(),
                oldContent = $('#commentContent-'+commentId),
                commentContent = $('form[name="editForm-'+commentId+'"] textarea').val();

            if (commentContent) {
                $.ajax({
                    type: 'POST',
                    url: "commentaire/modifier",
                    timeout: 3000,
                    data: {'id': commentId, 'content': commentContent},
                    success: function(urlFromController) {
                        oldContent.replaceWith('<div class="row" id="commentContent-'+commentId+'"><p>'+commentContent+'</p></div>');

                        formDiv.hide();
                        oldContent.slideDown();
                    },
                    error: function() {
                        $('<div class="alert alert-danger col-xs-12">Une erreur s\'est produite lors de l\'envoi du commentaire.<br />Veuillez réessayer plus tard.</div>').insertBefore(oldContent);
                    }
                });
            }
            else {
                $('<div class="alert alert-danger col-xs-12">Votre commentaire ne peut être vide.</div>').insertBefore(oldContent);
            }
        });
    }


    flagging();
    responding();
    deleting();
    editing();
});
