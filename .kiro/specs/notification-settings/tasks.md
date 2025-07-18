# Implementation Plan

- [x] 1. Add notification settings constant
  - Add `NOTIFICATION_SETTINGS = "notifications"` constant to `app/CentralLogics/Constants.php`
  - Ensure constant follows existing naming convention
  - _Requirements: 4.1, 4.2_

- [x] 2. Update settings navigation tabs
  - Modify `resources/views/admin/settings/_partials/tabs.blade.php` to include notifications tab
  - Add notifications tab link with proper active state handling
  - Position notifications tab appropriately in the navigation order
  - _Requirements: 4.1, 4.2_

- [x] 3. Extend SettingController for notifications
  - Update `app/Http/Controllers/Admin/SettingController.php` index method
  - Add conditional logic to handle notifications tab case
  - Ensure settings collection is properly formatted for notifications view
  - _Requirements: 4.3, 4.4, 5.1_

- [x] 4. Create notification settings view
  - Create `resources/views/admin/settings/notifications.blade.php`
  - Implement User Notifications card with toggle switches for email, push, SMS, price drops, and new market notifications
  - Implement Admin Notifications card with toggle switches for user registrations, market submissions, product submissions, user reports, and system errors
  - Implement Notification Schedules card with form fields for digest frequency, quiet hours, and delivery time
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 1.6, 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 3.1, 3.2, 3.3, 3.4, 3.5_

- [x] 5. Implement toggle switch functionality
  - Add proper `onchange="statusAlert(this)"` handlers to all notification toggle switches
  - Configure correct `data-url` attributes pointing to update-status route with notifications tab
  - Ensure toggle switches display current setting values from database
  - _Requirements: 1.2, 1.3, 1.4, 1.5, 1.6, 2.2, 2.3, 2.4, 2.5, 2.6, 4.4_

- [x] 6. Implement notification schedules form
  - Create form with proper action pointing to settings update route with notifications tab
  - Add select dropdown for digest frequency with options: real-time, daily, weekly
  - Add time input fields for quiet hours start, quiet hours end, and digest delivery time
  - Add form validation and proper name attributes for all fields
  - Include save button with consistent styling
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 4.3_

- [x] 7. Add validation rules for notification settings
  - Update `app/Http/Requests/UpdateSettingsRequest.php` to include notification-specific validation
  - Add time format validation for quiet_hours_start, quiet_hours_end, and digest_delivery_time
  - Add enum validation for digest_frequency field
  - Add boolean validation for all toggle settings
  - _Requirements: 5.4, 5.5_

- [x] 8. Update SettingService default values
  - Modify `app/Services/SettingService.php` getDefaultSettings method
  - Add notifications section with all default values as specified in design
  - Ensure default values match the requirements for each notification setting
  - _Requirements: 5.4_

- [x] 9. Test notification settings integration
  - Create test to verify notification settings page loads correctly
  - Create test to verify toggle switches update settings via AJAX
  - Create test to verify notification schedules form submission works
  - Create test to verify proper error handling for invalid notification settings
  - _Requirements: 4.5, 5.2, 5.3, 5.5_

- [x] 10. Add proper styling and JavaScript
  - Ensure notification settings page uses consistent CSS classes from existing settings pages
  - Verify toggle switch styling matches other settings pages
  - Add any necessary JavaScript for time picker functionality
  - Test responsive design for mobile devices
  - _Requirements: 4.1, 4.2_