<?php
require_once __DIR__ . '/../vendor/autoload.php';

class MacNotifier
{
    public function run($products)
    {
        $this->crawler()
             ->each(function ($node) use ($products) {
                 $uri = $node->attr('href');
                 foreach ($products as $id => $product) {
                     if ($this->contains($uri, $id)) {
                         $this->notify("{$id}：{$product}");
                     }
                 }
             });
    }

    protected function crawler()
    {
        $base_url    = "https://www.apple.com/tw/shop/browse/home/specialdeals/";
        $product_url = "mac/macbook_air/13";
        $url         = $base_url . $product_url;

        $selector = "div#primary div.box.refurb-list > div.box-content > table > tbody > tr > td.specs > h3 > a";

        $client = new Goutte\Client();

        return $client->request('GET', $url)
                      ->filter($selector);
    }

    protected function notify($item)
    {
        $text = "你的產品 {$item} 已經上線！";
        $url = 'https://hooks.slack.com/services/';
        $token = 'your-slack-token';

        $client = new Maknz\Slack\Client($url . $token);
        $message = $client->createMessage();
        $message->send($text);
    }

    protected function contains($haystack, $needle)
    {
        if (strpos($haystack, $needle) !== false) {
            return true;
        }
        return false;
    }
}

$products = [
    'G0TB2TA' => '2015 13.3" MacBook Air 2.2G 雙核心 i7 8GB 256GB',
    'G0TB0TA' => '2015 13.3" MacBook Air 2.2G 雙核心 i7 8GB 512GB'
];

$demo = new MacNotifier();
$demo->run($products);
