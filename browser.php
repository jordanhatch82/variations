<?php
    require_once "vendor/autoload.php";
    
    use ApaiIO\Configuration\GenericConfiguration;
    use ApaiIO\Operations\Lookup;
    use ApaiIO\ApaiIO;
    use Behat\Mink;
    
    
    // Choose a Mink driver. More about it in later chapters.
    $driver = new \Behat\Mink\Driver\GoutteDriver();
    
    $ASIN = $_GET['asin'];
    
    $session = new \Behat\Mink\Session($driver);
    $session2 = new \Behat\Mink\Session($driver);
    
    $url = 'https://www.amazon.com/product-reviews/'. $ASIN .  '/ref=cm_cr_getr_d_show_all?ie=UTF8&linkCode=xm2&showViewpoints=1&sortBy=recent&tag=lymphrunne-20&pageNumber=1&reviewerType=all_reviews';
      
    // start the session
    $session->start();
    $session->visit($url);
    $page = $session->getPage();
    
    //array to contain all the item level stats
    $itemInfo = array();
    
    //total count of reviews for item
    if(null !== $page->find('css', 'span.totalReviewCount')){
       $itemInfo['totalReviews']= $page->find('css', 'span.totalReviewCount')->getText();
    }
    
    //how many pages of reviews
    $reviewPageButtons = $page->findAll('css', 'li.page-button a');
    $reviewPageCount = $reviewPageButtons[sizeof($reviewPageButtons)-1]->getText();
    echo "<h4>". $reviewPageCount . " pages of reviews</h4>";
    
    //star rating for item
    $itemInfo['starRating'] = array_slice(explode(" ", $page->find('css', 'span.arp-rating-out-of-text')->getText(),2), 0 , 1);
    
    //get count of reviews by star rating
    for($c = 5; $c > 0; $c--){
        $tagName = 'a.'.$c.'star';
        $currentStarRating = $page->find('css', $tagName);
        if(null !== $currentStarRating){
            if ($currentStarRating->hasAttribute('aria-label')) {
                $text = $currentStarRating->getAttribute('aria-label');
                preg_match("/\((\d+)%\)/", $text , $count);;
                
            }
            $itemInfo['reviewCountByStar'][$c] = round(($count[1]/100) * $itemInfo['totalReviews']);
        }
        else $itemInfo['reviewCountByStar'][$c] = 0;
    }
   
        
    $reviewInfo = array();
    //iterate by page here
    
    $session2->start();
    $reviewNumber = 0;
    $reviewPage = 1;
    
    
    while($reviewPage <= $reviewPageCount){
        $newUrl = "https://www.amazon.com/product-reviews/'. $ASIN .  '/ref=cm_cr_getr_d_show_all?ie=UTF8&linkCode=xm2&showViewpoints=1&sortBy=recent&tag=lymphrunne-20&pageNumber=".$reviewPage."&reviewerType=all_reviews";
        $session2->visit($newURL);
        //echo $session2->getCurrentUrl() . "<br>";
        $newPage = $session2->getPage();
        $reviews = $newPage->findAll('css', 'div.review');
        if(null !== $reviews){
            foreach($reviews as $review){
                //get star review
                $starText = explode(' ', $review->find('css', 'i.review-rating span.a-icon-alt')->getText(), 2);
                $reviewInfo[$reviewNumber]['stars'] = $starText[0];
                //get review title
                $reviewInfo[$reviewNumber]['title'] = $review->find('css', 'a.review-title')->getText();
                //get review date
                $reviewDate = explode(' ', $review->find('css', 'span.review-date')->getText(), 2);
                $reviewInfo[$reviewNumber]['date'] = strtotime($reviewDate[1]);
                //get variation fields and values
                $variationArray = array();
                $variationData = $review->find('css', 'div.review-data')->getText();
                //determine if it's verified or not
                $variationData = explode('|',$variationData);
                if(in_array('Verified Purchase',$variationData)) $reviewInfo[$reviewNumber]['verified'] = true;
                else $reviewInfo[$reviewNumber]['verified'] = false;
                //parse the variation info
                foreach($variationData as $variation){
                    if($variation === "Verified Purchase") break;
                    $exploded = explode(': ', $variation, 2);
                    $variationArray[$exploded[0]] = $exploded[1];
                }
                $reviewInfo[$reviewNumber]['variations'] = $variationArray;
                
                //set the page adjustment for the key here
                //must be the last thing
                $reviewNumber++;
            }
        }
        else echo "<h1>No reviews found</h1>";
        $reviewPage++;
    }
    
    $session->stop();
    $session2->stop();
    
    $analyzedReviews = array();
    
    foreach($reviewInfo as $reviewData){
        foreach($reviewData['variations'] as $key => $value){
            if($key !== ""){
                $analyzedReviews[$key][$value]['label'] = $value;
                $analyzedReviews[$key][$value]['count']++;
                if($reviewData['verified']) $analyzedReviews[$key][$value]['verified']++;
            }
        }
    }
    
    echo "<h1>Item Info</h1>";
    echo "<p>Total Reviews - " . $itemInfo['totalReviews'] . "</p>";
    echo "<p>Star Rating - " . $itemInfo['starRating'][0] . " stars</p>";
    foreach($itemInfo['reviewCountByStar'] as $star => $count){
        echo "<p>" . $star . " stars - " . $count . " reviews</p>";
    }
    
    foreach($analyzedReviews as $key=>$variation){
        usort($variation, function($a, $b) {
            return $b['count'] <=> $a['count'];
        });
        echo "<h1>$key</h1>";
        foreach($variation as $label => $count){
            echo '<p>'.$count["label"].' - '.$count["count"].' reviews - '.$count["verified"].' verified</p>';
        }
        
    }
    
    
    
    
    
    
    
    
    
    