# LDAP Implementation Guide for DMS

## Overview

This guide provides step-by-step instructions for implementing LDAP authentication in the Document Management System (DMS). The implementation replaces the existing database-based authentication with LDAP while maintaining role and permission functionality.

## What Has Been Implemented

### 1. LDAP Package Integration
- **LdapRecord-Laravel** package installed and configured
- LDAP configuration file created at `config/ldap.php`
- Environment variables for LDAP connection settings

### 2. LDAP Models
- **LdapUser Model** (`app/Models/LdapUser.php`): Represents LDAP users with methods for role determination
- **LdapGroup Model** (`app/Models/LdapGroup.php`): Represents LDAP groups for role mapping

### 3. Authentication Service
- **LdapAuthService** (`app/Services/LdapAuthService.php`): Handles LDAP authentication, user provisioning, and role assignment

### 4. Database Schema Updates
- Migration to add LDAP fields to users table:
  - `ldap_dn`: LDAP Distinguished Name
  - `ldap_username`: LDAP username
  - `department`: User's department from LDAP
  - `title`: User's title from LDAP
  - `is_ldap_user`: Boolean flag for LDAP users
  - `password`: Made nullable for LDAP users

### 5. Updated Controllers
- **AuthenticatedSessionController**: Modified to use LDAP authentication with fallback to database authentication
- **LdapController**: Admin interface for LDAP testing and configuration

### 6. Admin Interface
- LDAP configuration and testing page at `/admin/ldap`
- Connection testing functionality
- User authentication testing
- User information retrieval from LDAP

## Setup Instructions

### 1. Environment Configuration

Copy the LDAP configuration from `.env.ldap.example` to your `.env` file and update with your LDAP server details:

```env
# LDAP Connection Settings
LDAP_CONNECTION=default
LDAP_HOST=your-ldap-server.com
LDAP_PORT=636
LDAP_BASE_DN=dc=yourcompany,dc=com
LDAP_USERNAME=cn=service_account,ou=users,dc=yourcompany,dc=com
LDAP_PASSWORD=your_service_account_password
LDAP_TIMEOUT=5
LDAP_SSL=true
LDAP_TLS=false

# LDAP User Attributes
LDAP_USER_ATTRIBUTE=samaccountname
LDAP_EMAIL_ATTRIBUTE=mail
LDAP_NAME_ATTRIBUTE=displayname
LDAP_DEPARTMENT_ATTRIBUTE=department
LDAP_TITLE_ATTRIBUTE=title
```

### 2. Update Role Mapping Configuration

Edit `config/ldap.php` and update the `role_mapping.groups` section with your actual LDAP group DNs:

```php
'role_mapping' => [
    'groups' => [
        'Admin' => [
            'CN=DMS_Admins,OU=Groups,DC=yourcompany,DC=com',
            'CN=IT_Admins,OU=Groups,DC=yourcompany,DC=com',
        ],
        'Manager' => [
            'CN=DMS_Managers,OU=Groups,DC=yourcompany,DC=com',
            'CN=Department_Heads,OU=Groups,DC=yourcompany,DC=com',
        ],
        // ... other roles
    ],
],
```

### 3. Run Database Migration

Execute the migration to add LDAP fields to the users table:

```bash
php artisan migrate
```

### 4. Test LDAP Configuration

1. Access the LDAP admin page: `/admin/ldap`
2. Test the LDAP connection
3. Test user authentication with known LDAP credentials
4. Verify user information retrieval

## How It Works

### Authentication Flow

1. **User Login**: User enters username/email and password
2. **LDAP Authentication**: System attempts LDAP authentication first
3. **User Provisioning**: If LDAP auth succeeds, user is provisioned/synced to local database
4. **Role Assignment**: User roles are assigned based on LDAP group membership
5. **Fallback**: If LDAP fails, system falls back to database authentication

### User Provisioning

- **First Login**: Creates new user record in local database with LDAP attributes
- **Subsequent Logins**: Updates existing user record with current LDAP data
- **Role Sync**: Roles are synchronized on every login based on current LDAP group membership

### Role Mapping

Roles are determined by:
1. **LDAP Group Membership**: Primary method using configured group mappings
2. **Title-based Detection**: HOD/Manager roles based on job titles
3. **Default Role**: "General User" assigned if no specific roles found

## Security Features

- **LDAPS Support**: Encrypted communication with LDAP server
- **Service Account**: Uses dedicated LDAP service account for queries
- **Password Security**: LDAP passwords never stored in local database
- **Audit Logging**: All LDAP operations are logged

## Troubleshooting

### Common Issues

1. **Connection Failed**
   - Verify LDAP host and port
   - Check firewall settings
   - Ensure SSL/TLS configuration is correct

2. **Authentication Failed**
   - Verify user exists in LDAP
   - Check username format (samaccountname vs. email)
   - Verify user credentials

3. **No Roles Assigned**
   - Check LDAP group membership
   - Verify group DN mappings in config
   - Ensure roles exist in database

### Debug Mode

Enable LDAP logging in `.env`:
```env
LDAP_LOGGING=true
LOG_LEVEL=debug
```

Check logs in `storage/logs/laravel.log` for detailed LDAP operation information.

## Additional Features

### Risk Reporting Module (As per new requirements)

The new requirements include a Risk Reporting module with the following features:

1. **Upload Type Selection**: Document or Risk
2. **Risk Issue Types**:
   - Operational Risk
   - Compliance Risk
   - Financial Risk
   - Security Risk
3. **Workflow Management**: Custom approval workflows
4. **HOD Auto-Selection**: Based on LDAP profile

### Next Steps for Full Implementation

1. **Risk Module Forms**: Create forms for each risk type
2. **Workflow Engine**: Implement dynamic workflow routing
3. **Dashboard Updates**: Add upload type dropdown
4. **Document Visibility**: Implement Private/Public/Publish options
5. **Compliance Reporting**: RBI-aligned templates and validation

## Support

For issues or questions regarding LDAP implementation:
1. Check the LDAP admin interface for connection status
2. Review application logs for error details
3. Verify LDAP server configuration with your IT team
4. Test with known working LDAP credentials

## Configuration Examples

### Active Directory Example
```env
LDAP_HOST=ad.company.com
LDAP_PORT=636
LDAP_BASE_DN=dc=company,dc=com
LDAP_USER_ATTRIBUTE=samaccountname
LDAP_SSL=true
```

### OpenLDAP Example
```env
LDAP_HOST=ldap.company.com
LDAP_PORT=636
LDAP_BASE_DN=dc=company,dc=com
LDAP_USER_ATTRIBUTE=uid
LDAP_SSL=true
```

This implementation provides a robust foundation for LDAP authentication while maintaining compatibility with existing DMS functionality.

