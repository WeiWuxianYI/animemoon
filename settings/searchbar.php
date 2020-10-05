<div id="searchBar">
    <div class="ovr"></div>
    <div class="searchBarG">
        <div class="sbg-icn"><i class="fas fa-search"></i></div>
        <input id="searchBarInput" type="text" placeholder="Search any anime...">
    </div>
    <div class="del"><div class="del-bg"></div></div>
    <div id="searchRes">
    </div>
</div>
<script>
    $(function ($) {
        let index = -1;
        let maxIndex = 0;
        let indexes = [];
        let searchBar = $('#searchBar');
        let searchRes = $('#searchRes');
        let searchBarGrid = $(searchBar.find('.searchBarG')[0]);
        let searchInput = $('#searchBarInput');
        searchBar.find('.ovr').on('click', function () {
            searchBar.removeClass('act');
            searchInput.val('');
            searchRes.css('height', 0);
            searchBarGrid.removeClass('searched');
            searchRes.removeClass('searched');
            index = -1;
            maxIndex = 0;
        });
        $('#searchButton').on('click', function () {
            searchBar.addClass('act');
            searchInput.focus();
        });
        let inSearch = false;
        searchInput.on('keyup', function (e) {
            let key = e.key;
            if (key === 'ArrowDown' || key === 'ArrowUp')
                return;
            let item = $(this);
            let txt = (item.val()).trim();
            if (txt === '') {
                searchRes.css('height', 0);
                searchBarGrid.removeClass('searched');
                searchRes.removeClass('searched');
            } else {
                if (!inSearch) {
                    inSearch = true;
                    let height = 0;
                    searchRes.find('.searchResult').remove();
                    $.post("<?=$site_link.'/searchapi.php'?>", {
                        search: txt
                    }, function (data) {
                        let jsData = $.parseJSON(data);
                        jsData.forEach(function (e) {
                            let trans = e['translation'];
                            searchRes.append('<a href="'+ e['url'] +'" class="searchResult"><img src="' + e['icon'] + '"/>' + e['name'] + e['translation'] + '<div class="subTitle">' + e['subtitle'] + '</div></a>');
                            height += 68;
                        });
                        inSearch = false;
                        if (height > 300)
                            height = 300;
                        searchRes.css('height', height);
                        if (height > 0) {
                            searchBarGrid.addClass('searched');
                            searchRes.addClass('searched');
                            maxIndex = searchRes.find('.searchResult').length;
                            indexes = $(searchRes.find('.searchResult'));
                        } else {
                            searchBarGrid.removeClass('searched');
                            searchRes.removeClass('searched');
                        }
                    });
                }
            }
        });
        $(document).on('keyup', function (e) {
            let key = e.key;
            if (!(key === 'ArrowDown' || key === 'ArrowUp'))
                return;
            if (maxIndex <= 0) {
                index = -1;
                return;
            }
            if (key === 'ArrowDown')
                index++;
            else
                index--;
            if (index < 0)
                index = 0;
            if (index > maxIndex - 1)
                index = maxIndex - 1;
            let oItem = $(indexes[index - 1]);
            let nItem = $(indexes[index + 1]);
            let currentItem = $(indexes[index]);
            if (index - 1 >= 0 && index - 1 <= maxIndex - 1) {
                oItem.css({
                    'background': '',
                    'color': ''
                });
                oItem.find('.subTitle').css('color', '');
            }
            if (index + 1 >= 0 && index + 1 <= maxIndex - 1) {
                nItem.css({
                    'background': '',
                    'color': ''
                });
                nItem.find('.subTitle').css('color', '');
            }
            if (index >= 0 && index <= maxIndex - 1) {
                currentItem.css({
                    'background': '#03A9F4',
                    'color': 'white'
                });
                currentItem.find('.subTitle').css('color', 'white');
            }
        });
    });
</script>