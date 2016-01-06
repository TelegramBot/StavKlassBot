<?php

require_once "vendor/autoload.php";

try {
    $token = 'YOUR_BOT_API_TOKEN';
    $bot = new \TelegramBot\Api\Client($token);

    $bot->inlineQuery(function (\TelegramBot\Api\Types\Inline\InlineQuery $inlineQuery) use ($bot) {
        /* @var \TelegramBot\Api\BotApi $bot */

        $html = file_get_contents('http://stavklass.ru/images/search?utf8=✓&image[text]=' . $inlineQuery->getQuery());

        $doc = new DOMDocument();
        $doc->loadHTML($html);

        $xpath = new DOMXPath($doc);
        $img = $xpath->query('//div[@class="image-content"]/a[@class="image"]/img');
        $result = [];

        for ($i = 0; $i < $img->length; $i++) {
            /* @var \DOMNodeList $img */
            $node = $img->item($i);
            $url = $node->getAttribute('src');
            list($width, $height) = getimagesize($url);

            $result[] = new \TelegramBot\Api\Types\Inline\InlineQueryResultPhoto(
                $url, $url, $url, 'image/jpeg', $width, $height
            );
        }

        $bot->answerInlineQuery($inlineQuery->getId(), $result, 0);
    });

    $bot->run();

} catch (\TelegramBot\Api\Exception $e) {
    $e->getMessage();
}
