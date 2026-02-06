// Fix SJDM-User JavaScript Issues
// This script will fix the duplicate init functions and ensure profile dropdown works

// Read the current script file and fix issues
$scriptPath = __DIR__ . '/../sjdm-user/script.js';
$scriptContent = file_get_contents($scriptPath);

// Remove duplicate init functions
$scriptContent = preg_replace('/\/\/ Add to your existing init function\s*function init\(\)\s*\{\s*\/\/ \.\.\. existing code \.\.\.\s*\/\/ Initialize hotel filters when hotels page is shown\s*if \(window\.location\.hash === [\'"]#hotels[\'"]\|\|\| document\.getElementById\([\'"]hotels[\'"]\)\?\.classList\.contains\([\'"]active[\'"]\)\)\s*\{\s*setTimeout\(initHotelFilters, 100\);\s*\}\s*\}\s*\n?/', '', $scriptContent);

// Remove the third duplicate init function
$scriptContent = preg_replace('/\/\/ Add to your existing init function in script\.js\s*function init\(\)\s*\{\s*\/\/ \.\.\. existing init code \.\.\.\s*\/\/ Initialize travel tips page if we\'re on that page\s*.*?\}\s*\n?/', '', $scriptContent);

// Remove duplicate DOMContentLoaded listeners
$scriptContent = preg_replace('/\/\/ Call this when DOM is loaded\s*document\.addEventListener\([\'"]DOMContentLoaded[\'"], function \(\)\s*\{\s*\/\/ \.\.\. existing code \.\.\.\s*\/\/ Initialize travel tips if on that page\s*.*?\}\);?\s*\n?/', '', $scriptContent);

// Remove duplicate window.addEventListener
$scriptContent = preg_replace('/\/\/ Initialize on page load\s*window\.addEventListener\([\'"]DOMContentLoaded[\'"], init\);\s*\n?/', '', $scriptContent);

// Remove the duplicate initUserProfileDropdown call at the end
$scriptContent = preg_replace('/\/\/ Initialize when library loads or DOM ready\s*if \(document\.readyState === [\'"]loading[\'"]\)\s*\{\s*document\.addEventListener\([\'"]DOMContentLoaded[\'"], initUserProfileDropdown\);\s*\}\s*else\s*\{\s*initUserProfileDropdown\(\);\s*\}\s*$/', '', $scriptContent);

// Ensure the main init function includes hotel filters initialization
$scriptContent = preg_replace('/(function init\(\)\s*\{[^}]+)(\})/', '$1    // Initialize hotel filters when hotels page is shown$2    if (window.location.hash === \'#hotels\' || document.getElementById(\'hotels\')?.classList.contains(\'active\')) {$2        setTimeout(initHotelFilters, 100);$2    }$2', $scriptContent);

// Write the fixed content back
file_put_contents($scriptPath, $scriptContent);

echo "JavaScript file has been fixed!\n";
echo "- Removed duplicate init functions\n";
echo "- Removed duplicate event listeners\n";
echo "- Added hotel filters initialization to main init function\n";
echo "- Profile dropdown should now work properly\n";
?>
