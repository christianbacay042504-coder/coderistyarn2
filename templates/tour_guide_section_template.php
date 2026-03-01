<?php
// Tour Guide Section Template for Tourist Detail Pages
// Usage: Include this file and call displayTourGuideSection($destinationName)

require_once __DIR__ . '/../functions/tour_guide_functions.php';

/**
 * Display the tour guide section for a destination
 * @param string $destinationName The name of the destination
 * @param bool $showInCard Whether to display in a card format (default: true)
 */
function displayTourGuideSection($destinationName, $showInCard = true) {
    $assignedGuide = getAssignedTourGuide($destinationName);
    
    if ($showInCard) {
        echo '<div class="tour-guide-section">';
        echo '<h3 class="section-subtitle" style="margin-top: 0; color: var(--primary);">';
        echo '<span class="material-icons-outlined" style="vertical-align: middle; margin-right: 8px;">person</span>';
        echo 'Assigned Tour Guide';
        echo '</h3>';
    } else {
        echo '<div class="tour-guide-section" style="margin: 40px 0;">';
        echo '<h2 class="section-title">Your Assigned Tour Guide</h2>';
    }
    
    if ($assignedGuide) {
        echo '<div class="tour-guide-header">';
        echo '<div class="tour-guide-avatar">';
        if ($assignedGuide['photo_url']) {
            echo '<img src="' . htmlspecialchars($assignedGuide['photo_url']) . '" alt="' . htmlspecialchars($assignedGuide['name']) . '">';
        } else {
            echo strtoupper(substr($assignedGuide['name'], 0, 1));
        }
        echo '</div>';
        echo '<div class="tour-guide-info">';
        echo '<h4>' . htmlspecialchars($assignedGuide['name']) . '</h4>';
        echo '<div class="tour-guide-specialty">' . formatGuideSpecialty($assignedGuide['specialty']) . '</div>';
        echo '<div class="tour-guide-rating">';
        echo formatGuideRating($assignedGuide['rating'], $assignedGuide['review_count']);
        echo '</div>';
        echo '</div>';
        echo '</div>';
        
        if ($assignedGuide['bio']) {
            echo '<div class="tour-guide-bio">';
            echo '<strong>About your guide:</strong> ' . htmlspecialchars($assignedGuide['bio']);
            echo '</div>';
        }
        
        echo '<div class="tour-guide-details">';
        if ($assignedGuide['contact_number']) {
            echo '<div class="tour-guide-contact-item">';
            echo '<span class="material-icons-outlined">phone</span>';
            echo '<span>' . htmlspecialchars($assignedGuide['contact_number']) . '</span>';
            echo '</div>';
        }
        
        if ($assignedGuide['email']) {
            echo '<div class="tour-guide-contact-item">';
            echo '<span class="material-icons-outlined">email</span>';
            echo '<span>' . htmlspecialchars($assignedGuide['email']) . '</span>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<div class="no-guide-assigned">';
        echo '<span class="material-icons-outlined" style="font-size: 2rem; display: block; margin-bottom: 10px;">person_off</span>';
        echo 'No tour guide assigned yet for this destination.<br>';
        echo '<small>Contact us for guided tour arrangements.</small>';
        echo '</div>';
    }
    
    echo '</div>';
}

/**
 * Get the CSS styles for the tour guide section
 * @return string CSS styles
 */
function getTourGuideCSS() {
    return '
    /* Tour Guide Section Styles */
    .tour-guide-section {
        margin: 30px 0;
        padding: 25px;
        background: linear-gradient(135deg, rgba(44, 95, 45, 0.05), rgba(76, 140, 76, 0.1));
        border-radius: 15px;
        border: 1px solid rgba(44, 95, 45, 0.2);
    }

    .tour-guide-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .tour-guide-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 700;
        flex-shrink: 0;
        border: 3px solid white;
        box-shadow: 0 4px 12px rgba(44, 95, 45, 0.3);
    }

    .tour-guide-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .tour-guide-info h4 {
        margin: 0 0 5px;
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--primary);
    }

    .tour-guide-specialty {
        font-size: 0.9rem;
        color: var(--text-light);
        margin-bottom: 5px;
    }

    .tour-guide-rating {
        font-size: 0.85rem;
        color: #ff9800;
        font-weight: 600;
    }

    .tour-guide-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-top: 20px;
    }

    .tour-guide-contact-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px;
        background: white;
        border-radius: 10px;
        font-size: 0.9rem;
        color: var(--text-dark);
        border: 1px solid rgba(44, 95, 45, 0.1);
    }

    .tour-guide-contact-item .material-icons-outlined {
        color: var(--primary);
        font-size: 20px;
        flex-shrink: 0;
    }

    .tour-guide-bio {
        margin-top: 20px;
        padding: 15px;
        background: white;
        border-radius: 10px;
        font-size: 0.9rem;
        line-height: 1.6;
        color: var(--text-light);
        border: 1px solid rgba(44, 95, 45, 0.1);
    }

    .no-guide-assigned {
        text-align: center;
        padding: 20px;
        color: var(--text-light);
        font-style: italic;
        background: rgba(255, 255, 255, 0.7);
        border-radius: 10px;
        border: 1px solid var(--border);
    }
    ';
}
?>
