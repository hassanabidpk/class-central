<?php
namespace ClassCentral\SiteBundle\Command\Network;

use ClassCentral\SiteBundle\Command\Network\NetworkAbstractInterface;
use ClassCentral\SiteBundle\Entity\Offering;

class RedditNetwork extends NetworkAbstractInterface
{
    public static $coursesByLevel = array(
        'beginner' => array(
            442,1586,1578,1325,1046,1481,320,1010,904,831,1580,303,375,640,590, 335, 1341, 441, 4891, 408, 1797, 1891, 2013, 1952, 2013,
            306, 529, 1983, 1850,1957, 2042,1821,2175,1650,732,553,1857,1727,
            615,1894, 1349, 2298, 1651,2195, 2486, 2659,2660, 2661, 2129, 2448, 1243,
            2630
        ),
        'intermediate' => array(
            824,599,616,1176,1470,1188,1585,1205,462,1178,339,1478,1479,1480,328,366,323,
            324,325,364,365,457,455,592, 551, 1299, 1701, 1523, 921, 846, 1457, 1742, 1282,
            650, 417, 594, 1187, 1737, 1738,1646, 1487,849,475, 1021, 835, 428,359,1152,487,1779,1816,1209,526,340, 724, 764,588,
            1748, 374, 1777,1875,531,1238,422,1529, 443, 1206, 1377, 1848, 1865 , 1788, 1881, 795,
            1948,533, 489, 1906, 451, 453, 426, 600, 2056, 1346, 558, 1724, 2109,2110,2111,
            2189,1715,1716,1717,1718,1719,1720,1712,1713,1714,1704,2211,458, 1806,2212,
            2215, 2335,2336, 2067,1053,1725,2214, 777, 1345,1347, 2038, 376, 2340, 342,
            1766, 2406, 745, 2360, 1240, 1348, 2427, 2658, 2503, 452, 2489, 548, 429, 2147, 1728, 2420, 2144,
            2716, 2244, 1730, 2861, 2445
        ),
        'advanced' => array(
            427,449,414,465,319,326,549,550, 552, 425, 1847,1848, 1849, 2018, 2458
        )
    );

    public static function getCourseToLevelMap()
    {
        $map = array();
        foreach(self::$coursesByLevel as $level => $courses)
        {
            foreach($courses as $course)
            {
                $map[$course] = $level;
            }
        }
        return $map;
    }

    public function outInitiative( $name , $offeringCount)
    {
        $this->output->writeln( strtoupper($name) . "({$offeringCount})");
        $this->output->writeln('');
    }

    public function beforeOffering()
    {
        // Table header row
        $this->output->writeln("Course Name|Start Date|Length|Provider|Rating");
        $this->output->writeln(":--|:--:|:--:|:--:|:--:");
    }


    public function outOffering(Offering $offering)
    {
        $rs = $this->container->get('review');

        $name = '[' . $offering->getName(). ']' . '(' . $offering->getUrl() . ')';

        if($offering->getInitiative() == null)
        {
            $initiative = 'Others';
        }
        else
        {
            $initiative = $offering->getInitiative()->getName();
        }

        $startDate = 'NA';
        if($offering->getStatus() == Offering::START_DATES_KNOWN)
        {
            $startDate = $offering->getStartDate()->format('M jS');
        }
        else if ( $offering->getStatus() == Offering::COURSE_OPEN)
        {
            $startDate = 'Self Paced';
        }

        $length = 'NA';
        if(  $offering->getCourse()->getLength() != 0)
        {
            $length = $offering->getCourse()->getLength() . ' weeks';
        }

        // Rating
        $courseRating = round($rs->getRatings($offering->getCourse()->getId()),1);
        $courseReviews = $rs->getReviewsArray( $offering->getCourse()->getId() );
        $reviewText = '';
        if($courseRating == 0)
        {
            $courseRating = 'NA';
        }
        else
        {
            $reviewText = sprintf("(%d %s)", $courseReviews['count'], ($courseReviews['count'] == 1 ) ? 'review' : 'reviews');
        }
        $url = 'https://www.class-central.com'. $this->router->generate('ClassCentralSiteBundle_mooc', array('id' => $offering->getCourse()->getId(), 'slug' => $offering->getCourse()->getSlug()));
        $url .= '#course-all-reviews';
        $rating = "[$courseRating]($url) $reviewText";

        $this->output->writeln("$name|$startDate|$length|$initiative|$rating");
    }

    }


