$(function ($)
{
    $('#account-buttons li').each(function ()
    {
        if(typeof $(this).attr('openPage') === typeof undefined || $(this).attr('openPage') === false) return;
        $(this).on('click', function ()
        {
            let buttonName = $(this).attr('openPage');
            $('#account-sections .account-body-content').each(function ()
            {
                if($(this).attr('id') === buttonName) $(this).css({ 'display': 'flex' });
                else $(this).css({ 'display': 'none' });
            });
            $('#account-buttons li').each(function ()
            {
                if($(this).attr('openPage') === buttonName) { if (!$(this).hasClass('ac-active')) $(this).addClass('ac-active'); }
                else { $(this).removeClass('ac-active'); }
            });
        });
    });
});