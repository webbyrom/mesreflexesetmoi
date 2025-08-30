/*****
 * menu sticky
 * scroll to top
 * Menu responsive
 */

(function ($) {
    document.addEventListener('DOMContentLoaded', function () {
        $(document).scroll(function () {
            if ($(document).scrollTop() > 0) {
                $("#Mrm_nav_menu").addClass("sticky");
            } else {
                $("#Mrm_nav_menu").removeClass("sticky");
            }
        });
    });
})(jQuery);

/// Bouton retour vers le haut

// affichage du bouton retour vers le haut au défilement de la page
window.addEventListener('scroll', function(){
    var button = this.document.getElementById('Mrm_scroll_top_button');
    if (this.window.scrollY > 100){
        button.style.display = 'block';
    } else {
        button.style.display= 'none';
    }
});
// défiler la page vers le haut au click
document.querySelector('.Mrm-scroll-totop').addEventListener('click', function(e){
    e.preventDefault();
    window.scrollTo({
        top:0,
        behavior: 'smooth'
    });
});

// Menu burger animation 
// menu burger animation
(function($) {
    $(document).ready(function() {
        $('.Mrm-menu-icon').click(function(e) {
            e.preventDefault();
            var $this = $(this);

            // Toggle de la classe 'Mrm-opened' sur l'élément .Mrm-menu-icon
            $this.toggleClass('Mrm-opened Mrm-closed');

            // Toggle de la classe 'Mrm-left-menu' sur l'élément #Mrm_collapse
            $('#Mrm_collapse').toggleClass('Mrm-left-menu');

            // Appel de la fonction toggleMenu()
            toggleMenu();
        });
    });

    function toggleMenu() {
        var sidebar = $('.Mrm-collapse');
        var content = $('body');

        if (sidebar.hasClass('Mrm-left-menu')) {
            sidebar.css('left', '0');
            content.css('margin-left', '15.625rem');
            content.addClass('no-scroll');
        } else {
            sidebar.css('left', '-15.625rem');
            content.css('margin-left', '0');
            content.removeClass('no-scroll');
        }
    }
})(jQuery);
