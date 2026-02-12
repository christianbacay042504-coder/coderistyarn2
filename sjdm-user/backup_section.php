<?php
// Create a clean section to replace the problematic part
$clean_section = '
                                <div class="guide-selection-grid">
                                    <?php 
                                        // Use backup variable to bypass any potential interference
                                        $guides_to_render = $GLOBALS[\'tour_guides_backup\'] ?? $tour_guides;
                                        foreach ($guides_to_render as $guide): 
                                    ?>
                                        <div class="guide-selection-card <?php echo ($preselected_guide == $guide[\'id\']) ? \'selected\' : \'\'; ?>" data-guide-id="<?php echo $guide[\'id\']; ?>" onclick="selectGuide(<?php echo $guide[\'id\']; ?>)">
                                            <div class="guide-card-header">
                                                <div class="guide-avatar">
                                                    <?php echo strtoupper(substr($guide[\'name\'], 0, 1)); ?>
                                                </div>
                                                <div class="guide-info">
                                                    <h4><?php echo htmlspecialchars($guide[\'name\']); ?></h4>
                                                    <span class="guide-specialty"><?php echo htmlspecialchars($guide[\'specialty\']); ?></span>
                                                </div>
                                            </div>
                                            <div class="guide-details">
                                                <div class="guide-detail">
                                                    <span class="material-icons-outlined">schedule</span>
                                                    <?php echo htmlspecialchars($guide[\'experience\']); ?> experience
                                                </div>
                                                <div class="guide-detail">
                                                    <span class="material-icons-outlined">translate</span>
                                                    <?php echo htmlspecialchars($guide[\'languages\']); ?>
                                                </div>
                                                <div class="guide-detail">
                                                    <span class="material-icons-outlined">groups</span>
                                                    Up to <?php echo htmlspecialchars($guide[\'max_group_size\']); ?>
                                                </div>
                                            </div>
                                            <div class="guide-description">
                                                <?php echo htmlspecialchars($guide[\'description\']); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="no-guides-message">
                                        <span class="material-icons-outlined">person_off</span>
                                        <h3>No tour guides available</h3>
                                        <p>Please check back later for available tour guides.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <input type="hidden" id="selectedGuide" name="selectedGuide" value="<?php echo htmlspecialchars($preselected_guide); ?>" required>
                        </div>
';

echo "Clean section created";
?>
