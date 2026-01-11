# Permission Structure

## Roles

The system currently supports the following roles for the Admin Panel:

1.  **Administrator** (`role:administrator`)
    - Full access to all modules.
    - Can manage Users (Admins), Settings, Events, and Backups.
    - Can approve/reject payments manually.
2.  **Petugas / Officer** (Implicit/Default)
    - Limited access.
    - Can access Dashboard.
    - Can View Orders and Participants.
    - Can access `Race Kit Collection` (Verification & Handover).
    - **Cannot** delete data, change settings, or perform backups.

## Route Filters

- `adminAuth`: Checks if the user is logged in as an admin. Applied to `/admin/*`.
- `role:administrator`: Checks if the logged-in admin has `role == 'administrator'`. Applied to critical management routes (User management, Events, Settings, Reports export).

## Implementation Details

- **Middleware**: `App\Filters\RoleFilter`
- **Auth Controller**: `App\Controllers\Admin\Auth` using `session` storage.

```php
// Example usage in Routes.php
$routes->group('', ['filter' => 'role:administrator'], function($routes) {
    $routes->get('settings', 'Settings::index');
});
```
