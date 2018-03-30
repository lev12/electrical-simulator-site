<?php

    if ($_SERVER['REDIRECT_URL'] == NULL)
    {
        $page = "home";
    }
    else
    {
        $page = substr($_SERVER['REDIRECT_URL'], 1);
    }

    function notFound()
    {
        echo '<div>ошибка 404</div>';
        echo '<div>страница ненайдена</div>';
        exit();
    }

    function top($title = "electrical simulator")
    {

        
        echo '<!doctype html>
            <html lang="ru">
            <head>
                <link rel="icon" href="pages/electrical-simulator.png">
                <meta charset="UTF-8">
                <title>'.$title.'</title>
                <link rel="stylesheet" type="text/css" href="pages/style.css" media="screen">
                <script type="text/javascript" src="pages/menu.js"></script>
            </head>
            <body>
            <div class="wraper">
                <div class="heder">
                    <div class="logo">
                        <a href="/"> 
                            <img class="logoIcon" src="pages/electrical-simulator.png" width="36" height="36"> 
                            <div class="logoText">Electrical Simulator </div> 
                        </a>
                    </div>
                    <div class="downloadAbout">
                        <a class="menuLink" href="http://electrical-simulator.ru/download.php">скачать</a>
                        <div class="line">|</div>
                        <a class="menuLink" href="http://electrical-simulator.ru/about.php">о нас</a>
                    </div>
                    <div class="menu">
                        <img src="pages/electrical-simulator.png" width="36" height="36">
                    </div>
                </div>
                <div class="content">';
    }
    function footer()
    {
        echo "      </div>
                        <div class=\"footer\">
                              <h4>Electrical simulator 2016</h4>
                            
                        </div>
                    </div>
                </body>
            </html>
        ";

    }

    function block_news($title, $image, $text)
    {
        echo '
                <div class="block">
                <h1 class="title">'.$title.'</h1>
                <img src="'.$image.'"media="screen" width="600px" height="600px">
                <div class="text">
                    '.$text.'
                </div>
                </div>
            ';
    }
    
    if ($page == "home")
    {
    	if ($page == "home") top();
        block_news("Релиз игры","http://electrical-simulator.ru/pages/electrical-simulator.png", "pre-alpha 1");
        if ($page == "home")footer();
    }
    else if (explode("/", $page)[0] == "api")
    {
    	$page = $page . ".php";
    	if (file_exists("api/api.php")) include "api/api.php";
        else notFound();
    }
    else
    {
    	$page = $page . ".php";
        if (file_exists('pages/'.$page.'')) include 'pages/'.$page.'';
        else notFound();
    }
?>

