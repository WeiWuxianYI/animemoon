<?php
$PAGE_TITLE = 'Home';
?>
<!DOCTYPE html>
<?php require_once("./settings/config.php") ?>
<?php require_once("./settings/header_footer.php") ?>
<html>
        <head>
            <title><?=$site_title?></title>
            <meta property="og:title" content="Home | <?=$site_title?>" />
            <meta property="og:url" content="<?=$site_link?>" />
            <meta property="og:description" content="Watch latest episodes of best animes online as soon as they are released and discuss them with many other people online!" />

            <meta name="description" content="Watch latest episodes of best animes online as soon as they are released and discuss them with many other people online!" />
            <meta name="viewport" content="width=device-width, height=device-height, initial-scale=' + scale + ', maximum-scale=' + scale + ', user-scalable=0">

            <?=$head_tags?>
            <link rel="stylesheet" href="<?=$site_link?>\css\homepage.min.css">
        </head>
        <body>
                <div class="pageloaded" id="pageloaded" style='display: block;'>
	                <?=$header?>
                    <?php
                        $counter = 0;
                        $newestEpisodes = $PDOConn->prepare("SELECT DISTINCT `title` FROM ao_episodes WHERE `homepage`='1' ORDER BY id DESC LIMIT 10");
                        $newestEpisodes->execute();
                        while ($nEpFetch = $newestEpisodes->fetch(PDO::FETCH_ASSOC)) {
                            $AOTL = $nEpFetch['title'];
                            $indexAo = $PDOConn->prepare("SELECT * FROM ao_index WHERE `title`=:title LIMIT 1");
                            $indexAo->bindParam(':title', $AOTL, PDO::PARAM_STR);
                            $indexAo->execute();
                            $indexAoFetch = $indexAo->fetch(PDO::FETCH_ASSOC);
                            $counter++;
                            ?>
                            <div bhdr="<?=$counter?>" class="b-header<?=($counter === 1 ? '' : ' inactive')?>" style="background-image: url('<?=$indexAoFetch['link']?>');">
                                <div class="b-header-overlay"></div>
                                <div class="b-header-tags">
                                    <?=str_replace(', ', ' | ', $indexAoFetch['tags'])?>
                                </div>
                                <a class="b-header-name" href="<?=$site_link.'/anime/'.$indexAoFetch['title']?>">
                                    <?=$indexAoFetch['text_title']?>
                                </a>
                                <div class="b-header-description">
                                    <?=$indexAoFetch['description']?>
                                </div>
                            </div>
                            <?php
                        }
                    ?>
                    <div id="leftSw" class="b-header-arrow">
                        <i class="fas fa-caret-left bha-icon"></i>
                    </div>
                    <div id="rightSw" class="b-header-arrow" style="left: auto;right: 0">
                        <i class="fas fa-caret-right bha-icon"></i>
                    </div>
                    <div class="movies-list">
                        <div class="mv-title">Latest Animes</div>
                        <div class="movies-list-content-home">
                            <?php
                            $PDO_HOME_PAGE = '1';
                            $q1 = $PDOConn->prepare("SELECT * FROM ao_episodes WHERE homepage = :home ORDER BY id DESC LIMIT 27");
                            $q1->bindParam(':home', $PDO_HOME_PAGE, PDO::PARAM_STR);
                            $q1->execute();

                            $counter = 1;
                            $serId = 0;
                            while($a1 = $q1->fetch(PDO::FETCH_ASSOC))
                            {
                                $anime = $a1["title"];
                                $q2 = $PDOConn->prepare("SELECT * FROM ao_index WHERE title = :anime LIMIT 1");
                                $q2->bindParam(':anime', $anime, PDO::PARAM_STR);
                                $q2->execute();
                                $a2 = $q2->fetch(PDO::FETCH_ASSOC);

                                $q3 = $PDOConn->prepare("SELECT * FROM ao_index WHERE title = :anime AND translation = 'subbed' LIMIT 1");
                                $q3->bindParam(':anime', $anime, PDO::PARAM_STR);
                                $q3->execute();
                                $a3 = $q3->fetch(PDO::FETCH_ASSOC);

                                $tags = $a2['tags'];
                                $title = $a2["text_title"];
                                $icon = $a2["icon"];
                                $popular = $a2["popular"];
                                $eps = $a2["episodes"];
                                if($popular == "1")  $popular = "<div class='popularflag'>Popular</div>";
                                elseif($popular == "0") $popular = "";
                                $ep = $a1["ep_title"];
                                $ep_text = $a1["ep_text_title"];
                                $ep_num = $a1["ep_num"];
                                $homepage = $a1["homepage"];
                                $time = $a1["time"];
                                $vws = $a1["views"];
                                $desc = $a3["description"];
                                $rel = $a2['released'];
                                if(strlen($icon) < 4) $icon = "$site_link/img/banner/soon.png";
                                $translation = $a2["translation"];

                                if($translation == "raw") $translation = "Raw";
                                elseif($translation == "dubbed") $translation = "Dubbed";
                                elseif($translation == "subbed") $translation = "Subbed";
                                else$translation = "Unknown";

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

                                if($homepage == 1)
                                {
                                    ?>
                                    <div class="movie-box-new mbn-home">
                                        <div id="mv-<?=$serId?>" class="movie-cover-new" style='background-image: url("<?=$icon?>")'>
                                            <div class="movie-cover-overlay"></div>
                                            <div class="movie-ep-count"><?=$eps?></div>
                                        </div>
                                        <div id="mv-nm-<?=$serId?>" class="movie-name-new"><span><?=$title?></span></div>
                                        <div class="movie-episodes-no"><?=$rel?></div>
                                        <div class="movie-trans" style="<?=($translation === 'Dubbed' ? 'background: #03A9F4':'')?>"><?=$translation?></div>
                                        <script>$("#mv-<?=$serId?>").on('mousedown', function(e) { if(e.which === 2 || e.which === 1) { e.preventDefault(); openWindow("<?=$site_link.'/anime/'.$anime.'/'.$ep_num?>", e.which); } });</script>
                                        <script>$("#mv-nm-<?=$serId?>").on('mousedown', function(e) { if(e.which === 2 || e.which === 1) { e.preventDefault(); openWindow("<?=$site_link.'/anime/'.$anime.'/'.$ep_num?>", e.which); } });</script>
                                    </div>
                                    <?php
                                    $counter++;
                                    $serId++;
                                }
                            }
                            ?>
                        </div>

                        <div class="mv-title">Random Animes</div>
                        <div class="movies-list-content-home">
                            <?php
                            $PDO_HOME_PAGE = '1';
                            $q1 = $PDOConn->prepare("SELECT * FROM ao_episodes ORDER BY RAND()");
                            $q1->bindParam(':home', $PDO_HOME_PAGE, PDO::PARAM_STR);
                            $q1->execute();

                            $counterRA = 0;
                            $serId = 0;
                            while($a1 = $q1->fetch(PDO::FETCH_ASSOC))
                            {
                                if ($counterRA == 27) {
                                    break;
                                }
                                $anime = $a1["title"];
                                $q2 = $PDOConn->prepare("SELECT * FROM ao_index WHERE title = :anime LIMIT 1");
                                $q2->bindParam(':anime', $anime, PDO::PARAM_STR);
                                $q2->execute();
                                $a2 = $q2->fetch(PDO::FETCH_ASSOC);

                                $q3 = $PDOConn->prepare("SELECT * FROM ao_index WHERE title = :anime AND translation = 'subbed' LIMIT 1");
                                $q3->bindParam(':anime', $anime, PDO::PARAM_STR);
                                $q3->execute();
                                $a3 = $q3->fetch(PDO::FETCH_ASSOC);

                                $tags = $a2['tags'];
                                $title = $a2["text_title"];
                                $icon = $a2["icon"];
                                $popular = $a2["popular"];
                                $eps = $a2["episodes"];
                                if($popular == "1")  $popular = "<div class='popularflag'>Popular</div>";
                                elseif($popular == "0") $popular = "";
                                $ep = $a1["ep_title"];
                                $ep_text = $a1["ep_text_title"];
                                $ep_num = $a1["ep_num"];
                                $homepage = $a1["homepage"];
                                $time = $a1["time"];
                                $vws = $a1["views"];
                                $desc = $a3["description"];
                                $rel = $a2['released'];
                                if(strlen($icon) < 4) $icon = "$site_link/img/banner/soon.png";
                                $translation = $a2["translation"];

                                if($translation == "raw") $translation = "Raw";
                                elseif($translation == "dubbed") $translation = "Dubbed";
                                elseif($translation == "subbed") $translation = "Subbed";
                                else$translation = "Unknown";

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

                                if($homepage == 1)
                                {
                                    ?>
                                    <div class="movie-box-new mbn-home">
                                        <div id="mv-<?=$serId?>-1" class="movie-cover-new" style='background-image: url("<?=$icon?>")'>
                                            <div class="movie-cover-overlay"></div>
                                            <div class="movie-ep-count"><?=$eps?></div>
                                        </div>
                                        <div id="mv-nm-<?=$serId?>-1" class="movie-name-new"><span><?=$title?></span></div>
                                        <div class="movie-episodes-no"><?=$rel?></div>
                                        <div class="movie-trans" style="<?=($translation === 'Dubbed' ? 'background: #03A9F4':'')?>"><?=$translation?></div>
                                        <script>$("#mv-<?=$serId?>-1").on('mousedown', function(e) { if(e.which === 2 || e.which === 1) { e.preventDefault(); openWindow("<?=$site_link.'/anime/'.$anime.'/'.$ep_num?>", e.which); } });</script>
                                        <script>$("#mv-nm-<?=$serId?>-1").on('mousedown', function(e) { if(e.which === 2 || e.which === 1) { e.preventDefault(); openWindow("<?=$site_link.'/anime/'.$anime.'/'.$ep_num?>", e.which); } });</script>
                                    </div>
                                    <?php
                                    $counterRA++;
                                    $serId++;
                                }
                            }
                            ?>
                        </div>
                    </div>
                <?=$footer?>
                <?php require_once 'settings/searchbar.php'; ?>
                </div>
        </body>
    <script>
        $(function ($) {
            let currentPage = 1;
            $('#leftSw').on('click', function () {
                currentPage--;
                if (currentPage <= 0)
                    currentPage = 3;
                if (currentPage >= 4)
                    currentPage = 1;
                $('[bhdr]').each(function () {
                    let item = $(this);
                    let id = item.attr('bhdr');
                    console.log(currentPage);
                    if (currentPage.toString() === id.toString()) {
                        item.removeClass('inactive');
                    } else {
                        item.addClass('inactive');
                    }
                });
            });
            $('#rightSw').on('click', function () {
                currentPage++;
                if (currentPage <= 0)
                    currentPage = 10;
                if (currentPage >= 11)
                    currentPage = 1;
                $('[bhdr]').each(function () {
                    let item = $(this);
                    let id = item.attr('bhdr');
                    console.log(currentPage);
                    if (currentPage.toString() === id.toString()) {
                        item.removeClass('inactive');
                    } else {
                        item.addClass('inactive');
                    }
                });
            });
        });
    </script>
</html>
