-- Add Reports menu item to admin menu
INSERT INTO admin_menu_items (menu_name, menu_url, menu_icon, display_order, is_active, created_at) 
VALUES ('Reports', 'reports.php', 'assessment', 7, 1, NOW());

-- Update display order for existing items if needed
UPDATE admin_menu_items SET display_order = 8 WHERE menu_url = 'analytics.php' AND display_order <= 7;
