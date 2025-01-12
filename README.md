# Rtb-Campaign-Handler
For AdPlay Technology
I took a dummy campaign and First I Validated the Response
array_filter($campaigns, function ($campaign) {...}):
Filters the $campaigns array to include only the campaigns that meet certain criteria specified in the callback function.
$dimensionMatch = array_filter()
Check if any of the dimensions (w, h) in the supported_dimensions array of the campaign matches the banner dimensions (w, h) from the bid request.
 Compares the width and height of the campaign dimensions to the requested banner dimensions.
 

Checks if the device type matches the campaign's targeting:
Device type 4 is interpreted as a "mobile" device.
Any other value defaults to "desktop."
Verify whether this matches the campaign device_targeting list.

Ensures the campaign's ad_format matches the requested ad format (banner).
