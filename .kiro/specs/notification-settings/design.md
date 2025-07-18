# Design Document

## Overview

The notification settings feature will extend the existing settings system to provide comprehensive notification management capabilities. The design follows the established patterns in the codebase, utilizing the existing SettingService, SettingController, and database structure while adding a new notification-specific view and constants.

## Architecture

The notification settings feature integrates into the existing settings architecture:

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Routes        │───▶│  SettingController│───▶│  SettingService │
│                 │    │                  │    │                 │
└─────────────────┘    └──────────────────┘    └─────────────────┘
                                │                        │
                                ▼                        ▼
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│ notifications   │    │   Validation     │    │   Setting Model │
│ .blade.php      │    │   Request        │    │   (Database)    │
└─────────────────┘    └──────────────────┘    └─────────────────┘
```

## Components and Interfaces

### 1. Constants Extension
- Add `NOTIFICATION_SETTINGS = "notifications"` constant to `app/CentralLogics/Constants.php`
- This constant will be used to identify notification settings in the database

### 2. View Component
- Create `resources/views/admin/settings/notifications.blade.php`
- Follow the same structure as `general.blade.php` and `business-rules.blade.php`
- Include three main sections:
  - User Notifications Card
  - Admin Notifications Card  
  - Notification Schedules Card

### 3. Navigation Integration
- Update `resources/views/admin/settings/_partials/tabs.blade.php`
- Add notifications tab between existing tabs
- Ensure proper active state handling

### 4. Controller Integration
- Extend existing `SettingController` to handle notifications tab
- No new methods needed - existing `index()`, `updateSettings()`, and `updateStatus()` methods will handle notifications
- Add notifications case to the conditional logic in `index()` method

### 5. Service Layer
- Utilize existing `SettingService` methods
- No modifications needed - existing methods support any settings type

## Data Models

### Settings Database Structure
The existing `settings` table structure will be used:

```sql
settings
├── id (primary key)
├── settings_type (string) - Will contain "notifications"
├── key_name (string) - Individual setting keys
├── value (text) - Setting values
├── is_active (boolean) - For toggle settings
├── created_at (timestamp)
└── updated_at (timestamp)
```

### Notification Setting Keys

#### User Notifications
- `enable_email_notifications` (boolean)
- `enable_push_notifications` (boolean) 
- `enable_sms_notifications` (boolean)
- `notify_price_drops` (boolean)
- `notify_new_markets` (boolean)

#### Admin Notifications  
- `notify_new_user_registrations` (boolean)
- `notify_new_market_submissions` (boolean)
- `notify_new_product_submissions` (boolean)
- `notify_user_reports_flags` (boolean)
- `notify_system_errors_warnings` (boolean)

#### Notification Schedules
- `digest_frequency` (string: "real-time", "daily", "weekly")
- `quiet_hours_start` (time: "HH:MM")
- `quiet_hours_end` (time: "HH:MM") 
- `digest_delivery_time` (time: "HH:MM")

## Error Handling

### Validation
- Use existing `UpdateSettingsRequest` validation class
- Add notification-specific validation rules for:
  - Time format validation for schedule fields
  - Enum validation for digest_frequency
  - Boolean validation for toggle settings

### Error States
- Database connection failures: Display generic error message
- Validation failures: Display field-specific error messages
- AJAX toggle failures: Display toast notification error
- Missing settings: Use default values gracefully

### Default Values
```php
'notifications' => [
    'enable_email_notifications' => true,
    'enable_push_notifications' => true,
    'enable_sms_notifications' => false,
    'notify_price_drops' => true,
    'notify_new_markets' => true,
    'notify_new_user_registrations' => true,
    'notify_new_market_submissions' => true,
    'notify_new_product_submissions' => true,
    'notify_user_reports_flags' => true,
    'notify_system_errors_warnings' => true,
    'digest_frequency' => 'daily',
    'quiet_hours_start' => '22:00',
    'quiet_hours_end' => '07:00',
    'digest_delivery_time' => '09:00',
]
```

## Testing Strategy

### Unit Tests
- Test SettingService methods with notification settings type
- Test default value handling for missing notification settings
- Test validation rules for notification-specific fields

### Integration Tests
- Test complete notification settings update flow
- Test AJAX toggle functionality for notification switches
- Test navigation between settings tabs including notifications
- Test error handling for invalid notification setting values

### Browser Tests
- Test notification settings page rendering
- Test form submission and success/error feedback
- Test toggle switch interactions
- Test time picker functionality for schedule settings

## User Interface Design

### Layout Structure
```
┌─────────────────────────────────────────────────────────────┐
│ Settings Navigation Tabs                                    │
│ [General] [Business] [Notifications] [Mail] [Integrations] │
└─────────────────────────────────────────────────────────────┘
┌─────────────────────────────────────────────────────────────┐
│ User Notifications Card                                     │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ ☑ Email notifications                    [Toggle]      │ │
│ │ ☑ Push notifications                     [Toggle]      │ │
│ │ ☐ SMS notifications                      [Toggle]      │ │
│ │ ☑ Price drop alerts                      [Toggle]      │ │
│ │ ☑ New market alerts                      [Toggle]      │ │
│ └─────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
┌─────────────────────────────────────────────────────────────┐
│ Admin Notifications Card                                    │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ ☑ New user registrations                 [Toggle]      │ │
│ │ ☑ New market submissions                 [Toggle]      │ │
│ │ ☑ New product submissions                [Toggle]      │ │
│ │ ☑ User reports and flags                 [Toggle]      │ │
│ │ ☑ System errors and warnings            [Toggle]      │ │
│ └─────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
┌─────────────────────────────────────────────────────────────┐
│ Notification Schedules Card                                 │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ Digest Frequency: [Daily ▼]    Quiet Start: [22:00]   │ │
│ │ Quiet End: [07:00]              Delivery: [09:00]     │ │
│ │                                 [Save Settings]        │ │
│ └─────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

### Styling
- Reuse existing CSS classes from general.blade.php
- Maintain consistent toggle switch styling
- Use existing card and form styling patterns
- Ensure responsive design for mobile devices

### JavaScript Interactions
- Reuse existing `statusAlert()` function for toggle switches
- Implement form submission handling consistent with other settings
- Add time picker validation for schedule fields
- Maintain existing AJAX error handling patterns