<?php
require_once("./settings/config.php");
$data = filter_input(INPUT_POST, 'search', FILTER_SANITIZE_STRING);
$SRC = "%{$data}%";
$q1 = $PDOConn->prepare("SELECT * FROM ao_index WHERE title LIKE :src 
                                                        OR text_title LIKE :src 
                                                        OR tags LIKE :src 
                                                        OR released LIKE :src 
                                                        or translation LIKE :src
                                                        or alt_title LIKE :src
                                                        ORDER BY title ASC");
$q1->bindParam(':src', $SRC, PDO::PARAM_STR);
$q1->execute();
$anime = [];
while($fetch = $q1->fetch(PDO::FETCH_ASSOC)) {
    $trans = '';
    if ($fetch['translation'] == 'dubbed')
        $trans = ' (Dub)';
    else if ($fetch['translation'] == 'raw')
        $trans = ' (Raw)';
    $anime[] = [
        'name' => $fetch['text_title'],
        'url' => $site_link.'/list.php?anime='.$fetch['title'],
        'icon' => $fetch['icon'],
        'translation' => $trans,
        'subtitle' => $fetch['episodes'].' ep'.($fetch['episodes'] == 1 ? '' : 's').'. • '.$fetch['released']
    ];
}
echo json_encode($anime);