let groupsOfEpisodes = [];
function setGroup(newGroup) { groupsOfEpisodes = newGroup; }
function changePagination(pageId)
{
    $(groupsOfEpisodes).each(function (e, val)
    {
        let page = $('#'+val);
        let btn = $('#btn-'+val);
        if(pageId !== val) { page.css({ 'display': 'none', 'opacity': 0 }); btn.removeClass('active'); }
        else { page.css({'display': 'flex' }).animate({ 'opacity': 1 }); btn.addClass('active'); }
    });
}