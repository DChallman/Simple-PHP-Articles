<?php

namespace DChallman\SimplePHPArticles;

class SPA
{
    private static $instance = null;

    private function __construct(){}

    public static function getInstance(){
        if(self::$instance == null)
            self::$instance = new SPA();

        return self::$instance;
    }

    public function init(){
        if(session_status() === PHP_SESSION_NONE){ session_start(); }

        if($_SESSION['articles'] == null){ $_SESSION['articles'] = array(); }

        if($_SESSION['spaCount'] == null){ $this->setArticles($this->getArticles()); }
    }

    public function show($nWords = 50){
        if(isset($_GET['article'])){
            $back = parse_url($_SERVER['HTTP_REFERER']);
            $e = $_SESSION['articles'][$_GET['article']];

            echo "<h4>" . $e['date'] . "<div style='float: right;'>[<a href=" . $back['path'] . ">Back</a>]</div></h4>";
            echo "<h1>" . $e['title'] . "</h1>";
            echo "<p>" . $e['body'] . "</p>";
        }else{
            $this->setArticles($this->getArticles());

            foreach($_SESSION['articles'] as $e){
                if(str_word_count($e['body'], 0) > $nWords){
                    $words = str_word_count($e['body'], 2);
                    $pos = array_keys($words);
                    $text = substr($e['body'], 0, $pos[$nWords])."<a href=\"articles.php?article=".$e['file']."\">...</a>";
                }else
                    $text = $e['body'];

                echo "<div>";
                echo "<h4 style='margin-bottom: 0;'>" . $e['date'] . "</h4>";
                echo "<h2 style='text-align:center; margin-top: 0;'>" . "<a href=articles.php?article=" . $e['file'] . ">" . $e['title'] . "</a></h2>";
                echo "<p>" . $text . "</p>";
                echo "</div>";
                echo "<hr style='clear: both;'>";
            }
        }
    }

    public function latest($num = 3){
        sizeof($_SESSION['articles']) > $num ? $count = $num : $count = sizeof($_SESSION['articles']);

        if($count > 0){
            echo "<div>";
            echo "<h1>Latest</h1>";

            $i = 1;
            foreach($_SESSION['articles'] as $e){
                echo "<h4 style='display: inline-block;'>" . $e['date'] . "</h4>";
                echo "<p style='display: inline-block;'>" . "<a href=articles.php?article=" . $e['file'] . ">" . $e['title'] . "</a></p><br>";

                if($i++ >= $num)
                    break;
            }

            echo "</div>";
        }
    }

    private function getArticles(){
        $glob = glob($_SERVER["DOCUMENT_ROOT"] . "/articles/*.spa");
        $_SESSION['spaCount'] =  count($glob);
        return $glob;
    }

    private function setArticles($articleGlob){
        if(sizeof($_SESSION['articles']) != $_SESSION['spaCount'])
        {
            $articles = array();
            for($i = 0; $i < count($articleGlob); $i++)
            {
                $path = $articleGlob[$i];
                $cur = fopen($path, "r") or die ("Unable to open file!");
                $file = explode('articles/', $path, 2)[1];

                $article = array(
                    $file => array(
                        'path'  => $path,
                        'file'  => $file,
                        'date'  => trim(fgets($cur)),
                        'title' => fgets($cur),
                        'body'  => fread($cur, filesize($articleGlob[$i]))
                    )
                );

                $articles = array_merge($articles, $article);
                fclose($cur);
            }

            $this->kvSort($articles, function($x, $y){
                return strtotime($y['date']) - strtotime($x['date']);
            });

            $_SESSION['articles'] = $articles;
        }
    }

    private function kvSort(&$array, $callback){
        $temp_array[key($array)] = array_shift($array);

        foreach($array as $key => $val){
            $offset = 0;
            $found = false;
            foreach($temp_array as $tmp_key => $tmp_val){
                if(!$found && $callback($val, $tmp_val)){
                    $temp_array = array_merge(
                        array_slice($temp_array, 0, $offset),
                        array($key => $val),
                        array_slice($temp_array, $offset)
                    );
                    $found = true;
                    break;
                }
                $offset++;
            }
            if(!$found) $temp_array = array_merge($temp_array, array($key => $val));
        }
        $array = $temp_array;
    }
}
?>
