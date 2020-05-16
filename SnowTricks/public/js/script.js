$( document ).ready(function() {
    $('.trash-trick').click(function(){
        if(confirm('Vous êtes sûr de vouloir supprimer ce Trick ?')) {
            location.reload();
        }
    })
});