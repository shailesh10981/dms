<?php

namespace App\Models;

use LdapRecord\Models\Model;

class LdapGroup extends Model
{
    /**
     * The object classes of the LDAP model.
     */
    public static array $objectClasses = [
        'top',
        'group',
    ];

    /**
     * Get the group's members.
     */
    public function members()
    {
        return $this->hasMany(LdapUser::class, 'member');
    }

    /**
     * Get the group's name.
     */
    public function getName(): string
    {
        return $this->getFirstAttribute('cn') ?: $this->getFirstAttribute('name');
    }
}

