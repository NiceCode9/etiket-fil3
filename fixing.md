I have an existing Laravel 12 + Filament 3 web-based ticket sales system.
Initially the system had 3 roles:
- super_admin
- qr_scanner
- wristband_validator

Now the business requirements have changed.
The system must be refactored into a **ticketing system rental platform** with the following new architecture and rules.

==================================================
CORE CONCEPT
==================================================

1. The system becomes a "Ticketing System Rental Platform".
2. There is a new main entity called **Tenant / Penyewa**.
3. Each tenant represents a client who rents the ticketing system.
4. All ticket sales data must be **isolated per tenant**.

==================================================
USER ROLES & OWNERSHIP
==================================================

Roles in the system:
- super_admin
- tenant_admin (penyewa)
- qr_scanner
- wristband_validator

Role rules:
1. super_admin:
   - Manages all tenants
   - Creates tenants
   - Creates tenant users
   - Creates events and tickets
   - When creating an event or ticket, super_admin must select which tenant owns it

2. tenant_admin (penyewa):
   - Belongs to exactly one tenant
   - Can ONLY view data related to their tenant
   - CANNOT create, edit, or delete events or tickets
   - Has read-only access

3. qr_scanner:
   - Belongs to exactly one tenant
   - Can ONLY scan QR codes
   - Can ONLY see scan history performed by themselves
   - Cannot see dashboard analytics

4. wristband_validator:
   - Belongs to exactly one tenant
   - Can ONLY validate wristbands
   - Can ONLY see validation history performed by themselves
   - Cannot see dashboard analytics

IMPORTANT:
- qr_scanner and wristband_validator are NO LONGER global admin roles
- They are tenant-scoped users

==================================================
DATABASE CHANGES
==================================================

Implement:
- tenants table
- users table must have tenant_id (nullable only for super_admin)
- events table must have tenant_id
- tickets table must have tenant_id
- orders table must have tenant_id
- scans table with:
  - user_id (scanner)
  - ticket_id
  - scan_type (qr_scan / wristband_validation)
  - scanned_at

Add proper foreign keys and indexes.

==================================================
FILAMENT PANEL STRUCTURE
==================================================

1. Super Admin Panel:
   - Full CRUD access to:
     - Tenants
     - Users
     - Events
     - Tickets
     - Orders
   - Can assign tenant when creating data

2. Tenant Admin Panel:
   - Read-only access to:
     - Events
     - Tickets
     - Orders
   - Cannot create, edit, or delete
   - Dashboard widgets:
     - Daily order chart (per tenant)
     - Best-selling ticket types per event

3. QR Scanner Panel (or Page):
   - Custom Filament Page or Laravel Blade
   - Features:
     - Camera-based QR scan
     - Validate ticket
     - Store scan history
   - Can view:
     - List of scans performed by themselves only

4. Wristband Validator Panel (or Page):
   - Similar to QR Scanner
   - Only validates wristbands
   - Stores validation history
   - Only sees own validation logs

==================================================
AUTHENTICATION & AUTHORIZATION
==================================================

- Use Filament Shield for role & permission management
- Enforce tenant-based authorization using:
  - Global scopes
  - Policies
  - Filament resource query overrides

Rules:
- Tenant users must NEVER see data from other tenants
- super_admin bypasses tenant scope
- qr_scanner and wristband_validator cannot access Filament resources except scan pages

==================================================
UI & UX REQUIREMENTS
==================================================

- Tenant dashboard:
  - Line chart: daily ticket orders
  - Bar or pie chart: most sold ticket types per event
- Scanner pages:
  - Mobile-friendly
  - Minimal UI
  - Fast scanning experience

==================================================
DELIVERABLES
==================================================

Provide:
1. Database migration plan
2. Updated Eloquent model relationships
3. Policy & authorization strategy
4. Filament Resource access rules per role
5. Dashboard widget logic (queries)
6. Scanner & validator page flow
7. Step-by-step refactor plan (safe for existing data)

DO NOT provide vague explanations.
DO NOT skip authorization logic.
Think like a production-grade SaaS multi-tenant system.
