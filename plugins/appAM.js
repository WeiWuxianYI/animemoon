let sidebarToggle = false;
function toggleSidebar(id)
{
    let sidebar = $('#'+id);
    if(sidebarToggle) sidebar.removeClass('active'); else sidebar.addClass('active');
    sidebarToggle = !sidebarToggle;
}
function openWindow(url, newTab)
{
    if(newTab === 2) (window.open(url, '_blank')).focus();
    else window.location.href = url;
}
function initializeSidebar(from, to)
{
    let desktopSidebar = $('#'+from);
    let mobileSidebar = $('#'+to);
    let mobileUl = $(desktopSidebar.clone().appendTo(mobileSidebar.find('.mobile-sidebar-content')[0]));
    $('#'+from+' ~ .login-nav li').each(function () { let element = $(this).clone(); $(element.find('span')).each(function () { $(this).remove(); }); mobileUl.append(element); });
    mobileUl.removeClass().addClass('mobile-sidebar-list').removeAttr('id');
    mobileUl.find('a').each(function ()
    {
        switch ($(this).text())
        {
            case 'Home': $(this).prepend('<i class="fas fa-home side-icon"></i>'); break;
            case 'Anime': $(this).prepend('<i class="fas fa-film side-icon"></i>'); break;
            case 'Account': $(this).prepend('<i class="fas fa-user-alt side-icon"></i>'); break;
            case 'Search': $(this).prepend('<i class="fas fa-search side-icon"></i>'); break;
            case 'Login': $(this).prepend('<i class="fas fa-user-alt side-icon"></i>'); break;
            case 'Register': $(this).prepend('<i class="fas fa-user-plus side-icon"></i>'); break;
        }
    });
    mobileUl.find('form').each(function () { $(this).parent().remove(); });
}

$(function ($)
{
    $('#searchBar').css('display', 'flex');
    $('.movie-box-new').each(function ()
    {
        let movieBox = $(this);
        movieBox.find('.movie-name-new').each(function ()
        {
            let movieName = $(this);
            let spanText = $(movieName.find('span')[0]);
            spanText.css('right', 'auto');
            let width = parseInt(spanText.outerWidth() + 1);
            spanText.css('right', '0');
            if(width <= 201) return true;
            movieName.on('mouseenter', function () {
                let text = $($(this).find('span')[0]);
                text.css({ 'margin-left': '-' + (width - 199    ) + 'px' });
            });
            movieName.on('mouseleave', function () { let text = $($(this).find('span')[0]); text.css({ 'margin-left': 0 }); });
        });
    });

});