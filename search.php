<!DOCTYPE html>
<?php require_once("./settings/config.php"); $PAGE_TITLE = 'Anime'; ?>
<?php require_once("./settings/header_footer.php") ?>
<?php

$yearFil = filter_input(INPUT_GET, 'year', FILTER_SANITIZE_STRING);
if (!isset($yearFil))
    $yearFil = '';
$statusFil = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING);
if (!isset($statusFil))
    $statusFil = '';
$genreFil = filter_input(INPUT_GET, 'genre', FILTER_SANITIZE_STRING);
if (!isset($genreFil))
    $genreFil = '';

$sortBy = filter_input(INPUT_GET, 'sortBy', FILTER_SANITIZE_STRING);
if (!isset($sortBy) || trim($sortBy) === '')
    $sortBy = 'text_title';
$sortRawValue = $sortBy;
$sortByValue = '';
switch ($sortBy) {
    case 'text_title': $sortByValue = 'ASC'; break;
    default:
        $sortByValue = "= '$sortBy' DESC";
        $sortBy = 'translation';
        break;
}
$sqlCounter = 0;
$statusSQL = '';
$genreSQL = '';
$yearSQL = '';

function addICS($n) {
    return trim('\'%'.$n.'%\'');
}

if ($statusFil !== '') {
    $sqlCounter++;
    $MSSS = join(" OR `status` LIKE ", array_map('addICS', explode(',', $statusFil)));
    $statusSQL = ($sqlCounter === 1 ? "WHERE" : ($sqlCounter > 1 ? "AND" : "")) . " `status` LIKE ".$MSSS;
}
if ($genreFil !== '') {
    $sqlCounter++;
    $MSSQ = join(" OR `tags` LIKE ", array_map('addICS', explode(',', $genreFil)));
    $genreSQL = ($sqlCounter === 1 ? "WHERE" : ($sqlCounter > 1 ? "AND" : "")) . " `tags` LIKE ".$MSSQ;
}
if ($yearFil !== '') {
    $sqlCounter++;
    $MSSY = join(" OR `released` LIKE ", array_map('addICS', explode(',', $yearFil)));
    $yearSQL = ($sqlCounter === 1 ? "WHERE" : ($sqlCounter > 1 ? "AND" : "")) . " `released` LIKE ".$MSSY;
}
$sql = "SELECT * FROM ao_index ".$statusSQL.' '.$genreSQL.' '.$yearSQL." ORDER BY `$sortBy` $sortByValue";
$q1 = $PDOConn->prepare($sql);
$q1->execute();

$filters = $PDOConn->prepare("SELECT * FROM ao_index");
$filters->execute();

$anime_list = [];
while ($f1 = $q1->fetch(PDO::FETCH_ASSOC)) {
    $anime_list[] = $f1;
    $local_tags = explode(',', $f1['tags']);
    $releasedAt = trim(explode(',', $f1['released'])[1]);
}
unset($f1);


$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
if (!isset($page)) {
    $page = 1;
}
if ($page <= 0) {
    $page = 1;
}
$itemsPerPage = 15;
$numberOfPages = ceil(count($anime_list) / $itemsPerPage);
if ($page > $numberOfPages) {
    $page = $numberOfPages;
}
$anime_list = array_slice($anime_list, ($page - 1) * $itemsPerPage, $itemsPerPage);

$pageURL = "$site_link/search.php?sortBy=$sortRawValue&genre=$genreFil&status=$statusFil&year=$yearFil&page=";

$tags = [];
$statuses = [];
$years = [];
while ($f1 = $filters->fetch(PDO::FETCH_ASSOC)) {
    $local_tags = explode(',', $f1['tags']);
    $releasedAt = trim(explode(',', $f1['released'])[1]);
    foreach ($local_tags as $tag) {
        $tags[trim($tag)] = true;
    }
    $statuses[$f1['status']] = true;
    $years[$releasedAt] = true;
}
ksort($years);
?>
<html>
        <head>
            <title>Search Results | <?=$site_title?></title>
            <meta property="og:title" content="Search Results | <?=$site_title?>" />
            <meta property="og:type" content="profile:Anime World" />
            <meta property="og:url" content="<?=$site_link?>" />
            <meta name="keywords" content="anime, animeworld, watch anime, latest anime, anime online, animeworld online, anime world, anime world online, animeworldonline" />
            <?=$head_tags?>
        </head>
        <body>
	        <?php echo $header; ?>
            <div class="anime-filters">
                <select name="sortBy">
                    <option <?=($sortRawValue === 'text_title' ? 'selected' : '')?> value="title">Sort By</option>
                    <option <?=($sortRawValue === 'dubbed' ? 'selected' : '')?> value="dubbed">Dubbed</option>
                    <option <?=($sortRawValue === 'subbed' ? 'selected' : '')?> value="subbed">Subbed</option>
                    <option <?=($sortRawValue === 'raw' ? 'selected' : '')?> value="raw">Raw</option>
                    <option <?=($sortRawValue === 'release' ? 'selected' : '')?> value="release">Release</option>
                </select>
                <select name="genre" multiple>
                    <option value="">Genre</option>
                    <?php
                    $genresList = explode(',', $genreFil);
                    foreach ($tags as $tag => $v) {
                        $sel = (in_array($tag, $genresList) ? 'selected' : '');
                        echo '<option '.$sel.' value="'.$tag.'">'.$tag.'</option>';
                    }
                    ?>
                </select>
                <select name="status" multiple>
                    <option value="">Status</option>
                    <?php
                    $statusesList = explode(',', $statusFil);
                    foreach ($statuses as $status => $v) {
                        $sel = (in_array($status, $statusesList) ? 'selected' : '');
                        echo "<option $sel value='$status'>$status</option>";
                    }
                    ?>
                </select>
                <select name="year" multiple>
                    <option value="">Year</option>
                    <?php
                    $yearsList = explode(',', $yearFil);
                    foreach ($years as $year => $v) {
                        $sel = (in_array($year, $yearsList) ? 'selected' : '');
                        echo "<option $sel value='$year'>$year</option>";
                    }
                    ?>
                </select>
                <script>
                    $(function ($) {
                        let siteUrl = '<?=$site_link?>/search.php';
                        let selects = $('.anime-filters').find('select');
                        selects.each((e, item) => {
                            let select = $(item);
                            select.on('change', () => {
                                let paramsUrl = siteUrl + '?';
                                let parArr = [];
                                selects.each((e2, item2) => {
                                    parArr.push($(item2).attr('name') + '=' + $(item2).val());
                                });
                                paramsUrl += parArr.join('&');
                                window.location.href = paramsUrl;
                            });
                        });
                    });
                </script>
            </div>
            <?php
                echo "<div class='movies-list filters'><div class='movies-list-content'>";
                $amount = 0;
                foreach($anime_list as $a1)
                {
                    $amount = 1;

                    $tags = $a1['tags'];
                    $title = $a1["title"];
                    $icon = $a1["icon"];
                    $text_title = $a1["text_title"];
                    $eps = $a1["episodes"];
                    $translation = $a1["translation"];
                    $rel = $a1["released"];

                    if($translation == "raw") $translation = "Raw";
                    elseif($translation == "dubbed") $translation = "Dubbed";
                    elseif($translation == "subbed") $translation = "Subbed";
                    else $translation = "Unknown";

                    $rand = uniqid();

                    $tags = explode(', ', $tags);
                    $TAGS_COUNT = 0;
                    $Tags = '';
                    foreach ($tags as $tag)
                    {
                        if($TAGS_COUNT === 2) break;
                        switch ($TAGS_COUNT)
                        {
                            case 0: $Tags = $tag; break;
                            case 1: $Tags .= ', '.$tag; break;
                        }
                        $TAGS_COUNT++;
                    }

                    ?>
                    <div class="movie-box-new">
                        <div id="mv-<?=$rand?>" class="movie-cover-new" style='background-image: url("<?=$icon?>")'>
                            <div class="movie-cover-overlay"></div>
                            <div class="movie-ep-count"><?=$eps?></div>
                        </div>
                        <div id="mv-nm-<?=$rand?>" class="movie-name-new"><span><?=$text_title?></span></div>
                        <div class="movie-episodes-no"><?=$rel?></div>
                        <div class="movie-trans" style="<?=($translation === 'Dubbed' ? 'background: #03A9F4':'')?>"><?=$translation?></div>
                        <script>$("#mv-<?=$rand?>").on('mousedown', function(e) { if(e.which === 2 || e.which === 1) { e.preventDefault(); openWindow("<?=$site_link.'/anime/'.$title?>", e.which); } });</script>
                        <script>$("#mv-nm-<?=$rand?>").on('mousedown', function(e) { if(e.which === 2 || e.which === 1) { e.preventDefault(); openWindow("<?=$site_link.'/anime/'.$title?>", e.which); } });</script>
                    </div>
                    <?php
                }
                echo '</div></div>';
            ?>
            <div class="pagination">
                <?php
                    for ($i = 1;$i <= $numberOfPages;$i++) {
                        $activePage = $page == $i ? " class='active'" : "";
                        echo "<a href='$pageURL$i' $activePage>$i</a>";
                    }
                ?>
            </div>
	        <?php echo $footer;?>
            <?php require_once("./settings/searchbar.php"); ?>
        </body>
</html>
