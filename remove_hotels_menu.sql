-- Remove Hotels from Admin Menu
-- This script removes the hotels menu item from admin_menu_items table

DELETE FROM admin_menu_items WHERE menu_name = 'Hotels' AND menu_url = 'hotels.php';

-- Verify removal
SELECT * FROM admin_menu_items WHERE is_active = 1 ORDER BY display_order ASC;
