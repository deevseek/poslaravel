# Multi-Tenant + Subscription Architecture

## Domain & Routing
- Central domain: `profesionalservis.my.id`.
- Tenants use subdomains: `{slug}.profesionalservis.my.id`.
- `InitializeTenant` middleware resolves tenant from subdomain, switches DB connection to tenant database-per-tenant.
- `EnsureSubscriptionActive` middleware guards POS and service routes so expired tenants are read-only.

## Database Strategy
- Central database stores **only** SaaS control-plane data (tenants, plans, subscriptions, billing).
- Each tenant gets an isolated MySQL schema named `tenant_{slug}`.
- Tenant DB is selected at runtime via `database.connections.tenant` and `TenantManager::switchToTenantConnection()`.
- Existing POS migrations are re-used for tenant databases via `tenancy.tenant_migrations_paths`.

## Folder Layout
```
app/
  Console/Commands   # tenant:create, tenant:migrate, tenant:seed, tenant:deactivate
  Tenancy/
    Middleware/      # InitializeTenant, EnsureSubscriptionActive
    Models/          # Tenant, Subscription, SubscriptionPlan
    Services/        # TenantProvisioningService
    Support/         # TenantManager for resolution + DB switching
config/tenancy.php   # tenancy configuration
bootstrap/app.php    # middleware aliases for tenancy
routes/console.php   # commands auto-discovered via Kernel
```

## Provisioning Flow
1. Validate requested subdomain uniqueness (`tenant:create` or registration controller).
2. Create tenant record in central DB with `tenant_{slug}` database name.
3. Create MySQL database schema.
4. Run tenant migrations (`database/migrations` + `database/migrations/tenant`) against the `tenant` connection.
5. Seed tenant defaults + admin user (email/password from payload).
6. Attach initial subscription record tied to chosen plan.
7. Tenant accesses app via `{subdomain}.profesionalservis.my.id`.

## Registration Endpoint (concept)
- POST `/register` on central domain collects: store name, subdomain, admin email/password, plan.
- Controller delegates to `TenantProvisioningService::provision()`.
- After provision, redirect to tenant login: `https://{subdomain}.profesionalservis.my.id/login`.

## Middleware Usage
- Apply `tenant` middleware globally on web routes to resolve and switch the database.
- Apply `subscription.active` on mutation routes (POS checkout, service intake). Read-only pages can omit it to allow expired tenants to view data.

## Artisan Operations
- `php artisan tenant:create "Toko Jaya" tokojaya owner@tokojaya.id secret --plan=1`
- `php artisan tenant:migrate` (or `--tenant=1` for specific) to run migrations per tenant.
- `php artisan tenant:seed --tenant=tokojaya`
- `php artisan tenant:deactivate tokojaya`

## Security & Scaling Notes
- DB-per-tenant enforces isolation; no `tenant_id` column usage allowed.
- Use dedicated MySQL user with restricted grants for tenants (via `TENANT_DB_*` env) to reduce blast radius.
- Enforce HTTPS and HSTS on central + tenant domains; validate subdomains against allowlist to avoid wildcard hijack.
- Use queues for provisioning to keep registration responsive; wrap provisioning in transactions and add retries for migration/seed steps.
- Add connection pooling (e.g., ProxySQL or RDS Proxy) and caching for tenant lookups to minimize latency.
- Implement backup/restore per tenant schema; automate subscription checks via scheduler.
- Monitor migration runtime and add feature flags for large tenants; shard tenants across DB servers if needed (connection info per-tenant record).

## Subscription Guard Behavior
- Expired subscription: login allowed but mutation endpoints blocked by `EnsureSubscriptionActive` (HTTP 402).
- Suspended tenant: blocked at middleware (HTTP 403) before DB switch.

## End-to-End Tenant Registration (happy path)
1. Central controller validates input and subdomain uniqueness.
2. `TenantProvisioningService` creates tenant record and database.
3. Migrations + seeders run on new DB; default admin user created.
4. Subscription created and set active.
5. DNS/wildcard SSL already configured; user logs in at new subdomain.
