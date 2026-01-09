# Multi-Tenant Refactoring Guide

This document outlines the complete refactoring of the ticketing system from a single-tenant to a multi-tenant rental platform.

## Overview

The system has been transformed from a single-tenant ticketing system to a **Ticketing System Rental Platform** where multiple tenants (clients) can rent and use the system with complete data isolation.

## Database Changes

### New Tables

1. **tenants** - Stores tenant/client information
   - Fields: id, name, slug, email, phone, address, is_active, timestamps, soft_deletes

### Modified Tables

1. **users** - Added `tenant_id` (nullable, only null for super_admin)
2. **events** - Added `tenant_id` (required)
3. **orders** - Added `tenant_id` (required, auto-set from event)
4. **tickets** - Added `tenant_id` (required, auto-set from event)

### Refactored Tables

1. **scan_logs** → **scans** - Completely refactored
   - Old structure: ticket_id, scan_type, scanned_by, scanned_at, status, notes, ip_address, user_agent
   - New structure: user_id, ticket_id, tenant_id, scan_type, scanned_at
   - Simplified to focus on core scanning functionality

## Models Updated

### New Models
- `App\Models\Tenant` - Tenant model with relationships
- `App\Models\Scan` - Replaces ScanLog model

### Updated Models
All models now use the `HasTenant` trait for automatic tenant scoping:
- `App\Models\User` - Added tenant relationship
- `App\Models\Event` - Added tenant relationship and scope
- `App\Models\Order` - Added tenant relationship, auto-sets from event
- `App\Models\Ticket` - Added tenant relationship, auto-sets from event
- `App\Models\Scan` - New model with tenant relationship

### HasTenant Trait

Located at `app/Models/Concerns/HasTenant.php`, this trait:
- Applies global scope to filter by tenant automatically
- Super admin bypasses tenant scope
- Tenant users can only see their tenant's data
- Provides `scopeForTenant()` query scope

## User Roles

### Role Structure

1. **super_admin**
   - No tenant_id (nullable)
   - Full CRUD access to all tenants and their data
   - Can create tenants, users, events, tickets, orders
   - Can assign tenant when creating data
   - Bypasses all tenant scopes

2. **tenant_admin**
   - Must have tenant_id
   - Read-only access to Events, Tickets, Orders
   - Cannot create, edit, or delete
   - Can view dashboard with tenant-specific analytics
   - All forms are disabled for this role

3. **qr_scanner**
   - Must have tenant_id
   - Can ONLY scan QR codes
   - Can ONLY see scan history performed by themselves
   - Cannot access Filament resources (except scan pages)
   - Tenant-scoped scanning

4. **wristband_validator**
   - Must have tenant_id
   - Can ONLY validate wristbands
   - Can ONLY see validation history performed by themselves
   - Cannot access Filament resources (except scan pages)
   - Tenant-scoped validation

## Filament Resources

### New Resources

1. **TenantResource** (`app/Filament/Resources/TenantResource.php`)
   - Full CRUD for tenants
   - Only accessible by super_admin
   - Navigation group: "System Management"

### Updated Resources

1. **EventResource**
   - Super admin: Can select tenant when creating
   - Tenant admin: Read-only, all fields disabled
   - Auto-sets tenant_id for tenant_admin users
   - Shows tenant column in table (super admin only)

2. **OrderResource**
   - Super admin: Can select tenant when creating
   - Tenant admin: Read-only, all fields disabled
   - Auto-sets tenant_id from event or user
   - Shows tenant column in table (super admin only)

3. **TicketResource**
   - Similar updates as OrderResource
   - Tenant scoping applied

4. **UserResource** (if exists)
   - Super admin: Can assign tenant when creating users
   - Tenant users: Cannot be created by tenant_admin

## Authorization & Policies

### Global Scopes

The `HasTenant` trait automatically applies tenant scoping:
- Super admin: Sees all data
- Tenant users: Only see their tenant's data

### Policies

All policies should be updated to:
1. Check if user is super_admin (bypass tenant check)
2. Check if user's tenant_id matches the resource's tenant_id
3. Enforce read-only for tenant_admin role

### Filament Resource Access

- **super_admin**: Full access to all resources
- **tenant_admin**: Read-only access to Events, Tickets, Orders
- **qr_scanner**: No resource access (only scan pages)
- **wristband_validator**: No resource access (only scan pages)

## Scanner Pages

### QR Scanner (`app/Filament/Pages/QRScanner.php`)

- Scans QR codes to issue wristbands
- Tenant-scoped: Only scans tickets from user's tenant
- Creates Scan records with type 'qr_scan'
- Updates ticket status to 'scanned_for_wristband'
- Accessible by: super_admin, qr_scanner

### Wristband Validator (`app/Filament/Pages/WristbandValidator.php`)

- Validates wristband codes
- Tenant-scoped: Only validates tickets from user's tenant
- Creates Scan records with type 'wristband_validation'
- Updates ticket status to 'used'
- Accessible by: super_admin, wristband_validator

## Migration Steps

### 1. Run Migrations

```bash
php artisan migrate
```

This will:
- Create tenants table
- Add tenant_id to users, events, orders, tickets
- Refactor scan_logs to scans table

### 2. Seed Roles

```bash
php artisan db:seed --class=RoleSeeder
```

This creates:
- All roles (super_admin, tenant_admin, qr_scanner, wristband_validator)
- Sample tenant
- Sample users for each role

### 3. Migrate Existing Data

**IMPORTANT**: Before running migrations on production:

1. Create a default tenant for existing data
2. Update existing records to assign tenant_id:
   ```php
   // In a migration or seeder
   $defaultTenant = Tenant::create([
       'name' => 'Default Tenant',
       'slug' => 'default',
       'is_active' => true,
   ]);
   
   // Assign existing data to default tenant
   Event::whereNull('tenant_id')->update(['tenant_id' => $defaultTenant->id]);
   Order::whereNull('tenant_id')->update(['tenant_id' => $defaultTenant->id]);
   Ticket::whereNull('tenant_id')->update(['tenant_id' => $defaultTenant->id]);
   ```

3. Update existing users:
   - Super admin users: Keep tenant_id as null
   - Other users: Assign to appropriate tenant

### 4. Update Scan Logs

If you have existing scan_logs data, create a migration to migrate it:

```php
// Migrate old scan_logs to new scans table
$scanLogs = DB::table('scan_logs')->get();
foreach ($scanLogs as $log) {
    $ticket = Ticket::find($log->ticket_id);
    if ($ticket) {
        Scan::create([
            'user_id' => $log->scanned_by,
            'ticket_id' => $log->ticket_id,
            'tenant_id' => $ticket->tenant_id,
            'scan_type' => $log->scan_type === 'qr_for_wristband' ? 'qr_scan' : 'wristband_validation',
            'scanned_at' => $log->scanned_at,
        ]);
    }
}
```

## Dashboard Widgets (To Be Implemented)

### For Tenant Admin Dashboard

1. **Daily Orders Chart**
   - Line chart showing daily ticket orders for the tenant
   - Query: `Order::where('tenant_id', auth()->user()->tenant_id)->groupBy('date')`

2. **Best-Selling Ticket Types**
   - Bar or pie chart showing most sold ticket types per event
   - Query: Aggregate ticket sales by ticket_type_id for tenant's events

## Testing Checklist

- [ ] Super admin can create tenants
- [ ] Super admin can create users and assign tenants
- [ ] Super admin can create events and assign tenants
- [ ] Tenant admin can view events (read-only)
- [ ] Tenant admin cannot create/edit/delete events
- [ ] QR scanner can only scan tickets from their tenant
- [ ] Wristband validator can only validate tickets from their tenant
- [ ] Tenant users cannot see data from other tenants
- [ ] Global scopes work correctly
- [ ] Auto-setting tenant_id works in Order and Ticket creation

## Security Considerations

1. **Always validate tenant_id** in controllers and policies
2. **Never trust client-side tenant_id** - always set server-side
3. **Use global scopes** as primary defense
4. **Policies as secondary defense** for explicit checks
5. **Test tenant isolation** thoroughly

## Next Steps

1. Create dashboard widgets for tenant admin
2. Update all remaining Filament resources with tenant scoping
3. Add tenant filtering to all resource tables
4. Create scan history pages for scanners
5. Add comprehensive tests for tenant isolation
6. Update API endpoints (if any) with tenant scoping

## Notes

- The `HasTenant` trait uses Laravel's global scopes which automatically apply to all queries
- Super admin bypass is handled in the trait's boot method
- All tenant-scoped models should use the `HasTenant` trait
- Remember to update policies for all resources
