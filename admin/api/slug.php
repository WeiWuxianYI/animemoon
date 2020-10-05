<?php
require_once 'apiConfig.php';
header('Content-Type: application/json');
$key = filter_input(INPUT_POST, 'key', FILTER_SANITIZE_STRING);
if ($key != $API_KEY)
    exit('error');
$slug = filter_input(INPUT_POST, 'slug', FILTER_SANITIZE_STRING);
$studio = filter_input(INPUT_POST, 'studio', FILTER_SANITIZE_STRING);
$translation = filter_input(INPUT_POST, 'translation', FILTER_SANITIZE_STRING);
$tags = filter_input(INPUT_POST, 'tags', FILTER_SANITIZE_STRING);
if (!isset($slug, $studio, $translation)) {
    exit(json_encode([
        'error' => 'The `slug`, `studio` and `translation` must specified.'
    ]));
}
$options = array(
    'http' => array(
        'header'  => "Content-type: application/vnd.api+json\r\n".
                     "Accept: application/vnd.api+json\r\n",
        'method'  => 'GET'
    )
);
$context  = stream_context_create($options);
$result = file_get_contents("https://kitsu.io/api/edge/anime?filter[text]=$slug", false, $context);
$result = json_decode($result, true);
if (count($result['data']) == 0) {
    exit(json_encode([ 'error' => "There are no results." ]));
}
$anime = $result['data'][0];
$tiles = $anime['attributes']['titles'];
$animeSlug = $anime['attributes']['slug'];
$title = "";
$altTitle = "";
if (isset($tiles['en'])) {
    $title = $tiles['en'];
} else if (isset($tiles['en_us'])) {
    $title = $tiles['en_us'];
} else if (isset($titles['en_jp'])) {
    $title = $tiles['en_jp'];
} else if (isset($titles['ja_jp'])) {
    $title = $tiles['ja_jp'];
} else if (isset($titles['en_cn'])) {
    $title = $tiles['en_cn'];
}
if (isset($titles['en_jp'])) {
    $altTitle = $tiles['en_jp'];
} else if (isset($titles['ja_jp'])) {
    $altTitle = $tiles['ja_jp'];
} else if (isset($titles['en_cn'])) {
    $altTitle = $tiles['en_cn'];
}
$animeId = $anime['id'];
$description = $anime['attributes']['synopsis'];
$description = substr($description, 0, strripos($description, '.')).'.';
$coverImage = $anime['attributes']['posterImage']['original'];
$bannerImage = $anime['attributes']['coverImage']['original'];

$satTime = strtotime($anime['attributes']['startDate']);
$startedAt = (string)date('M n, Y', $satTime);
$status = $anime['attributes']['status'] == 'current' ? 'Currently Airing' : 'Finished';
$rating = $anime['attributes']['ageRating'].'-'.$anime['attributes']['ageRatingGuide'];

$getGenres = file_get_contents($anime['relationships']['genres']['links']['related'], false, $context);
$getGenres = json_decode($getGenres, true);
$genresData = $getGenres['data'];
$genresList = [];
foreach ($genresData as $genreData) {
    $genresList[] = $genreData['attributes']['name'];
}
$genres = join(', ', $genresList);
if (trim($genres) == "" && !isset($tags)) {
    exit(json_encode([
        'error' => 'There were no `tags` found, you need to specify them.'
    ]));
}
if (isset($tags)) {
    $genres = (string)$tags;
}

$uploadURL = $site_link."/api/kitsu.php";
$uploadDATA = [
    'id' => $animeId,
    'title' => $title,
    'desc' => $description,
    'cover' => $coverImage,
    'banner' => $bannerImage,
    'date' => $startedAt,
    'status' => $status,
    'age' => $rating,
    'tags' => $genres,
    'studio' => $studio,
    'translation' => $translation,
    'slug' => $animeSlug,
    'altT' => $altTitle,
    'key' => $key
];
$uploadOptions = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($uploadDATA)
    )
);
$uploadContext  = stream_context_create($uploadOptions);
$uploadResult = file_get_contents($uploadURL, false, $uploadContext);
exit($uploadResult);