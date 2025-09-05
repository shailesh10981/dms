<?php

namespace App\Models;

use LdapRecord\Models\Model;

class LdapUser extends Model
{
    /**
     * The object classes of the LDAP model.
     */
    public static array $objectClasses = [
        'top',
        'person',
        'organizationalperson',
        'inetorgperson',
    ];

    /**
     * The attributes that should be mutated to dates.
     */
    protected array $dates = [
        'whenchanged',
        'whencreated',
    ];

    /**
     * Get the user's groups.
     */
    public function groups()
    {
        return $this->belongsToMany(LdapGroup::class, 'member');
    }

    /**
     * Get the user's department from LDAP.
     */
    public function getDepartmentAttribute()
    {
        return $this->getFirstAttribute('department');
    }

    /**
     * Get the user's title from LDAP.
     */
    public function getTitleAttribute()
    {
        return $this->getFirstAttribute('title');
    }

    /**
     * Get the user's email from LDAP.
     */
    public function getEmailAttribute()
    {
        return $this->getFirstAttribute('mail');
    }

    /**
     * Get the user's display name from LDAP.
     */
    public function getDisplayNameAttribute()
    {
        return $this->getFirstAttribute('displayname') ?: $this->getFirstAttribute('cn');
    }

    /**
     * Get the user's username from LDAP.
     */
    public function getUsernameAttribute()
    {
        return $this->getFirstAttribute('samaccountname') ?: $this->getFirstAttribute('uid');
    }

    /**
     * Check if user is HOD/Manager based on title or group membership.
     */
    public function isHod(): bool
    {
        $title = strtolower($this->title ?? '');
        $hodTitles = ['manager', 'head', 'director', 'supervisor', 'lead'];
        
        foreach ($hodTitles as $hodTitle) {
            if (str_contains($title, $hodTitle)) {
                return true;
            }
        }

        // Check if user belongs to manager groups
        $managerGroups = config('ldap.role_mapping.manager_groups', []);
        foreach ($this->groups as $group) {
            if (in_array($group->getName(), $managerGroups)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get user roles based on LDAP group membership.
     */
    public function getRoles(): array
    {
        $roles = [];
        $roleMapping = config('ldap.role_mapping.groups', []);

        foreach ($this->groups as $group) {
            $groupName = $group->getName();
            foreach ($roleMapping as $role => $ldapGroups) {
                if (in_array($groupName, $ldapGroups)) {
                    $roles[] = $role;
                }
            }
        }

        // Add Manager role if user is HOD
        if ($this->isHod() && !in_array('Manager', $roles)) {
            $roles[] = 'Manager';
        }

        // Default role if no roles found
        if (empty($roles)) {
            $roles[] = config('ldap.role_mapping.default_role', 'General User');
        }

        return array_unique($roles);
    }
}

