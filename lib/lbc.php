<?php

class Lbc_Ad
{
    protected $_id;
    protected $_link;
    protected $_title;
    protected $_description;
    protected $_price;
    protected $_date;
    protected $_category;
    protected $_county;
    protected $_city;
    protected $_professional;
    protected $_thumbnail_link;
    protected $_urgent;
    
    
    /**
    * @param int $id
    * @return Lbc_Ad
    */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }
    
    /**
    * @return int
    */
    public function getId()
    {
        return $this->_id;
    }
    
    
    /**
     * @param string $link
     * @return Lbc_Ad
     */
    public function setLink($link)
    {
        $this->_link = $link;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getLink()
    {
        return $this->_link;
    }
    
    
    /**
     * @param string $title
     * @return Lbc_Ad
     */
    public function setTitle($title)
    {
        $this->_title = $title;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }
    
    
    /**
     * @param string $description
     * @return Lbc_Ad
     */
    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }
    
    
    /**
     * @param int $price
     * @return Lbc_Ad
     */
    public function setPrice($price)
    {
        $this->_price = (int) preg_replace('/[^0-9]*/', '', $price);
        return $this;
    }
    
    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->_price;
    }
    
    
    /**
     * @param Zend_Date $date
     * @return Lbc_Ad
     */
    public function setDate($date)
    {
        $this->_date = $date;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getDate()
    {
        return $this->_date;
    }
    
    
    /**
     * @param string $category
     * @return Lbc_Ad
     */
    public function setCategory($category)
    {
        $this->_category = $category;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->_category;
    }
    
    
    /**
     * @param string $county
     * @return Lbc_Ad
     */
    public function setCounty($county)
    {
        $this->_county = $county;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getCounty()
    {
        return $this->_county;
    }
    
    
    /**
     * @param string $city
     * @return Lbc_Ad
     */
    public function setCity($city)
    {
        $this->_city = $city;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getCity()
    {
        return $this->_city;
    }
    
    
    /**
     * @param bool $professionnal
     * @return Lbc_Ad
     */
    public function setProfessionnal($professionnal)
    {
        $this->_professionnal = $professionnal;
        return $this;
    }
    
    /**
     * @return bool
     */
    public function getProfessionnal()
    {
        return $this->_professionnal;
    }
    
    
    /**
     * @param string $thumbail
     * @return Lbc_Ad
     */
    public function setThumbnailLink($thumbail)
    {
        $this->_thumbnail_link = $thumbail;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getThumbnailLink()
    {
        return $this->_thumbnail_link;
    }
    
    
    /**
     * @param bool $urgent
     * @return Lbc_Ad
     */
    public function setUrgent($urgent)
    {
        $this->_urgent = (bool)$urgent;
        return $this;
    }
    
    /**
     * @return bool
     */
    public function getUrgent()
    {
        return $this->_urgent;
    }
}

class Lbc
{
    public static function formatUrl($url)
    {
        $aUrl = parse_url($url);
        if (!isset($aUrl["host"]) || $aUrl["host"] != "www.leboncoin.fr") {
            throw new Exception("Url invalide");
        }
        if (isset($aUrl["query"])) {
            parse_str($aUrl["query"], $query);
            unset($query["o"]);
            $url = str_replace($aUrl["query"], http_build_query($query), $url);
        }
        return $url;
    }
}


class Lbc_Parser
{
    static function process($content) {
        $timeToday = strtotime(date("Y-m-d")." 23:59:59");
        $dateYesterday = $timeToday - 24*3600;

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($content);
        $divsAd = $dom->getElementsByTagName("div");
        $ads = array();

        // date mapping
        $months = array(
            "jan" => 1, "fév" => 2, "mars" => 3, "avr" => 4,
            "mai" => 5, "juin" => 3, "juillet" => 7, "août" => 8,
            "sept" => 9, "oct" => 10, "nov" => 11,
            "dec" => 12
        );

        foreach ($divsAd AS $result) {
            if (false === strpos($result->getAttribute("class"), "ad-lbc")) {
                continue;
            }
            $ad = new Lbc_Ad();
            $ad->setProfessionnal(false)->setUrgent(false);
            $parent = $result->parentNode;
            if ($parent->tagName == "a") {
                $a = $parent;
            } else {
                $aTags = $result->getElementsByTagName("a");
                if (!$aTags->length) {
                    continue;
                }
                $a = $aTags->item(0);
            }
            if (!preg_match('/([0-9]+)\.htm.*/', $a->getAttribute("href"), $m)) {
                continue;
            }
            $ad->setLink($a->getAttribute("href"))
                ->setId($m[1]);
            foreach ($result->getElementsByTagName("div") AS $node) {
                if ($node->hasAttribute("class")) {
                    $class = $node->getAttribute("class");
                    if ($class == "date") {
                        $dateStr = preg_replace("#\s+#", " ", trim($node->nodeValue));
                        $aDate = explode(' ', $dateStr);
                        if (false !== strpos($dateStr, 'Aujourd')) {
                            $time = strtotime(date("Y-m-d")." 00:00:00");
                        } elseif (false !== strpos($dateStr, 'Hier')) {
                            $time = strtotime(date("Y-m-d")." 00:00:00");
                            $time = strtotime("-1 day", $time);
                        } else {
                            if (!isset($months[$aDate[1]])) {
                                continue;
                            }
                            $time = strtotime(date("Y")."-".$months[$aDate[1]]."-".$aDate[0]);
                        }
                        $aTime = explode(":", $aDate[count($aDate) - 1]);
                        $time += (int)$aTime[0] * 3600 + (int)$aTime[1] * 60;
                        if ($timeToday < $time) {
                            $time = strtotime("-1 year", $time);
                        }
                        $ad->setDate($time);
                    } elseif ($class == "title") {
                        $ad->setTitle(trim($node->nodeValue));
                    } elseif ($class == "image") {
                        $img = $node->getElementsByTagName("img");
                        if ($img->length > 0) {
                            $img = $img->item(0);
                            $ad->setThumbnailLink($img->getAttribute("src"));
                        }
                    } elseif ($class == "placement") {
                        $placement = $node->nodeValue;
                        if (false !== strpos($placement, "/")) {
                            $placement = explode("/", $placement);
                            $ad->setCounty(trim($placement[1]))
                                ->setCity(trim($placement[0]));
                        } else {
                            $ad->setCounty(trim($placement));
                        }
                    } elseif ($class == "category") {
                        $category = $node->nodeValue;
                        if (false !== strpos($category, "(pro)")) {
                            $ad->setProfessionnal(true);
                        }
                        $ad->setCategory(trim(str_replace("(pro)", "", $category)));
                    } elseif ($class == "price") {
                        if (preg_match("#[0-9 ]+#", $node->nodeValue, $m)) {
                            $ad->setPrice((int)str_replace(" ", "", trim($m[0])));
                        }
                    } elseif ($class == "urgent") {
                        $ad->setUrgent(true);
                    }
                }
            }
            if ($ad->getDate()) {
                $ads[$ad->getId()] = $ad;
            }
        }
        return $ads;
    }
}
















