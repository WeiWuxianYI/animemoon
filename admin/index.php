<?php
$TITLE = 'KITSU';
require_once 'private/config.php';
?>
<html>
<head>
    <?php require_once 'private/meta.php'; ?>
</head>
<body>
<?php require_once 'private/sidebar.php'?>
<input type="text" name="search" id="search" placeholder="Search...">
<div class="data-rows" id="rows">
</div>
<div id="apiModal" class="modal">
    <div onclick="closeModal('#apiModal')" class="modal-pointer"></div>
    <div class="modal-content">
    </div>
</div>
<script>
    let modalData = [];
    $(function ($) {
        let rows = $('#rows');
        let inWork = false;
        $('#search').on('change', function () {
            let text = $(this).val();
            if (text === '' || text === undefined) {
                rows.empty();
                inWork = false;
                return;
            }
            if (inWork)
                return;
            inWork = true;
            $.ajax({
                url: "https://kitsu.io/api/edge/anime?page[limit]=20&page[offset]=0&filter[text]=" + text,
                type: "GET",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('Accept', 'application/vnd.api+json');
                    xhr.setRequestHeader('Content-Type', 'application/vnd.api+json');
                    xhr.setRequestHeader('Access-Control-Allow-Origin', '*');
                },
                success: function(response) {
                    rows.empty();
                    let data = $(response.data);
                    data.each(function (e) {
                        let title = this.attributes.titles.en;
                        if (title === '' || title === undefined)
                            title = this.attributes.titles.en_jp;
                        if (title === '' || title === undefined)
                            title = this.attributes.titles.en_cn;
                        let desc = this.attributes.synopsis;

                        let altTitle = this.attributes.titles.en_jp;
                        if (altTitle === '' || altTitle === undefined)
                            altTitle = this.attributes.titles.en_cn;

                        let animeId = this.id;
                        desc = desc.substr(0, desc.lastIndexOf('.')) + '.';

                        let cover = undefined;
                        if (this.attributes.posterImage !== null)
                            cover = this.attributes.posterImage.original;
                        let banner = undefined;
                        if (this.attributes.coverImage !== null)
                            banner = this.attributes.coverImage.original;

                        let sDate = new Date(this.attributes.startDate).toDateString('MMM DD YYYY');
                        sDate = sDate.substr(4, sDate.length);
                        let sDateAr = sDate.split(' ');
                        sDate = sDateAr[0] + ' ' + sDateAr[1] + ', ' + sDateAr[2];

                        let status = this.attributes.status;
                        if (status === 'finished')
                            status = 'Finished';
                        else if (status === 'current')
                            status = 'Currently Airing';

                        let age = this.attributes.ageRating + '-' + this.attributes.ageRatingGuide;

                        modalData[e] = [ animeId, title, desc, cover, banner, this.relationships.genres.links.related, sDate, status, age, '', this.attributes.slug, altTitle ];
                        rows.append('\n' +
                            '    <div class="data-row">\n' +
                            '        <div class="row-column" style="max-width: 100px;min-width: 50px">' + animeId + '</div>\n' +
                            '        <div class="row-column" style="max-width: 400px;min-width: 150px">' + title + '</div>\n' +
                            '        <div class="row-column" style="padding-right: 20px">' + desc + '</div>\n' +
                            '        <button onclick="openModalWithData(\''+ e +'\')" type="button" style="margin-left: auto;min-width: 90px"><i class="fas fa-plus button-icon"></i> Add</button>\n' +
                            '    </div>');
                    });
                    inWork = false;
                }
            });
        });
    });
    function openModalWithData(id) {
        let data = modalData[id];
        let title = data[1];
        let genresAPI = data[5];
        let tags = '';
        openModal('#apiModal');
        $.ajax({
            url: genresAPI,
            type: "GET",
            beforeSend: function(xhr) {
                xhr.setRequestHeader('Accept', 'application/vnd.api+json');
                xhr.setRequestHeader('Content-Type', 'application/vnd.api+json');
                xhr.setRequestHeader('Access-Control-Allow-Origin', '*');
            },
            success: function(response) {
                $(response.data).each(function () {
                    tags += this.attributes.name + ' ';
                });
                tags = tags.trim();
                tags = tags.replace(new RegExp(' ', 'g'), ', ');
                data[9] = tags;
                let modal = $('#apiModal');
                let cont = modal.find('.modal-content');
                cont.empty();
                cont.append('' +
                    '<div class="modal-title">Add anime `' + title + '`.</div>' +
                    '<input type="text" id="studioInput" placeholder="Studio">' +
                    '<input type="text" id="transInput" placeholder="Translation">'+
                    '<input value="' + tags + '" type="text" id="tagsInput" placeholder="Tags">'+
                    '<button onclick="saveAnime(\'' + id + '\')">Add</button>'
                );
            }
        });
    }
    function saveAnime(id) {
        let data = modalData[id];
        let animeId = data[0];
        let title = data[1];
        let desc = data[2];
        let cover = data[3];
        let banner = data[4];
        let date = data[6];
        let status = data[7];
        let age = data[8];
        let tags = $('#tagsInput').val();
        let slug = data[10];
        let altTitle = data[11];
        let studio = $('#studioInput').val();
        let translation = $('#transInput').val();
        let modal = $('#apiModal');
        let cont = modal.find('.modal-content');
        cont.empty();
        cont.append('<img src="public/images/loading.svg" style="width: 40px;display: block;margin: 0 auto" class="rotating">');
        $.post('<?=$site_link?>/api/kitsu.php', {
            'id': animeId,
            'title': title,
            'desc': desc,
            'cover': cover,
            'banner': banner,
            'date': date,
            'status': status,
            'age': age,
            'tags': tags,
            'studio': studio,
            'translation': translation,
            'slug': slug,
            'altT': altTitle,
            'key': '<?=$API_KEY?>'
        }, function (e) {
            if (e === 'success') {
                closeModal('#apiModal');
                return;
            }
            let errorCode = e.replace('error ', '');
            let hasErrorCode = (errorCode !== '');
            let errorMessage = hasErrorCode ? 'The variable `' + errorCode + '` was null.' : 'Unknwon error.';
            cont.empty();
            cont.append('' +
                '<div class="modal-title">Add anime failed.</div>' +
                '<div class="modal-subtitle">' + errorMessage + '</div>' +
                '<input type="text" id="studioInput" placeholder="Studio">' +
                '<input type="text" id="transInput" placeholder="Translation">'+
                '<input value="' + tags + '" type="text" id="tagsInput" placeholder="Tags">'+
                '<button onclick="saveAnime(\'' + id + '\')">Add</button>'
            );
        });
    }
</script>
</body>
</html>