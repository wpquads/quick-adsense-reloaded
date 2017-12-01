<?php

namespace wpquads;

/**
 * Class Settings
 * @package quads
 */
class adSettings {

    /**
     * @var array
     */
    private $form = array();

    /**
     * Settings constructor.
     * @param Tabs $tabs
     */
    public function __construct() {

        $this->setAdForm(); 
    }

    /**
     * 
     */
    public function setAdForm() {
        
        $this->form = new Form();
                
        $this->vi = new vi();
        
        $settings = json_decode(json_encode(get_option("quads_vi_ads", array())));
        // Get the key width value 1 only as long as we do not have more than one (1) different vi ad
        $settings = isset($settings->ads->{'1'}) ? $settings->ads->{'1'} : new \stdClass();

        // Ad type
        $options = array('vi_stories' => 'vi_stories', 'outstream' => 'Outstream');
        $element = new Select(
                "quads_vi_ads[ads][1][type]", $options, array(
            "class" => "medium-text",
            "step" => 1,
            "max" => 999999,
            "min" => 0
                )
        );

        $this->form->add(
                $element->setLabel("Ad Unit*")->setDefault(isset($settings->type) ? $settings->type : 'vi_stories')
        );


        // Keywords
        $element = new Text('quads_vi_ads[ads][1][keywords]', array());
        $this->form->add(
                $element->setLabel("Keywords")->setTooltip("Comma separated values describing the content of the page e.g. 'cooking, grilling, pulled pork")->setDefault(isset($settings->keywords) ? $settings->keywords : '')
        );

        // iAB tier 1 category
        $iab_options = array(
            array("val0" => "select", "val1" => "Select tier 1 category"),
            array("val0" => "IAB1", "val1" => "Arts & Entertainment"),
            array("val0" => "IAB2", "val1" => "Automotive"),
            array("val0" => "IAB3", "val1" => "Business"),          
            array("val0" => "IAB4", "val1" => "Careers"),      
            array("val0" => "IAB5", "val1" => "Education"),
            array("val0" => "IAB6", "val1" => "Family & Parenting"),     
            array("val0" => "IAB7", "val1" => "Health & Fitness"),         
            array("val0" => "IAB8", "val1" => "Food & Drink"),           
            array("val0" => "IAB9", "val1" => "Hobbies & Interests"),         
            array("val0" => "IAB10", "val1" => "Home & Garden"),           
            array("val0" => "IAB11", "val1" => "Law, Gov’t & Politics"),           
            array("val0" => "IAB12", "val1" => "News"),           
            array("val0" => "IAB13", "val1" => "Personal Finance"),           
            array("val0" => "IAB14", "val1" => "Society"),           
            array("val0" => "IAB15", "val1" => "Science"),           
            array("val0" => "IAB16", "val1" => "Pets"),          
            array("val0" => "IAB17", "val1" => "Sports"),           
            array("val0" => "IAB18", "val1" => "Style & Fashion"),          
            array("val0" => "IAB19", "val1" => "Technology & Computing"),           
            array("val0" => "IAB20", "val1" => "Travel"),          
            array("val0" => "IAB21", "val1" => "Real Estate"),           
            array("val0" => "IAB22", "val1" => "Shopping"),          
            array("val0" => "IAB23", "val1" => "Religion & Spirituality"),          
            array("val0" => "IAB24", "val1" => "Uncategorized"),           
            array("val0" => "IAB26", "val1" => "Illegal Content"),
            
        );

        // Change the array to one nested level 
        $optionsNew = array();
        foreach ($iab_options as $key => $value) {
            $optionsNew[$value['val0']] = $value['val1'];
        }

        $element = new Select(
                "quads_vi_ads[ads][1][iab1]", $optionsNew, array(
            "class" => "large-text",
            "step" => 1,
            "max" => 999999,
            "min" => 0
                )
        );

        $this->form->add(
                $element->setLabel("IAB Category*")->setDefault(isset($settings->iab1) ? $settings->iab1 : 'select')
        );
        
        // iAB tier 2 category
        $iab2_options = array(
            array("val0" => "select", "val1" => "Select tier 2 category"),
            array("val0" => "IAB1-1", "val1" => "Books & Literature"),
            array("val0" => "IAB1-2", "val1" => "Celebrity Fan/Gossip"),
            array("val0" => "IAB1-3", "val1" => "Fine Art"),
            array("val0" => "IAB1-4", "val1" => "Humor"),
            array("val0" => "IAB1-5", "val1" => "Movies"),
            array("val0" => "IAB1-6", "val1" => "Music"),
            array("val0" => "IAB1-7", "val1" => "Television"),
            array("val0" => "IAB2-1", "val1" => "Auto Parts"),
            array("val0" => "IAB2-2", "val1" => "Auto Repair"),
            array("val0" => "IAB2-3", "val1" => "Buying/Selling Cars"),
            array("val0" => "IAB2-4", "val1" => "Car Culture"),
            array("val0" => "IAB2-5", "val1" => "Certified Pre-Owned"),
            array("val0" => "IAB2-6", "val1" => "Convertible"),
            array("val0" => "IAB2-7", "val1" => "Coupe"),
            array("val0" => "IAB2-8", "val1" => "Crossover"),
            array("val0" => "IAB2-9", "val1" => "Diesel"),
            array("val0" => "IAB2-10", "val1" => "Electric Vehicle"),
            array("val0" => "IAB2-11", "val1" => "Hatchback"),
            array("val0" => "IAB2-12", "val1" => "Hybrid"),
            array("val0" => "IAB2-13", "val1" => "Luxury"),
            array("val0" => "IAB2-14", "val1" => "MiniVan"),
            array("val0" => "IAB2-15", "val1" => "Mororcycles"),
            array("val0" => "IAB2-16", "val1" => "Off-Road Vehicles"),
            array("val0" => "IAB2-17", "val1" => "Performance Vehicles"),
            array("val0" => "IAB2-18", "val1" => "Pickup"),
            array("val0" => "IAB2-19", "val1" => "Road-Side Assistance"),
            array("val0" => "IAB2-20", "val1" => "Sedan"),
            array("val0" => "IAB2-21", "val1" => "Trucks & Accessories"),
            array("val0" => "IAB2-22", "val1" => "Vintage Cars"),
            array("val0" => "IAB2-23", "val1" => "Wagon"),
            array("val0" => "IAB3-1", "val1" => "Advertising"),
            array("val0" => "IAB3-2", "val1" => "Agriculture"),
            array("val0" => "IAB3-3", "val1" => "Biotech/Biomedical"),
            array("val0" => "IAB3-4", "val1" => "Business Software"),
            array("val0" => "IAB3-5", "val1" => "Construction"),
            array("val0" => "IAB3-6", "val1" => "Forestry"),
            array("val0" => "IAB3-7", "val1" => "Government"),
            array("val0" => "IAB3-8", "val1" => "Green Solutions"),
            array("val0" => "IAB3-9", "val1" => "Human Resources"),
            array("val0" => "IAB3-10", "val1" => "Logistics"),
            array("val0" => "IAB3-11", "val1" => "Marketing"),
            array("val0" => "IAB3-12", "val1" => "Metals"),
            array("val0" => "IAB4-1", "val1" => "Career Planning"),
            array("val0" => "IAB4-2", "val1" => "College"),
            array("val0" => "IAB4-3", "val1" => "Financial Aid"),
            array("val0" => "IAB4-4", "val1" => "Job Fairs"),
            array("val0" => "IAB4-5", "val1" => "Job Search"),
            array("val0" => "IAB4-6", "val1" => "Resume Writing/Advice"),
            array("val0" => "IAB4-7", "val1" => "Nursing"),
            array("val0" => "IAB4-8", "val1" => "Scholarships"),
            array("val0" => "IAB4-9", "val1" => "Telecommuting"),
            array("val0" => "IAB4-10", "val1" => "U.S. Military"),
            array("val0" => "IAB4-11", "val1" => "Career Advice"),
            array("val0" => "IAB5-1", "val1" => "7-12 Education"),
            array("val0" => "IAB5-2", "val1" => "Adult Education"),
            array("val0" => "IAB5-3", "val1" => "Art History"),
            array("val0" => "IAB5-4", "val1" => "Colledge Administration"),
            array("val0" => "IAB5-5", "val1" => "College Life"),
            array("val0" => "IAB5-6", "val1" => "Distance Learning"),
            array("val0" => "IAB5-7", "val1" => "English as a 2nd Language"),
            array("val0" => "IAB5-8", "val1" => "Language Learning"),
            array("val0" => "IAB5-9", "val1" => "Graduate School"),
            array("val0" => "IAB5-10", "val1" => "Homeschooling"),
            array("val0" => "IAB5-11", "val1" => "Homework/Study Tips"),
            array("val0" => "IAB5-12", "val1" => "K-6 Educators"),
            array("val0" => "IAB5-13", "val1" => "Private School"),
            array("val0" => "IAB5-14", "val1" => "Special Education"),
            array("val0" => "IAB5-15", "val1" => "Studying Business"),
            array("val0" => "IAB6-1", "val1" => "Adoption"),
            array("val0" => "IAB6-2", "val1" => "Babies & Toddlers"),
            array("val0" => "IAB6-3", "val1" => "Daycare/Pre School"),
            array("val0" => "IAB6-4", "val1" => "Family Internet"),
            array("val0" => "IAB6-5", "val1" => "Parenting – K-6 Kids"),
            array("val0" => "IAB6-6", "val1" => "Parenting teens"),
            array("val0" => "IAB6-7", "val1" => "Pregnancy"),
            array("val0" => "IAB6-8", "val1" => "Special Needs Kids"),
            array("val0" => "IAB6-9", "val1" => "Eldercare"),
            array("val0" => "IAB7-1", "val1" => "Exercise"),
            array("val0" => "IAB7-2", "val1" => "A.D.D."),
            array("val0" => "IAB7-3", "val1" => "AIDS/HIV"),
            array("val0" => "IAB7-4", "val1" => "Allergies"),
            array("val0" => "IAB7-5", "val1" => "Alternative Medicine"),
            array("val0" => "IAB7-6", "val1" => "Arthritis"),
            array("val0" => "IAB7-7", "val1" => "Asthma"),
            array("val0" => "IAB7-8", "val1" => "Autism/PDD"),
            array("val0" => "IAB7-9", "val1" => "Bipolar Disorder"),
            array("val0" => "IAB7-10", "val1" => "Brain Tumor"),
            array("val0" => "IAB7-11", "val1" => "Cancer"),
            array("val0" => "IAB7-12", "val1" => "Cholesterol"),
            array("val0" => "IAB7-13", "val1" => "Chronic Fatigue Syndrome"),
            array("val0" => "IAB7-14", "val1" => "Chronic Pain"),
            array("val0" => "IAB7-15", "val1" => "Cold & Flu"),
            array("val0" => "IAB7-16", "val1" => "Deafness"),
            array("val0" => "IAB7-17", "val1" => "Dental Care"),
            array("val0" => "IAB7-18", "val1" => "Depression"),
            array("val0" => "IAB7-19", "val1" => "Dermatology"),
            array("val0" => "IAB7-20", "val1" => "Diabetes"),
            array("val0" => "IAB7-21", "val1" => "Epilepsy"),
            array("val0" => "IAB7-22", "val1" => "GERD/Acid Reflux"),
            array("val0" => "IAB7-23", "val1" => "Headaches/Migraines"),
            array("val0" => "IAB7-24", "val1" => "Heart Disease"),
            array("val0" => "IAB7-25", "val1" => "Herbs for Health"),
            array("val0" => "IAB7-26", "val1" => "Holistic Healing"),
            array("val0" => "IAB7-27", "val1" => "IBS/Crohn’s Disease"),
            array("val0" => "IAB7-28", "val1" => "Incest/Abuse Support"),
            array("val0" => "IAB7-29", "val1" => "Incontinence"),
            array("val0" => "IAB7-30", "val1" => "Infertility"),
            array("val0" => "IAB7-31", "val1" => "Men’s Health"),
            array("val0" => "IAB7-32", "val1" => "Nutrition"),
            array("val0" => "IAB7-33", "val1" => "Orthopedics"),
            array("val0" => "IAB7-34", "val1" => "Panic/Anxiety Disorders"),
            array("val0" => "IAB7-35", "val1" => "Pediatrics"),
            array("val0" => "IAB7-36", "val1" => "Physical Therapy"),
            array("val0" => "IAB7-37", "val1" => "Psychology/Psychiatry"),
            array("val0" => "IAB7-38", "val1" => "Senor Health"),
            array("val0" => "IAB7-39", "val1" => "Sexuality"),
            array("val0" => "IAB7-40", "val1" => "Sleep Disorders"),
            array("val0" => "IAB7-41", "val1" => "Smoking Cessation"),
            array("val0" => "IAB7-42", "val1" => "Substance Abuse"),
            array("val0" => "IAB7-43", "val1" => "Thyroid Disease"),
            array("val0" => "IAB7-44", "val1" => "Weight Loss"),
            array("val0" => "IAB7-45", "val1" => "Women’s Health"),
            array("val0" => "IAB8-1", "val1" => "American Cuisine"),
            array("val0" => "IAB8-2", "val1" => "Barbecues & Grilling"),
            array("val0" => "IAB8-3", "val1" => "Cajun/Creole"),
            array("val0" => "IAB8-4", "val1" => "Chinese Cuisine"),
            array("val0" => "IAB8-5", "val1" => "Cocktails/Beer"),
            array("val0" => "IAB8-6", "val1" => "Coffee/Tea"),
            array("val0" => "IAB8-7", "val1" => "Cuisine-Specific"),
            array("val0" => "IAB8-8", "val1" => "Desserts & Baking"),
            array("val0" => "IAB8-9", "val1" => "Dining Out"),
            array("val0" => "IAB8-10", "val1" => "Food Allergies"),
            array("val0" => "IAB8-11", "val1" => "French Cuisine"),
            array("val0" => "IAB8-12", "val1" => "Health/Lowfat Cooking"),
            array("val0" => "IAB8-13", "val1" => "Italian Cuisine"),
            array("val0" => "IAB8-14", "val1" => "Japanese Cuisine"),
            array("val0" => "IAB8-15", "val1" => "Mexican Cuisine"),
            array("val0" => "IAB8-16", "val1" => "Vegan"),
            array("val0" => "IAB8-17", "val1" => "Vegetarian"),
            array("val0" => "IAB8-18", "val1" => "Wine"),
            array("val0" => "IAB9-1", "val1" => "Art/Technology"),
            array("val0" => "IAB9-2", "val1" => "Arts & Crafts"),
            array("val0" => "IAB9-3", "val1" => "Beadwork"),
            array("val0" => "IAB9-4", "val1" => "Birdwatching"),
            array("val0" => "IAB9-5", "val1" => "Board Games/Puzzles"),
            array("val0" => "IAB9-6", "val1" => "Candle & Soap Making"),
            array("val0" => "IAB9-7", "val1" => "Card Games"),
            array("val0" => "IAB9-8", "val1" => "Chess"),
            array("val0" => "IAB9-9", "val1" => "Cigars"),
            array("val0" => "IAB9-10", "val1" => "Collecting"),
            array("val0" => "IAB9-11", "val1" => "Comic Books"),
            array("val0" => "IAB9-12", "val1" => "Drawing/Sketching"),
            array("val0" => "IAB9-13", "val1" => "Freelance Writing"),
            array("val0" => "IAB9-14", "val1" => "Genealogy"),
            array("val0" => "IAB9-15", "val1" => "Getting Published"),
            array("val0" => "IAB9-16", "val1" => "Guitar"),
            array("val0" => "IAB9-17", "val1" => "Home Recording"),
            array("val0" => "IAB9-18", "val1" => "Investors & Patents"),
            array("val0" => "IAB9-19", "val1" => "Jewelry Making"),
            array("val0" => "IAB9-20", "val1" => "Magic & Illusion"),
            array("val0" => "IAB9-21", "val1" => "Needlework"),
            array("val0" => "IAB9-22", "val1" => "Painting"),
            array("val0" => "IAB9-23", "val1" => "Photography"),
            array("val0" => "IAB9-24", "val1" => "Radio"),
            array("val0" => "IAB9-25", "val1" => "Roleplaying Games"),
            array("val0" => "IAB9-26", "val1" => "Sci-Fi & Fantasy"),
            array("val0" => "IAB9-27", "val1" => "Scrapbooking"),
            array("val0" => "IAB9-28", "val1" => "Screenwriting"),
            array("val0" => "IAB9-29", "val1" => "Stamps & Coins"),
            array("val0" => "IAB9-30", "val1" => "Video & Computer Games"),
            array("val0" => "IAB9-31", "val1" => "Woodworking"),
            array("val0" => "IAB10-1", "val1" => "Appliances"),
            array("val0" => "IAB10-2", "val1" => "Entertaining"),
            array("val0" => "IAB10-3", "val1" => "Environmental Safety"),
            array("val0" => "IAB10-4", "val1" => "Gardening"),
            array("val0" => "IAB10-5", "val1" => "Home Repair"),
            array("val0" => "IAB10-6", "val1" => "Home Theater"),
            array("val0" => "IAB10-7", "val1" => "Interior Decorating"),
            array("val0" => "IAB10-8", "val1" => "Landscaping"),
            array("val0" => "IAB10-9", "val1" => "Remodeling & Construction"),
            array("val0" => "IAB11-1", "val1" => "Immigration"),
            array("val0" => "IAB11-2", "val1" => "Legal Issues"),
            array("val0" => "IAB11-3", "val1" => "U.S. Government Resources"),
            array("val0" => "IAB11-4", "val1" => "Politics"),
            array("val0" => "IAB11-5", "val1" => "Commentary"),
            array("val0" => "IAB12-1", "val1" => "International News"),
            array("val0" => "IAB12-2", "val1" => "National News"),
            array("val0" => "IAB12-3", "val1" => "Local News"),
            array("val0" => "IAB13-1", "val1" => "Beginning Investing"),
            array("val0" => "IAB13-2", "val1" => "Credit/Debt & Loans"),
            array("val0" => "IAB13-3", "val1" => "Financial News"),
            array("val0" => "IAB13-4", "val1" => "Financial Planning"),
            array("val0" => "IAB13-5", "val1" => "Hedge Fund"),
            array("val0" => "IAB13-6", "val1" => "Insurance"),
            array("val0" => "IAB13-7", "val1" => "Investing"),
            array("val0" => "IAB13-8", "val1" => "Mutual Funds"),
            array("val0" => "IAB13-9", "val1" => "Options"),
            array("val0" => "IAB13-10", "val1" => "Retirement Planning"),
            array("val0" => "IAB13-11", "val1" => "Stocks"),
            array("val0" => "IAB13-12", "val1" => "Tax Planning"),
            array("val0" => "IAB14-1", "val1" => "Dating"),
            array("val0" => "IAB14-2", "val1" => "Divorce Support"),
            array("val0" => "IAB14-3", "val1" => "Gay Life"),
            array("val0" => "IAB14-4", "val1" => "Marriage"),
            array("val0" => "IAB14-5", "val1" => "Senior Living"),
            array("val0" => "IAB14-6", "val1" => "Teens"),
            array("val0" => "IAB14-7", "val1" => "Weddings"),
            array("val0" => "IAB14-8", "val1" => "Ethnic Specific"),
            array("val0" => "IAB15-1", "val1" => "Astrology"),
            array("val0" => "IAB15-2", "val1" => "Biology"),
            array("val0" => "IAB15-3", "val1" => "Chemistry"),
            array("val0" => "IAB15-4", "val1" => "Geology"),
            array("val0" => "IAB15-5", "val1" => "Paranormal Phenomena"),
            array("val0" => "IAB15-6", "val1" => "Physics"),
            array("val0" => "IAB15-7", "val1" => "Space/Astronomy"),
            array("val0" => "IAB15-8", "val1" => "Geography"),
            array("val0" => "IAB15-9", "val1" => "Botany"),
            array("val0" => "IAB15-10", "val1" => "Weather"),
            array("val0" => "IAB16-1", "val1" => "Aquariums"),
            array("val0" => "IAB16-2", "val1" => "Birds"),
            array("val0" => "IAB16-3", "val1" => "Cats"),
            array("val0" => "IAB16-4", "val1" => "Dogs"),
            array("val0" => "IAB16-5", "val1" => "Large Animals"),
            array("val0" => "IAB16-6", "val1" => "Reptiles"),
            array("val0" => "IAB16-7", "val1" => "Veterinary Medicine"),
            array("val0" => "IAB17-1", "val1" => "Auto Racing"),
            array("val0" => "IAB17-2", "val1" => "Baseball"),
            array("val0" => "IAB17-3", "val1" => "Bicycling"),
            array("val0" => "IAB17-4", "val1" => "Bodybuilding"),
            array("val0" => "IAB17-5", "val1" => "Boxing"),
            array("val0" => "IAB17-6", "val1" => "Canoeing/Kayaking"),
            array("val0" => "IAB17-7", "val1" => "Cheerleading"),
            array("val0" => "IAB17-8", "val1" => "Climbing"),
            array("val0" => "IAB17-9", "val1" => "Cricket"),
            array("val0" => "IAB17-10", "val1" => "Figure Skating"),
            array("val0" => "IAB17-11", "val1" => "Fly Fishing"),
            array("val0" => "IAB17-12", "val1" => "Football"),
            array("val0" => "IAB17-13", "val1" => "Freshwater Fishing"),
            array("val0" => "IAB17-14", "val1" => "Game & Fish"),
            array("val0" => "IAB17-15", "val1" => "Golf"),
            array("val0" => "IAB17-16", "val1" => "Horse Racing"),
            array("val0" => "IAB17-17", "val1" => "Horses"),
            array("val0" => "IAB17-18", "val1" => "Hunting/Shooting"),
            array("val0" => "IAB17-19", "val1" => "Inline Skating"),
            array("val0" => "IAB17-20", "val1" => "Martial Arts"),
            array("val0" => "IAB17-21", "val1" => "Mountain Biking"),
            array("val0" => "IAB17-22", "val1" => "NASCAR Racing"),
            array("val0" => "IAB17-23", "val1" => "Olympics"),
            array("val0" => "IAB17-24", "val1" => "Paintball"),
            array("val0" => "IAB17-25", "val1" => "Power & Motorcycles"),
            array("val0" => "IAB17-26", "val1" => "Pro Basketball"),
            array("val0" => "IAB17-27", "val1" => "Pro Ice Hockey"),
            array("val0" => "IAB17-28", "val1" => "Rodeo"),
            array("val0" => "IAB17-29", "val1" => "Rugby"),
            array("val0" => "IAB17-30", "val1" => "Running/Jogging"),
            array("val0" => "IAB17-31", "val1" => "Sailing"),
            array("val0" => "IAB17-32", "val1" => "Saltwater Fishing"),
            array("val0" => "IAB17-33", "val1" => "Scuba Diving"),
            array("val0" => "IAB17-34", "val1" => "Skateboarding"),
            array("val0" => "IAB17-35", "val1" => "Skiing"),
            array("val0" => "IAB17-36", "val1" => "Snowboarding"),
            array("val0" => "IAB17-37", "val1" => "Surfing/Bodyboarding"),
            array("val0" => "IAB17-38", "val1" => "Swimming"),
            array("val0" => "IAB17-39", "val1" => "Table Tennis/Ping-Pong"),
            array("val0" => "IAB17-40", "val1" => "Tennis"),
            array("val0" => "IAB17-41", "val1" => "Volleyball"),
            array("val0" => "IAB17-42", "val1" => "Walking"),
            array("val0" => "IAB17-43", "val1" => "Waterski/Wakeboard"),
            array("val0" => "IAB17-44", "val1" => "World Soccer"),
            array("val0" => "IAB18-1", "val1" => "Beauty"),
            array("val0" => "IAB18-2", "val1" => "Body Art"),
            array("val0" => "IAB18-3", "val1" => "Fashion"),
            array("val0" => "IAB18-4", "val1" => "Jewelry"),
            array("val0" => "IAB18-5", "val1" => "Clothing"),
            array("val0" => "IAB18-6", "val1" => "Accessories"),
            array("val0" => "IAB19-1", "val1" => "3-D Graphics"),
            array("val0" => "IAB19-2", "val1" => "Animation"),
            array("val0" => "IAB19-3", "val1" => "Antivirus Software"),
            array("val0" => "IAB19-4", "val1" => "C/C++"),
            array("val0" => "IAB19-5", "val1" => "Cameras & Camcorders"),
            array("val0" => "IAB19-6", "val1" => "Cell Phones"),
            array("val0" => "IAB19-7", "val1" => "Computer Certification"),
            array("val0" => "IAB19-8", "val1" => "Computer Networking"),
            array("val0" => "IAB19-9", "val1" => "Computer Peripherals"),
            array("val0" => "IAB19-10", "val1" => "Computer Reviews"),
            array("val0" => "IAB19-11", "val1" => "Data Centers"),
            array("val0" => "IAB19-12", "val1" => "Databases"),
            array("val0" => "IAB19-13", "val1" => "Desktop Publishing"),
            array("val0" => "IAB19-14", "val1" => "Desktop Video"),
            array("val0" => "IAB19-15", "val1" => "Email"),
            array("val0" => "IAB19-16", "val1" => "Graphics Software"),
            array("val0" => "IAB19-17", "val1" => "Home Video/DVD"),
            array("val0" => "IAB19-18", "val1" => "Internet Technology"),
            array("val0" => "IAB19-19", "val1" => "Java"),
            array("val0" => "IAB19-20", "val1" => "JavaScript"),
            array("val0" => "IAB19-21", "val1" => "Mac Support"),
            array("val0" => "IAB19-22", "val1" => "MP3/MIDI"),
            array("val0" => "IAB19-23", "val1" => "Net Conferencing"),
            array("val0" => "IAB19-24", "val1" => "Net for Beginners"),
            array("val0" => "IAB19-25", "val1" => "Network Security"),
            array("val0" => "IAB19-26", "val1" => "Palmtops/PDAs"),
            array("val0" => "IAB19-27", "val1" => "PC Support"),
            array("val0" => "IAB19-28", "val1" => "Portable"),
            array("val0" => "IAB19-29", "val1" => "Entertainment"),
            array("val0" => "IAB19-30", "val1" => "Shareware/Freeware"),
            array("val0" => "IAB19-31", "val1" => "Unix"),
            array("val0" => "IAB19-32", "val1" => "Visual Basic"),
            array("val0" => "IAB19-33", "val1" => "Web Clip Art"),
            array("val0" => "IAB19-34", "val1" => "Web Design/HTML"),
            array("val0" => "IAB19-35", "val1" => "Web Search"),
            array("val0" => "IAB19-36", "val1" => "Windows"),
            array("val0" => "IAB20-1", "val1" => "Adventure Travel"),
            array("val0" => "IAB20-2", "val1" => "Africa"),
            array("val0" => "IAB20-3", "val1" => "Air Travel"),
            array("val0" => "IAB20-4", "val1" => "Australia & New Zealand"),
            array("val0" => "IAB20-5", "val1" => "Bed & Breakfasts"),
            array("val0" => "IAB20-6", "val1" => "Budget Travel"),
            array("val0" => "IAB20-7", "val1" => "Business Travel"),
            array("val0" => "IAB20-8", "val1" => "By US Locale"),
            array("val0" => "IAB20-9", "val1" => "Camping"),
            array("val0" => "IAB20-10", "val1" => "Canada"),
            array("val0" => "IAB20-11", "val1" => "Caribbean"),
            array("val0" => "IAB20-12", "val1" => "Cruises"),
            array("val0" => "IAB20-13", "val1" => "Eastern Europe"),
            array("val0" => "IAB20-14", "val1" => "Europe"),
            array("val0" => "IAB20-15", "val1" => "France"),
            array("val0" => "IAB20-16", "val1" => "Greece"),
            array("val0" => "IAB20-17", "val1" => "Honeymoons/Getaways"),
            array("val0" => "IAB20-18", "val1" => "Hotels"),
            array("val0" => "IAB20-19", "val1" => "Italy"),
            array("val0" => "IAB20-20", "val1" => "Japan"),
            array("val0" => "IAB20-21", "val1" => "Mexico & Central America"),
            array("val0" => "IAB20-22", "val1" => "National Parks"),
            array("val0" => "IAB20-23", "val1" => "South America"),
            array("val0" => "IAB20-24", "val1" => "Spas"),
            array("val0" => "IAB20-25", "val1" => "Theme Parks"),
            array("val0" => "IAB20-26", "val1" => "Traveling with Kids"),
            array("val0" => "IAB20-27", "val1" => "United Kingdom"),
            array("val0" => "IAB21-1", "val1" => "Apartments"),
            array("val0" => "IAB21-2", "val1" => "Architects"),
            array("val0" => "IAB21-3", "val1" => "Buying/Selling Homes"),
            array("val0" => "IAB22-1", "val1" => "Contests & Freebies"),
            array("val0" => "IAB22-2", "val1" => "Couponing"),
            array("val0" => "IAB22-3", "val1" => "Comparison"),
            array("val0" => "IAB22-4", "val1" => "Engines"),
            array("val0" => "IAB23-1", "val1" => "Alternative Religions"),
            array("val0" => "IAB23-2", "val1" => "Atheism/Agnosticism"),
            array("val0" => "IAB23-3", "val1" => "Buddhism"),
            array("val0" => "IAB23-4", "val1" => "Catholicism"),
            array("val0" => "IAB23-5", "val1" => "Christianity"),
            array("val0" => "IAB23-6", "val1" => "Hinduism"),
            array("val0" => "IAB23-7", "val1" => "Islam"),
            array("val0" => "IAB23-8", "val1" => "Judaism"),
            array("val0" => "IAB23-9", "val1" => "Latter-Day Saints"),
            array("val0" => "IAB23-10", "val1" => "Pagan/Wiccan"),
            array("val0" => "IAB25", "val1" => "Non-Standard Content"),
            array("val0" => "IAB25-1", "val1" => "Unmoderated UGC"),
            array("val0" => "IAB25-2", "val1" => "Extreme Graphic/Explicit Violence"),
            array("val0" => "IAB25-3", "val1" => "Pornography"),
            array("val0" => "IAB25-4", "val1" => "Profane Content"),
            array("val0" => "IAB25-5", "val1" => "Hate Content"),
            array("val0" => "IAB25-6", "val1" => "Under Construction"),
            array("val0" => "IAB25-7", "val1" => "Incentivized"),
            array("val0" => "IAB26-1", "val1" => "Illegal Content"),
            array("val0" => "IAB26-2", "val1" => "Warez"),
            array("val0" => "IAB26-3", "val1" => "Spyware/Malware"),
            array("val0" => "IAB26-4", "val1" => "Copyright Infringement")
        );

        // Change the array to one nested level 
        $iab2New = array();
        foreach ($iab2_options as $key => $value) {
            $iab2New[$value['val0']] = $value['val1'];
        }

        $element = new Select(
                "quads_vi_ads[ads][1][iab2]", $iab2New, array(
            "class" => "large-text"
                )
        );

        $this->form->add(
                $element->setLabel("IAB Category Tier2 *")->setDefault(isset($settings->iab2) ? $settings->iab2 : 'select')
        );
        
        $options = $this->vi->getLanguages();

        // language
        $element = new Select('quads_vi_ads[ads][1][language]', $options);
        $this->form->add(
                $element->setLabel("Language")->setDefault(isset($settings->language) ? $settings->language : '')
        );

        
        // bg_color
        $element = new Text('quads_vi_ads[ads][1][bg_color]', array("class" => "jscolor"), array());
        $this->form->add(
                $element->setLabel("Background Color")->setDefault(isset($settings->bg_color) ? $settings->bg_color : 'fafafa')
        );
        // text_color
        $element = new Text('quads_vi_ads[ads][1][text_color]', array("class" => "jscolor"), array());
        $this->form->add(
                $element->setLabel("Text Color")->setDefault(isset($settings->text_color) ? $settings->text_color : '000000')
        );
        // txt_font_family
        $element = new Select('quads_vi_ads[ads][1][txt_font_family]', $this->vi->getFontFamily(), array() );
        $this->form->add(
                $element->setLabel("Text Font Family")->setDefault(isset($settings->txt_font_family) ? $settings->txt_font_family : 'Verdana')
        );
        // font_size
//        $element = new Numerical('quads_vi_ads[ads][1][font_size]', array());
//        $this->form->add(
//                $element->setLabel("Text Font Size")->setDefault(!empty($settings->font_size) ? $settings->font_size : '')
//        );
        // font_size
        $element = new Select('quads_vi_ads[ads][1][font_size]', array(
            '8' => '8', 
            '9' => '9', 
            '10' => '10', 
            '11' => '11',
            '12' => '12',
            '13' => '13',
            '14' => '14',
            '15' => '15',
            '16' => '16',
            '17' => '17',
            '18' => '18',
            '19' => '19',
            '20' => '20',
            '21' => '21',
            '22' => '22',
            '23' => '23',
            '24' => '24',
            '25' => '25',
            '26' => '26',
            '27' => '27',
            '28' => '28',
            '29' => '29',
            '30' => '30',
            '31' => '31',
            '32' => '32',
            '33' => '33',
            '34' => '34',
            '35' => '35',
            '36' => '36',
            ));
        $this->form->add(
                $element->setLabel("Text Font Size")->setDefault(!empty($settings->font_size) ? $settings->font_size : '')
        );
        // optional1
        $element = new Text('quads_vi_ads[ads][1][optional1]', array());
        $this->form->add(
                $element->setLabel("Optional 1")->setDefault(isset($settings->optional1) ? $settings->optional1 : '')
        );
        // optional3
        $element = new Text('quads_vi_ads[ads][1][optional2]', array());
        $this->form->add(
                $element->setLabel("Optional 2")->setDefault(isset($settings->optional2) ? $settings->optional2 : '')
        );
        // optional3
        $element = new Text('quads_vi_ads[ads][1][optional3]', array());
        $this->form->add(
                $element->setLabel("Optional 3")->setDefault(isset($settings->optional3) ? $settings->optional3 : '')
        );
        
        // ad code
        $element = new TextAreaHidden('quads_vi_ads[ads][1][code]', array());
        $this->form->add(
                $element->setLabel("Ad Code")->setDefault(isset($settings->code) ? $settings->code : '')
        );
        
        
        
        // Layout
        $element = new Radio('quads_vi_ads[ads][1][align]', array('default' => 'default', 'left' => 'left', 'middle' => 'middle', 'right' => 'right'));
        $this->form->add(
                $element->setLabel("align")->setDefault(isset($settings->align) ? $settings->align : '')
        );
      
        // Margin Top
        $element = new Numerical('quads_vi_ads[ads][1][marginTop]', array());
        $this->form->add(
                $element->setLabel("Top")->setDefault(isset($settings->marginTop) ? $settings->marginTop : '0')
        );
        // Margin Right
        $element = new Numerical('quads_vi_ads[ads][1][marginRight]', array());
        $this->form->add(
                $element->setLabel("Top")->setDefault(isset($settings->marginRight) ? $settings->marginRight : '0')
        );
        // Margin Bottom
        $element = new Numerical('quads_vi_ads[ads][1][marginBottom]', array());
        $this->form->add(
                $element->setLabel("Top")->setDefault(isset($settings->marginBottom) ? $settings->marginBottom : '0')
        );
        // Margin Left
        $element = new Numerical('quads_vi_ads[ads][1][marginLeft]', array());
        $this->form->add(
                $element->setLabel("Top")->setTooltip('Create some distance between the video content and text elements on your page. Values around 10 and more are looking nice but this also depends on your theme and what you personally prefer;)')->setDefault(isset($settings->marginLeft) ? $settings->marginLeft : '0')
        );
        
        // Position
        $options = array(
            'notShown' => 'No Automatic',
            'abovePost' => 'Above Content',
            'middlePost' => 'Middle of Content',
            'belowPost' => 'Below of Content'
            );
                
        $element = new Select('quads_vi_ads[ads][1][position]', $options);
        $this->form->add(
                $element->setLabel("Position")->setDefault(isset($settings->position) ? $settings->position : 'abovePost')
        );
        
        // Condition Post Types
        $options = array_merge (array('noPostTypes' => 'Exclude nothing'), get_post_types());
                            
        $element = new SelectMultiple('quads_vi_ads[ads][1][excludedPostTypes]', $options);
        $this->form->add(
                $element->setLabel("Exclude Post Types")->
                setTooltip("Exclude ads from beeing shown on specific post types by selecting post type user_roles. Select multiple values by holding down ctrl key.")->
                setDefault(isset($settings->excludedPostTypes) ? $settings->excludedPostTypes : 'noPostTypes')
        );
        
        // Condition Extra Pages
        $options = array_merge (array('noExtraPages' => 'Exclude nothing'), array('homePage' => 'Home Page'));
                            
        $element = new SelectMultiple('quads_vi_ads[ads][1][excludedExtraPages]', $options);
        $this->form->add(
                $element->setLabel("Exclude Extra Pages")->
                setTooltip("Never show vi video ad on these extra pages. Select multiple values by holding down ctrl key.")->
                setDefault(isset($settings->excludedExtraPages) ? $settings->excludedExtraPages : 'noExtraPages')
        );
        
        // Hide Ads On Posts
        $element = new Text('quads_vi_ads[ads][1][excludedPostIds]', array());
        $this->form->add(
                $element->setLabel("Exclude Posts")->
                setTooltip("Exclude ads from beeing shown on specific pages by adding comma separated post ids here.")->
                setDefault(isset($settings->excludedPostIds) ? $settings->excludedPostIds : '')
        );

        
         // Condition User Roles
        $options = array_merge (array('noUserRoles' => 'Exclude nothing'), $this->quads_get_user_roles());                  
        $element = new SelectMultiple('quads_vi_ads[ads][1][excludedUserRoles]', $options);
        $this->form->add(
                $element->setLabel("Exclude User Roles ")->
                setTooltip("Show ads on specific user_roles only by selecting coresponding user_roles. Select multiple values by holding down ctrl key.")->
                setDefault(isset($settings->excludedUserRoles) ? $settings->excludedUserRoles : 'allUserRoles')
        );
           
    }

    /**
     * @param string $name
     * @return array|Form
     */
    public function get($name = null) {
        return (null === $name) ? $this->form : $this->form;
    }
    
    
    /**
     * 
     * Get all user roles
     * 
     * @global array $wp_roles
     * @return array
     */
    private function quads_get_user_roles() {
        global $wp_roles;
        $roles = array();

        foreach ($wp_roles->roles as $role) {
            //if( isset( $role["capabilities"]["edit_posts"] ) && $role["capabilities"]["edit_posts"] === true ) {
            $value = str_replace(' ', null, strtolower($role["name"]));
            $roles[$value] = $role["name"];
            //}
        }
        return $roles;
    }

}
