# SJDM Tours - Admin Database Structure (Updated)

## Overview
Complete admin database system with **admin_** prefix for all admin-related tables to clearly separate them from regular application tables.

## Admin Tables with "admin_" Prefix

### 1. `admin_users` Table
**Purpose**: Core admin user information with admin marks
```sql
- id (PK, Auto Increment)
- user_id (FK to users.id, Unique)
- admin_mark (varchar, default 'A') - Admin badge/mark
- role_title (varchar, default 'Administrator')
- permissions (text, default NULL)
- created_at, updated_at (timestamps)
```

**Sample Data**:
- user_id: 1, admin_mark: 'ADMIN', role_title: 'Super Administrator'
- user_id: 1, admin_mark: 'ADMIN_DASHBOARD', role_title: 'Dashboard Administrator'
- user_id: 1, admin_mark: 'ADMIN_USERS', role_title: 'User Administrator'
- user_id: 1, admin_mark: 'ADMIN_CONTENT', role_title: 'Content Administrator'

### 2. `admin_dashboards` Table
**Purpose**: Dashboard configurations and layouts
```sql
- id (PK, Auto Increment)
- admin_id (FK to admin_users.id)
- dashboard_name (varchar) - 'Main Dashboard', 'Analytics Dashboard'
- dashboard_layout (json) - Layout configuration
- widgets_config (json) - Widget settings
- theme_settings (json) - Theme preferences
- is_default (boolean) - Default dashboard flag
- created_at, updated_at (timestamps)
```

### 3. `admin_permissions_table` Table
**Purpose**: Granular admin permissions by module
```sql
- id (PK, Auto Increment)
- admin_id (FK to admin_users.id)
- module (varchar) - 'dashboard', 'users', 'bookings', etc.
- permission_type (enum) - 'read', 'write', 'delete', 'admin'
- granted_at (timestamp)
- granted_by (FK to admin_users.id)
```

### 4. `admin_activity` Table
**Purpose**: Track all admin actions for security
```sql
- id (PK, Auto Increment)
- admin_id (FK to admin_users.id)
- action (varchar) - 'ACCESS', 'CREATE', 'UPDATE', 'DELETE'
- module (varchar) - 'dashboard', 'users', 'bookings'
- description (text) - Action details
- ip_address (varchar) - Admin's IP
- user_agent (varchar) - Browser info
- created_at (timestamp)
```

### 5. `admin_preferences` Table
**Purpose**: Personal admin preferences
```sql
- id (PK, Auto Increment)
- admin_id (FK to admin_users.id)
- setting_key (varchar) - 'theme', 'language', etc.
- setting_value (text) - Setting value
- setting_type (enum) - 'text', 'number', 'boolean', 'json'
- category (varchar) - 'appearance', 'performance', 'security'
- created_at, updated_at (timestamps)
```

### 6. `admin_dashboard_settings` Table
**Purpose**: Global dashboard settings
```sql
- id (PK, Auto Increment)
- setting_key (varchar, Unique) - 'page_title', 'admin_logo_text'
- setting_value (text)
- setting_type (enum) - 'text', 'number', 'boolean'
- description (text)
- created_at, updated_at (timestamps)
```

### 7. `admin_menu_items` Table
**Purpose**: Dynamic admin navigation menu
```sql
- id (PK, Auto Increment)
- menu_name (varchar) - 'Dashboard', 'User Management'
- menu_icon (varchar) - 'dashboard', 'people'
- menu_url (varchar) - 'dashboard.php', 'user-management.php'
- display_order (int) - Menu order
- is_active (boolean) - Show/hide menu item
- parent_id (int) - For submenus
- created_at, updated_at (timestamps)
```

## Admin Mark System

### Admin Mark Types:
- **'A'** - Basic Administrator
- **'ADMIN'** - Super Administrator
- **'ADMIN_DASHBOARD'** - Dashboard Administrator
- **'ADMIN_USERS'** - User Administrator
- **'ADMIN_CONTENT'** - Content Administrator

### Dashboard Features:
1. **Admin Badge Display**: Shows admin mark in user profile
2. **Activity Logging**: Tracks all admin actions with IP and timestamps
3. **Role-based Access**: Different admin levels with specific permissions
4. **Customizable Dashboard**: Multiple dashboard layouts and widgets
5. **Personal Settings**: Individual admin preferences and themes

## Security Features

1. **Activity Logging**: Every admin action is logged in `admin_activity`
2. **IP Tracking**: Records admin IP addresses
3. **Permission System**: Granular permissions by module in `admin_permissions_table`
4. **Foreign Key Constraints**: Data integrity maintained
5. **Timestamp Tracking**: Created/updated timestamps for audit

## Usage in Dashboard

The dashboard now:
- Queries `admin_users` table for admin info and marks
- Logs access to `admin_activity` table
- Uses `admin_dashboard_settings` for global settings
- Can access `admin_preferences` for personal settings
- Displays menu from `admin_menu_items`

## Table Relationships

```
users (1) → (1) admin_users (1) → (many) admin_dashboards
users (1) → (1) admin_users (1) → (many) admin_permissions_table
users (1) → (1) admin_users (1) → (many) admin_preferences
users (1) → (1) admin_users (1) → (many) admin_activity
```

## Import Instructions

1. Import the updated `sjdm_tours.sql` file
2. Admin user (ID 1) will automatically get admin marks in `admin_users`
3. Dashboard will display admin badges and log activity to `admin_activity`
4. All admin tables are properly linked with foreign keys

## Benefits of "admin_" Prefix

1. **Clear Separation**: All admin tables are clearly marked
2. **Database Organization**: Easy to identify admin-related tables
3. **Security**: Clear distinction between user and admin data
4. **Maintenance**: Easier to manage admin-specific data
5. **Scalability**: Easy to add new admin tables with consistent naming

This provides a complete admin management system with proper database structure, clear table naming, and comprehensive security features.
