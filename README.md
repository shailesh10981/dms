# DMS (Document Management + Risk Reporting)

A Laravel 10 application implementing a DMS with versioning, configurable approval workflows, audit history, and a Risk Reporting module with multi‑step approvals.

## Quick start (DB mode)

Prerequisites
- PHP 8.1+
- Composer
- SQLite (recommended for quick start) or MySQL

Setup
1) Copy env and set DB
```
cp .env.example .env
```
Option A – SQLite (recommended for local):
```
sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=sqlite/' .env
sed -i 's/^DB_DATABASE=.*/DB_DATABASE=$(pwd)\/database\/database.sqlite/' .env
mkdir -p database && touch database/database.sqlite
```
Option B – MySQL: set DB_* in .env accordingly.

2) Install dependencies
```
composer install --prefer-dist --no-interaction --ignore-platform-req=ext-ldap
composer require doctrine/dbal:^3 --no-interaction
```
Note: ext-ldap is only required for LDAP mode. You can ignore it in local DB mode as shown above.

3) App key, migrate, seed, storage link
```
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

4) Run
```
php artisan serve
```

Login (seeded)
- Admin: admin@dms.com / password
- Manager: manager@dms.com / password
- Compliance Officer: compliance@dms.com / password
- Users: finance1@dms.com, finance2@dms.com, hr1@dms.com, it1@dms.com, ops1@dms.com, auditor@dms.com / password

## Key features

DMS
- Upload with metadata (Department, Location, Project, Visibility, Expiry)
- Approval Workflow: pick approvers in order (Select2); default HOD used if left empty
- Versioning: re-uploads create a new working version; after final approval, previous versions are purged (DB rows and files)
- Audit trail: actions (create/update/submit/approve/reject/download) recorded and visible on the document page
- Trash and restore (soft delete)

Risk Reporting
- Four issue types with dynamic fields (Operational, Compliance, Financial, Security)
- Optional attachment
- Workflow per submission; default HOD is preselected based on Department
- Current approver can approve/reject; all transitions logged to RiskAuditLog

Dashboard
- Quick actions (Upload Document / Create Risk)
- Visibility cards (Private / Public / Publish) and counts
- Pending approvals (documents/risks), recent items, status chips, and workflow order preview

## LDAP (optional)
To enable LDAP login (via ldaprecord‑laravel):
```
# .env
LDAP_ENABLED=true
LDAP_CONNECTION=default
LDAP_HOST=ad.example.com
LDAP_PORT=636
LDAP_BASE_DN=dc=example,dc=com
LDAP_USERNAME=cn=service_account,ou=users,dc=example,dc=com
LDAP_PASSWORD=********
LDAP_SSL=true
LDAP_TLS=false
LDAP_LOGGING=true
LDAP_CACHE=false
```
Requirements
- PHP ext-ldap on the server (remove the --ignore-platform-req=ext-ldap flag and ensure the extension is installed)
- Groups → roles mapping configured in config/ldap.php (role_mapping)

## QA harness
Run the end‑to‑end checks in DB mode without the browser:
```
php scripts/qa_runner.php
cat docs/qa/qa_results.json
```
This uploads a sample document, creates a new version, routes it through approval, and exercises the risk submission and approval flow.

## Notes
- Document ID format is DEPTYYYY-MM-DD-NNN. Uniqueness is enforced; sequence is per‑day. Adjust generation in App\Models\Document@generateDocumentId if you require strict per‑department sequencing.
- Mail notifications are stored in the database by default (see App\Notifications). Configure mail in .env to send emails.
