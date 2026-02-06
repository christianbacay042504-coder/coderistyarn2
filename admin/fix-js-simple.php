<?php
// Simple fix for SJDM-User JavaScript
$scriptPath = __DIR__ . '/../sjdm-user/script.js';
$content = file_get_contents($scriptPath);

// Remove the duplicate init function at lines 172-180
$content = str_replace("// Add to your existing init function\nfunction init() {\n    // ... existing code ...\n\n    // Initialize hotel filters when hotels page is shown\n    if (window.location.hash === '#hotels' || document.getElementById('hotels')?.classList.contains('active')) {\n        setTimeout(initHotelFilters, 100);\n    }\n}", "", $content);

// Remove duplicate window.addEventListener at the end
$content = str_replace("// Initialize on page load\nwindow.addEventListener('DOMContentLoaded', init);", "", $content);

// Remove duplicate initUserProfileDropdown call at the end
$content = str_replace("// Initialize when library loads or DOM ready\nif (document.readyState === 'loading') {\n    document.addEventListener('DOMContentLoaded', initUserProfileDropdown);\n} else {\n    initUserProfileDropdown();\n}", "", $content);

file_put_contents($scriptPath, $content);
echo "Fixed JavaScript file!";
?>
