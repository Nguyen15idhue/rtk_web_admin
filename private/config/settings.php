<?php
// General application settings

define('DEFAULT_ITEMS_PER_PAGE', 10);

// Station coordinate offset settings
// Set to true to apply ~2km offset to station coordinates when saving to database
// Set to false to use original coordinates from API
define('ENABLE_COORDINATE_OFFSET', true);
define('COORDINATE_OFFSET_DISTANCE_KM', 2.0);
