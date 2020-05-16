$( document ).ready(function() {
    $('.trash-trick').click(function(){
        if(confirm('Vous êtes sûr de vouloir supprimer ce Trick ?')) {
            location.reload();
        }
    })
    $(".down").click(function() {
         $('html, body').animate({
             scrollTop: $(".up").offset().top
         }, 1500);
     });
    
    $(".up").click(function() {
        $('html, body').animate({
            scrollTop: $(".down").offset().top
        }, 1000);
    });

    $('.icon-add-avatar').click(function()
    {
        $('.custom-file-input').click();
    });

});