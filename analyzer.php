<?php
    require_once "vendor/autoload.php";
    //namespace Acme\Demo;
    
    use ApaiIO\Configuration\GenericConfiguration;
    use ApaiIO\Operations\Lookup;
    use ApaiIO\ApaiIO;
    
    $conf = new GenericConfiguration();
    $client = new \GuzzleHttp\Client();
    $request = new \ApaiIO\Request\GuzzleRequest($client);
    
    $conf
        ->setCountry('com')
        ->setAccessKey('AKIAJPHT4VGUUI2J5ELQ')
        ->setSecretKey('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')
        ->setAssociateTag('lymphrunne-20')
        ->setRequest($request)
        ->setResponseTransformer(new \ApaiIO\ResponseTransformer\XmlToArray());
    $apaiIO = new ApaiIO($conf);
    
    /*$search = new Search();
    $search->setCategory('DVD');
    $search->setActor('Bruce Willis');
    $search->setKeywords('Die Hard');*/
    
    /*$lookup = new Lookup();
    $lookup->setItemId('B01H745CD6');
    $lookup->setResponseGroup(array('Reviews'));
    
    
    $formattedResponse = $apaiIO->runOperation($lookup);*/
    
    
    //echo "<a href='" . $formattedResponse['Items']['Item']['CustomerReviews']['IFrameURL'] . "'>link</a>";
    
    $url = "https://www.amazon.com/product-reviews/B0758V3YTX/ref=cm_cr_getr_d_show_all?ie=UTF8&linkCode=xm2&showViewpoints=1&sortBy=recent&tag=lymphrunne-20&pageNumber=1&reviewerType=all_reviews";
    
    
    use Sunra\PhpSimple\HtmlDomParser;
    $parser = new HtmlDomParser();
    
    //$url = $formattedResponse['Items']['Item']['CustomerReviews']['IFrameURL'];
    $elem_name = "div.review";
    
    $arrContextOptions=array(
        "ssl"=>array(
            "cafile" => "cacert.pem",
            "verify_peer"=> true,
            "verify_peer_name"=> true
        ),
        'http' => array(
            'user_agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1'
        )
    );

    $dom = $parser->file_get_html( $url, false, stream_context_create($arrContextOptions) );
    
    
    $elems = $dom->find($elem_name);
    echo"<pre>";
    foreach($elems as $review){
        
        $star = $review->find('span.a-icon-alt');
        var_dump($star[0]->nodes[0]);
        //echo $star->nodes[0]->_->4 . "<br>";
    }
    echo "</pre>";
    
    
    
    //echo $dom;
    
     /*// create curl resource 
    $ch = curl_init(); 

    // set url 
    curl_setopt($ch, CURLOPT_URL, $url); 

    //return the transfer as a string 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1');


    // $output contains the output string 
    $output = curl_exec($ch); 

    // close curl resource to free up system resources 
    curl_close($ch);
    
    $dom = new DOMDocument();
    @$dom->loadHTML($output);
    
    $totalReviews = 0;
    $starRating = 0;
    $reviewCount = array();
    
    preg_match('/totalReviewCount\"\>(\d+)/', $output, $totalReviews, PREG_OFFSET_CAPTURE);
    preg_match('/arp-rating-out-of-text">(\d+.\d+)/', $output, $starRating, PREG_OFFSET_CAPTURE);
    
    for($c = 5; $c > 0; $c--){
        preg_match('/'. $c . 'star" aria-label="(\d+)/', $output, $reviews, PREG_OFFSET_CAPTURE);
        $reviewCalc = round(($reviews[1][0]/100) * $totalReviews[1][0]);
        $reviewCount[$c] = array($reviewCalc, $reviews[1][0] / 100);
    }*/
    
    
    echo "<pre>";
    //var_dump($totalReviews[1][0]);
    //var_dump($starRating[1][0]);
    //var_dump($reviewCount);
    //var_dump($dom);
    echo "</pre>";
    