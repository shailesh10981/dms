# LDAP Integration Architecture Design for DMS

## 1. Introduction

This document outlines the proposed architecture and implementation strategy for integrating LDAP (Lightweight Directory Access Protocol) authentication into the existing Document Management System (DMS). The primary goal is to replace the current database-based user login system with LDAP authentication, while ensuring that the existing role and permission management functionalities remain intact and are mapped appropriately from LDAP user attributes or groups.

## 2. Current Authentication System Overview

The current DMS utilizes a standard Laravel authentication system, likely based on Laravel Breeze, which manages user credentials (username/email and password) directly within its MySQL database. User roles and permissions are handled by the `spatie/laravel-permission` package, with roles such as Admin, Manager, Compliance Officer, and General User defined and assigned within the application's database. Authentication is performed by verifying credentials against the `users` table in the database.

## 3. LDAP Integration Strategy

To meet the new requirement of using LDAP for user login, we will integrate an LDAP client library into the Laravel application. The `LdapRecord-Laravel` package has been identified as a suitable choice due to its comprehensive features, active maintenance, and strong community support, as evidenced by various search results [1, 2, 3]. It provides robust capabilities for searching LDAP directories, performing operations, and authenticating LDAP users within Laravel applications.

### 3.1. Choice of LDAP Package: LdapRecord-Laravel

`LdapRecord-Laravel` offers a seamless way to interact with LDAP servers. Its key advantages include:

*   **Active Directory / OpenLDAP Support**: Compatible with various LDAP server types.
*   **Authentication & Synchronization**: Facilitates user authentication against LDAP and synchronization of user data to the local database.
*   **Model-based Interaction**: Allows treating LDAP entries as Eloquent-like models, simplifying data retrieval and manipulation.
*   **Laravel Integration**: Designed specifically for Laravel, providing facades, service providers, and guard implementations.

### 3.2. Authentication Flow

The new authentication flow will be as follows:

1.  **User Login Attempt**: A user attempts to log in via the standard login form, providing their username (or email, depending on LDAP configuration) and password.
2.  **LDAP Authentication**: The application will attempt to authenticate the provided credentials against the configured LDAP server using `LdapRecord-Laravel`.
3.  **Successful LDAP Authentication**: If LDAP authentication is successful, the application will proceed to check for the user's existence in the local database.
4.  **User Provisioning/Synchronization**: 
    *   If the user does not exist in the local database, a new user record will be created (`provisioned`). Essential user details (e.g., username, email, display name) will be synchronized from LDAP attributes to the local `users` table.
    *   If the user already exists, their local record will be updated (`synchronized`) with any relevant changes from LDAP (e.g., updated email, name).
5.  **Role and Permission Assignment**: After successful authentication and local user provisioning/synchronization, the application will determine the user's roles based on their LDAP group memberships or other LDAP attributes. These roles will then be assigned to the user in the local database using `spatie/laravel-permission`.
6.  **Application Login**: The user will then be logged into the Laravel application, and their access will be governed by the roles and permissions assigned from the local database.

### 3.3. User Provisioning and Synchronization

To maintain compatibility with the existing `spatie/laravel-permission` package and other application functionalities that rely on a local `users` table, we will implement a provisioning and synchronization mechanism:

*   **On First Login**: When an LDAP-authenticated user logs in for the first time, a corresponding record will be created in the `users` table. The `password` field in the local database will not store the LDAP password but can be set to a non-usable hash or `NULL` as it's not used for authentication.
*   **Subsequent Logins**: On subsequent successful LDAP logins, the existing user record in the `users` table will be updated with any changes from LDAP (e.g., updated email, name). This ensures that the local user data remains consistent with the LDAP directory.

### 3.4. Role and Permission Mapping

The new requirements state that roles should be fetched from LDAP group settings. This will involve:

1.  **LDAP Group Retrieval**: During the authentication and provisioning process, the application will query the LDAP server for the user's group memberships.
2.  **Mapping Configuration**: A configuration will be established (e.g., in `config/ldap.php` or a dedicated mapping file) to map specific LDAP group Distinguished Names (DNs) or common names (CNs) to the application's defined roles (Admin, Manager, Compliance Officer, General User).
3.  **Role Assignment**: Based on this mapping, the `spatie/laravel-permission` package will be used to assign the corresponding roles to the authenticated user in the local database. If a user belongs to multiple LDAP groups that map to different roles, the application will assign all relevant roles.
4.  **HOD Auto-Selection**: The requirement mentions HOD auto-selection based on the user's LDAP profile. This implies that an LDAP attribute (e.g., `department`, `title`) will be used to identify if a user is an HOD/Manager and assign the 'Manager' role accordingly. This mapping will also be configurable.

### 3.5. Configuration

The following LDAP configuration parameters will be required, typically stored in the `.env` file and accessed via `config/ldap.php`:

*   `LDAP_HOST`: LDAP server URL (e.g., `ldap.example.com`)
*   `LDAP_PORT`: Port for LDAP (e.g., `389` for LDAP, `636` for LDAPS)
*   `LDAP_BASE_DN`: Base Distinguished Name for searches (e.g., `dc=example,dc=com`)
*   `LDAP_BIND_DN`: DN of a user with search permissions (e.g., `cn=admin,dc=example,dc=com`)
*   `LDAP_BIND_PASSWORD`: Password for the bind user
*   `LDAP_USE_SSL`: Boolean, `true` for LDAPS (recommended)
*   `LDAP_VERSION`: LDAP protocol version (e.g., `3`)
*   `LDAP_USER_ATTRIBUTE`: LDAP attribute used for username (e.g., `samaccountname`, `uid`, `mail`)
*   `LDAP_GROUP_ATTRIBUTE`: LDAP attribute for group membership (e.g., `memberof`, `member`)
*   `LDAP_GROUP_MAPPING`: Array mapping LDAP group DNs/CNs to application roles.
*   `LDAP_HOD_ATTRIBUTE`: LDAP attribute to identify HODs/Managers.

## 4. Database Schema Changes

Minimal changes are expected for the existing `users` table. The `password` column will no longer be used for authentication but should be retained to satisfy existing framework requirements. It can be made nullable or filled with a placeholder value. No new tables are anticipated for LDAP integration itself, as roles and permissions will continue to be managed by `spatie/laravel-permission`'s tables.

## 5. Implementation Steps (High-Level)

1.  **Install LdapRecord-Laravel**: Add the package to the project via Composer.
2.  **Publish Configuration**: Publish the package's configuration file (`config/ldap.php`).
3.  **Configure LDAP Connection**: Set up LDAP connection details in `.env` and `config/ldap.php`.
4.  **Create LDAP User Model**: Define an LdapRecord model for users that maps to LDAP attributes.
5.  **Modify Authentication Guard**: Update `config/auth.php` to use the LdapRecord authentication guard.
6.  **Implement User Provisioning**: Create an event listener or modify the authentication process to provision/synchronize users to the local database on successful LDAP login.
7.  **Implement Role Mapping**: Develop logic to read LDAP group memberships and assign corresponding `spatie/laravel-permission` roles to the local user.
8.  **Update Login Controller**: Adjust `AuthenticatedSessionController` to use the new LDAP authentication flow.
9.  **Testing**: Thoroughly test LDAP authentication, user provisioning, and role assignment.

## 6. Security Considerations

*   **LDAPS**: Always use LDAPS (LDAP over SSL/TLS) to encrypt communication between the application and the LDAP server (`LDAP_USE_SSL=true`).
*   **Bind User**: Use a dedicated LDAP bind user with minimal necessary permissions (read-only access to user and group attributes).
*   **Password Storage**: Never store LDAP passwords in the local database. The local `password` field will be ignored for LDAP-authenticated users.
*   **Input Validation**: Ensure all user inputs are properly validated and sanitized to prevent injection attacks.

## 7. References

[1] LdapRecord Documentation: [https://ldaprecord.com/docs/laravel/v2/](https://ldaprecord.com/docs/laravel/v2/)
[2] Laravel News - LdapRecord: [https://laravel-news.com/ldaprecord-php-ldap-framework](https://laravel-news.com/ldaprecord-php-ldap-framework)
[3] Adldap2/Adldap2-Laravel GitHub: [https://github.com/Adldap2/Adldap2-Laravel](https://github.com/Adldap2/Adldap2-Laravel)


