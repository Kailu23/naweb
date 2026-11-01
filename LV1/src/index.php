<?php
include_once 'DiplomskiRadovi.php';
include_once 'simple_html_dom.php';

function fetchHTML($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
}
function parseHTML($html)
{
    $dom = new DOMDocument();

    libxml_use_internal_errors(true);

    $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

    libxml_clear_errors();

    return new DOMXPath($dom);
}
for ($page = 2; $page <= 6; $page++) {
    $url = "https://stup.ferit.hr/index.php/zavrsni-radovi/page/$page/";

    $html = fetchHTML($url);

    if (!$html) {
        echo "Error while fetching page $page\n";
        continue;
    }

    try {
        $xpath = parseHTML($html);

        $articles = $xpath->query("//article[contains(@class, 'fusion-post-medium')]");

        if ($articles->length == 0) {
            echo "Nisu pronađeni članci na stranici $page\n";
            continue;
        }

        echo "Pronađeno {$articles->length} radova na stranici $page </br>";

        foreach ($articles as $article) {
            $titleNodes = $xpath->query(".//h2[contains(@class, 'blog-shortcode-post-title')]//a", $article);

            if ($titleNodes->length == 0) continue;

            $titleNode = $titleNodes->item(0);
            $naziv_rada = trim($titleNode->textContent);
            /** @var DOMElement $titleNode */
            $link_rada = $titleNode->getAttribute('href');

            $imgNodes = $xpath->query(".//img", $article);
            $oib_tvrtke = '';

            if ($imgNodes->length > 0) {
                $img = $imgNodes->item(0);
                /** @var DOMElement $img */
                $src = $img->getAttribute('src');

                if (preg_match('/(\d{11})/', $src, $matches)) {
                    $oib_tvrtke = $matches[1];
                }
            }

            $textNodes = $xpath->query(".//div[contains(@class, 'fusion-post-content-container')]//p", $article);
            $tekst_rada = '';

            if ($textNodes->length > 0) {
                $tekst_rada = trim($textNodes->item(0)->textContent);
            }

            $rad = new DiplomskiRadovi();
            $rad = new DiplomskiRadovi([
                'naziv_rada' => $naziv_rada,
                'tekst_rada' => $tekst_rada,
                'link_rada' => $link_rada,
                'oib_tvrtke' => $oib_tvrtke
            ]);
            $rad->save();
            echo ' ' . $rad->get_naziv_rada() . '</br>';
            echo ' ' . $rad->get_tekst_rada() . '</br>';
            echo ' ' . $rad->get_oib_tvrtke() . '</br>';
            echo ' ' . $rad->get_link_rada() . '</br>';
            echo '</br>';
        }
    } catch (Exception $e) {
        echo "Error while parsing page $page: " . $e->getMessage() . "\n";
        continue;
    }

    echo '</br></br></br></br>';
}
