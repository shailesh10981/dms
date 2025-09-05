<?php

namespace App\Services;

use App\Models\User;
use App\Models\LdapUser;
use LdapRecord\Connection;
use LdapRecord\Container;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class LdapAuthService
{
    protected Connection $connection;

    public function __construct()
    {
        $this->connection = Container::getDefaultConnection();
    }

    /**
     * Authenticate user against LDAP and provision/sync to local database.
     */
    public function authenticate(string $username, string $password): ?User
    {
        try {
            // Attempt LDAP authentication
            $ldapUser = $this->authenticateLdap($username, $password);
            
            if (!$ldapUser) {
                Log::info('LDAP authentication failed for user: ' . $username);
                return null;
            }

            Log::info('LDAP authentication successful for user: ' . $username);

            // Provision or sync user to local database
            $localUser = $this->provisionUser($ldapUser);

            // Assign roles based on LDAP groups
            $this->assignRoles($localUser, $ldapUser);

            return $localUser;

        } catch (\Exception $e) {
            Log::error('LDAP authentication error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Authenticate user against LDAP server.
     */
    protected function authenticateLdap(string $username, string $password): ?LdapUser
    {
        try {
            // Build user DN for authentication
            $userAttribute = config('ldap.user_attributes.username');
            $baseDn = config('ldap.connections.default.base_dn');
            
            // Search for user first
            $ldapUser = LdapUser::where($userAttribute, '=', $username)->first();
            
            if (!$ldapUser) {
                Log::info('LDAP user not found: ' . $username);
                return null;
            }

            // Attempt to bind with user credentials
            $userDn = $ldapUser->getDn();
            
            if ($this->connection->auth()->attempt($userDn, $password)) {
                Log::info('LDAP bind successful for: ' . $userDn);
                return $ldapUser;
            }

            Log::info('LDAP bind failed for: ' . $userDn);
            return null;

        } catch (\Exception $e) {
            Log::error('LDAP authentication error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Provision or sync LDAP user to local database.
     */
    protected function provisionUser(LdapUser $ldapUser): User
    {
        $username = $ldapUser->username;
        $email = $ldapUser->email;
        $name = $ldapUser->display_name ?: $username;

        // Find existing user or create new one
        $localUser = User::where('email', $email)
            ->orWhere('name', $username)
            ->first();

        if ($localUser) {
            // Update existing user
            $localUser->update([
                'name' => $name,
                'email' => $email,
                'ldap_dn' => $ldapUser->getDn(),
                'ldap_username' => $username,
                'department' => $ldapUser->department,
                'title' => $ldapUser->title,
            ]);
            Log::info('Updated existing user: ' . $email);
        } else {
            // Create new user
            $localUser = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(uniqid()), // Random password, not used
                'ldap_dn' => $ldapUser->getDn(),
                'ldap_username' => $username,
                'department' => $ldapUser->department,
                'title' => $ldapUser->title,
                'email_verified_at' => now(), // LDAP users are considered verified
            ]);
            Log::info('Created new user: ' . $email);
        }

        return $localUser;
    }

    /**
     * Assign roles to user based on LDAP group membership.
     */
    protected function assignRoles(User $localUser, LdapUser $ldapUser): void
    {
        try {
            // Get roles from LDAP user
            $ldapRoles = $ldapUser->getRoles();
            
            Log::info('LDAP roles for user ' . $localUser->email . ': ' . implode(', ', $ldapRoles));

            // Remove all existing roles
            $localUser->syncRoles([]);

            // Assign new roles
            foreach ($ldapRoles as $roleName) {
                $role = Role::where('name', $roleName)->first();
                if ($role) {
                    $localUser->assignRole($role);
                    Log::info('Assigned role "' . $roleName . '" to user: ' . $localUser->email);
                } else {
                    Log::warning('Role "' . $roleName . '" not found in database');
                }
            }

        } catch (\Exception $e) {
            Log::error('Error assigning roles: ' . $e->getMessage());
        }
    }

    /**
     * Test LDAP connection.
     */
    public function testConnection(): bool
    {
        try {
            return $this->connection->isConnected() || $this->connection->connect();
        } catch (\Exception $e) {
            Log::error('LDAP connection test failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get LDAP user by username.
     */
    public function getLdapUser(string $username): ?LdapUser
    {
        try {
            $userAttribute = config('ldap.user_attributes.username');
            return LdapUser::where($userAttribute, '=', $username)->first();
        } catch (\Exception $e) {
            Log::error('Error fetching LDAP user: ' . $e->getMessage());
            return null;
        }
    }
}

