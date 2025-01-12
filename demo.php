<?php

// Sample campaign array
$campaigns = [
    [
        'id' => '1',
        'name' => '"Test_Banner_13th-31st_march_Developer',
        'advertiser' => 'TechCo',
        'bid_price' => 2.5,
        'geotargeting' => ['US', 'CA', 'BGD'],
        'device_targeting' => ['mobile', 'tablet'],
        'ad_format' => 'banner',
        'supported_dimensions' => [
            ['w' => 320, 'h' => 50],
            ['w' => 776, 'h' => 393],
            ['w' => 667, 'h' => 375]
        ],
        'creative' => [
            'type' => 'image',
            'image_url' => 'https://example.com/tech-banner.jpg',
            'landing_page_url' => 'https://example.com/landing'
        ]
    ],
    [
        'id' => '2',
        'name' => 'Fitness Gear Campaign',
        'advertiser' => 'FitCo',
        'bid_price' => 3.0,
        'geotargeting' => ['US'],
        'device_targeting' => ['desktop'],
        'ad_format' => 'banner',
        'supported_dimensions' => [
            ['w' => 750, 'h' => 200],
            ['w' => 400, 'h' => 300]
        ],
        'creative' => [
            'type' => 'image',
            'image_url' => 'https://s3-ap-southeast-1.amazonaws.com/elasticbeanstalk-ap-southeast-1-5410920200615/CampaignFile/20240117030213/D300x250/e63324c6f222208f1dc66d3e2daaaf06.png',
            'landing_page_url' => 'https://example.com/fitness-landing'
        ]
    ]
];

// Function to handle bid requests
function handleBidRequest($bidRequestJson, $campaigns) {
    // Parse bid request JSON
    $bidRequest = json_decode($bidRequestJson, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return json_encode(['error' => 'Invalid JSON format']);
    }

    // Validate bid request
    if (!isset($bidRequest['imp'], $bidRequest['device'], $bidRequest['device']['geo'], $bidRequest['imp'][0]['bidfloor'])) {
        return json_encode(['error' => 'Missing required bid request parameters']);
    }

    $device = $bidRequest['device'];
    $geo = $device['geo']['country'];
    $imp = $bidRequest['imp'][0];
    $adFormat = 'banner';
    $bidFloor = $imp['bidfloor'];
    $banner = $imp['banner'];

    // Filter eligible campaigns
    $eligibleCampaigns = array_filter($campaigns, function ($campaign) use ($device, $geo, $adFormat, $bidFloor, $banner) {
        $dimensionMatch = array_filter($campaign['supported_dimensions'], function ($dim) use ($banner) {
            return $dim['w'] == $banner['w'] && $dim['h'] == $banner['h'];
        });

        return in_array($geo, $campaign['geotargeting']) &&
               in_array($device['devicetype'] == 4 ? 'mobile' : 'desktop', $campaign['device_targeting']) &&
               $campaign['ad_format'] === $adFormat &&
               $campaign['bid_price'] >= $bidFloor &&
               !empty($dimensionMatch);
    });

    if (empty($eligibleCampaigns)) {
        return json_encode(['error' => 'No eligible campaigns found']);
    }

    // Select the highest bid campaign
    usort($eligibleCampaigns, function ($a, $b) {
        return $b['bid_price'] <=> $a['bid_price'];
    });

    $selectedCampaign = $eligibleCampaigns[0];

    // Generate response
    $response = [
        [
            "campaignname" => $selectedCampaign['name'],
            "advertiser" => $selectedCampaign['advertiser'],
            "code" => uniqid(),
            "appid" => uniqid(),
            "tld" => $selectedCampaign['creative']['landing_page_url'],
            "portalname" => "",
            "creative_type" => 1,
            "creative_id" => uniqid(),
            "day_capping" => 0,
            "dimension" => $banner['w'] . "x" . $banner['h'],
            "attribute" => "rich-media",
            "url" => $selectedCampaign['creative']['landing_page_url'],
            "billing_id" => "123456789",
            "price" => $selectedCampaign['bid_price'],
            "bidtype" => "CPM",
            "image_url" => $selectedCampaign['creative']['image_url'],
            "htmltag" => "",
            "from_hour" => "0",
            "to_hour" => "23",
            "hs_os" => "Android,iOS,Desktop",
            "operator" => "Banglalink,GrameenPhone,Robi,Teletalk,Airtel,Wi-Fi",
            "device_make" => "No Filter",
            "country" => $geo,
            "city" => "",
            "lat" => "",
            "lng" => "",
            "app_name" => null,
            "user_list_id" => "0",
            "adplay_logo" => 1,
            "vast_video_duration" => null,
            "logo_placement" => 1,
            "hs_model" => null,
            "is_rewarded_inventory" => 0,
            "pixel_tag" => null,
            "dmp_campaign_audience" => 0,
            "platform" => null,
            "open_publisher" => 1,
            "audience_targeting" => 0,
            "native_title" => null,
            "native_type" => null,
            "native_data_value" => null,
            "native_data_cta" => null,
            "native_data_rating" => null,
            "native_data_price" => null,
            "native_img_icon" => null
        ]
    ];

    return json_encode($response);
}

// Example bid request
$bidRequestJson = '{
    "id": "myB92gUhMdC5DUxndq3yAg",
    "imp": [
        {
            "id": "1",
            "banner": {
                "w": 320,
                "h": 50,
                "pos": 1,
                "api": [3, 5, 6, 7],
                "format": [
                    {"w": 776, "h": 393},
                    {"w": 667, "h": 375},
                    {"w": 640, "h": 360},
                    {"w": 592, "h": 360},
                    {"w": 568, "h": 320},
                    {"w": 320, "h": 480},
                    {"w": 750, "h": 200},
                    {"w": 400, "h": 300}
                ]
            },
            "displaymanager": "GOOGLE",
            "instl": 1,
            "tagid": "3167273236690230250",
            "bidfloor": 0.01,
            "bidfloorcur": "USD",
            "secure": 1,
            "exp": 3600,
            "metric": [
                {"type": "click_through_rate", "value": 0.19889350235462189, "vendor": "EXCHANGE"},
                {"type": "viewability", "value": 0.97999999999999998, "vendor": "EXCHANGE"}
            ],
            "ext": {
                "billing_id": ["123456789", "152349838468"],
                "publisher_settings_list_id": ["10210479292634817089", "14735124967324597266"],
                "allowed_vendor_type": [785, 767, 144],
                "ampad": 2,
                "creative_enforcement_settings": {
                    "policy_enforcement": 2,
                    "scan_enforcement": 1,
                    "publisher_blocks_enforcement": 1
                },
                "auction_environment": 0
            }
        }
    ],
    "app": {
        "name": "com.ludo.king",
        "bundle": "com.ludo.king",
        "publisher": {
            "id": "pub-5742233882270312",
            "ext": {"country": "GB"}
        },
        "content": {
            "url": "https://play.google.com/store/apps/details?id=com.firsttouchgames.dls7",
            "userrating": "4.3",
            "livestream": 0,
            "language": "en"
        },
        "storeurl": "https://play.google.com/store/apps/details?id=com.firsttouchgames.dls7",
        "ext": {"inventorypartnerdomain": ""}
    },
    "device": {
        "ua": "Mozilla/5.0 (Linux; Android 11; M2004J19C Build/RP1A.200720.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/107.0.5304.105 Mobile Safari/537.36",
        "ip": "103.92.152.0",
        "geo": {
            "lat": 23.774545669555664,
            "lon": 90.440811157226562,
            "country": "BGD",
            "city": "Dhaka",
            "zip": "1212"
        },
        "make": "xiaomi",
        "model": "m2004j19c",
        "os": "android",
        "osv": "11",
        "devicetype": 4,
        "ifa": "90637fa5-79c8-4a22-97bd-0c8c7e853f16",
        "lmt": 0,
        "w": 776,
        "h": 393,
        "pxratio": 2.75,
        "ext": {
            "user_agent_data": {
                "platform": {"brand": "Android", "version": ["11"]},
                "mobile": 1,
                "model": "M2004J19C",
                "browsers": [
                    {"brand": "Mozilla", "version": ["5", "0"]},
                    {"brand": "AppleWebKit", "version": ["537", "36"]},
                    {"brand": "Version", "version": ["4", "0"]},
                    {"brand": "Chrome", "version": ["107", "0", "5304", "105"]},
                    {"brand": "Mobile Safari", "version": ["537", "36"]}
                ]
            }
        }
    },
    "user": {
        "id": "CAESEK7QRNHvCqCtWwFtkJjMQVU",
        "ext": {}
    },
    "at": 1,
    "tmax": 1000,
    "cur": ["USD"],
    "bcat": ["IAB1-2"],
    "source": {
        "ext": {
            "omidpn": "Google",
            "omidpv": "afma-sdk-a-v223712999.222508000.1",
            "schain": {
                "complete": 1,
                "nodes": [{"asi": "google.com", "sid": "pub-5742233882270312", "hp": 1}],
                "ver": "1.0"
            }
        }
    },
    "ext": {
        "google_query_id": "AA8e6VI-G-6-PHEjFD9KLYFQqH6v_SGtU6wcSv_E4yC7YgDuY37SAecQnCz_PggyO4x3-KIFQA",
        "fcap_scope": 3,
        "privacy_treatments": {"allow_user_data_collection": 1}
    }
}';

$response = handleBidRequest($bidRequestJson, $campaigns);

// Output the response
header('Content-Type: application/json');
echo $response;

?>
