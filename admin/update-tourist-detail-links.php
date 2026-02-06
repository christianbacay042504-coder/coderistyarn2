<?php
// Update all tourist-detail pages to include links to sjdm-user tourist-spots

$touristDetailDir = __DIR__ . '/../tourist-detail';
$files = glob($touristDetailDir . '/*.php');

foreach ($files as $file) {
    echo "Processing: " . basename($file) . "\n";
    
    $content = file_get_contents($file);
    
    // Check if the file already has the "View All Destinations" button
    if (strpos($content, 'View All Destinations') !== false) {
        echo "  - Skipped (already has View All Destinations button)\n";
        continue;
    }
    
    // Find the CTA buttons section and add the new button
    $ctaButtonsPattern = '/(<div class="cta-buttons">)(.*?)(<\/div>)/s';
    
    if (preg_match($ctaButtonsPattern, $content, $matches)) {
        $newButton = '<button class="btn-secondary" onclick="location.href=\'/coderistyarn2/sjdm-user/tourist-spots.php\'">
                        <span class="material-icons-outlined">place</span>
                        View All Destinations
                    </button>';
        
        // Add the new button after the existing buttons
        $updatedCtaButtons = $matches[1] . $matches[2] . $newButton . $matches[3];
        
        // Replace the original CTA buttons section
        $content = str_replace($matches[0], $updatedCtaButtons, $content);
        
        // Save the updated file
        if (file_put_contents($file, $content)) {
            echo "  - Added View All Destinations button\n";
        } else {
            echo "  - Failed to update file\n";
        }
    } else {
        echo "  - CTA buttons section not found\n";
    }
}

echo "\nUpdate complete!\n";
?>
