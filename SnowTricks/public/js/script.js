$( document ).ready(function() {
    $('.trash-trick').click(function(){
        alert('Le trick va être supprimer ?')
    })
    $('.delete-trick').click(function(){
        alert('Le trick va être supprimer ?')
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

    $('#user_password').val('');

    setTimeout(function() { 
        $('.alert').css('display','none');
    }, 3500);

    if($('#security_email').hasClass('is-invalid')){
        alert('Votre adresse email existe déjà!')
    }

    $('.btn-add-video').click(function()
    {
        $('.modal').css('display','block');
        $('.show-add-image').css('display','none');
    });

    $('.btn-add-image').click(function()
    {
        $('.modal').css('display','block');
        $('.show-add-video').css('display','none');
    });
    $('.close').click(function()
    {
        $('.modal').css('display','none');
        $('.show-add-image').css('display','block');
        $('.show-add-video').css('display','block');
    })
    $('textarea#trick_description').trumbowyg();

    $('input#trick_name').keypress(function(){
        if($(this).val().length > 30){
            alert('Le titre ne doit pas dépasser 30 caractères')
        }
    });

    $('textarea#comment_text').trumbowyg();
});
