<?php
        $head_tags = "
            <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
	        
	        <link 
href=\"https://fonts.googleapis.com/css?family=Rubik:300,400,500,700\" 
rel=\"stylesheet\">
	        <link rel=\"stylesheet\" href=\"$site_link/css/style.css$style_version\">
	        <link rel=\"stylesheet\" href=\"$site_link/css/app.min.css$style_version\">
	        <script src=\"$site_link/plugins/Font-Awesome-5.3.1/font-awesome-5.3.1.js$style_version\"></script>
            <script src=\"$site_link/plugins/appAM.js$style_version\"></script>
            <script 
src=\"$site_link/plugins/EpisodesPagination.js$style_version\"></script>
	        
	        
            <meta name='keywords' content='anime, world, online, free, 
subbed, dubbed, latest, episodes, watch, animeworld, animeworld.online' 
/>
            <meta name='author' content='Rared Designs' />
            <meta name='theme-color' content='black'>
            <meta property='og:type' content='profile:$site_title' />
            <meta charset='utf-8'>
            <meta http-equiv='X-UA-Compatible' content='IE=edge'>
            <meta name='viewport' content='width=1280'>
            <link rel='shortcut icon' 
href='$site_link/images/favicon.ico'>
            <link rel='icon' href='$site_link/images/favicon.ico'>
			
	        <meta name='msapplication-TileImage' 
content='$site_link/images/favicon.ico'>
	        <meta name='application-name' content='$site_title'>
	        <meta name='msapplication-TileColor' content='#181818'>
	        <meta name='msapplication-TileImage' 
content='$site_link/images/favicon.ico'>
			
			<meta property='og:image' 
content='$site_link/images/favicon.ico' />
        ";

        if(!isset($PAGE_TITLE)) $PAGE_TITLE = ''; if(!isset($HOME_PAGE)) 
$HOME_PAGE = '#'; if(!isset($ANIME_PAGE)) $ANIME_PAGE = '#';

    $header =
        "
            <div class=\"sidebar-new\">
                <a href='$site_link' class=\"sidebar-new-button ".($PAGE_TITLE === 'Home' ? 'active' : '')."\"><i class=\"fas fa-home\"></i></a>
                <div class=\"sidebar-new-delimiter\"></div>
                <a href='$site_link/profile/' class=\"sidebar-new-button ".($PAGE_TITLE === 'Account' ? 'active' : '')."\"><i class=\"far fa-user-circle\"></i></a>
                <div id='searchButton' class=\"sidebar-new-button\"><i class=\"fas fa-search\"></i></div>
                <a href='$site_link/search.php' class=\"sidebar-new-button ".($PAGE_TITLE === 'Anime' ? 'active' : '')."\"><i class=\"fas fa-film\"></i></a>
                <div class=\"sidebar-new-delimiter\"></div>
                <a target='_blank' href='https://discord.gg/AnAzRrf' class=\"sidebar-new-button\"><i class=\"fab fa-discord\"></i></a>
                <a target='_blank' href='https://twitter.com/AnimemoonN' class=\"sidebar-new-button\"><i class=\"fab fa-twitter\"></i></a>
                <a target='_blank' href='https://www.patreon.com/animemoon' class=\"sidebar-new-button\"><i class=\"fab fa-patreon\"></i></a>
                <div class=\"sidebar-new-delimiter\"></div>
                <a href='$site_link/tos.php' class=\"sidebar-new-button ".($PAGE_TITLE === 'TOS' ? 'active' : '')."\"><i class=\"far fa-file-alt\"></i></a>
            </div>
        ";
    $footer =
        "
        ";
