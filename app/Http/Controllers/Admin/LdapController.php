<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LdapAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LdapController extends Controller
{
    protected LdapAuthService $ldapAuthService;

    public function __construct(LdapAuthService $ldapAuthService)
    {
        $this->ldapAuthService = $ldapAuthService;
        $this->middleware(['auth', 'role:Admin']);
    }

    /**
     * Show LDAP configuration and test page.
     */
    public function index()
    {
        $connectionStatus = $this->ldapAuthService->testConnection();
        
        return view('admin.ldap.index', [
            'connectionStatus' => $connectionStatus,
            'config' => config('ldap'),
        ]);
    }

    /**
     * Test LDAP connection.
     */
    public function testConnection()
    {
        try {
            $status = $this->ldapAuthService->testConnection();
            
            if ($status) {
                return response()->json([
                    'success' => true,
                    'message' => 'LDAP connection successful'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'LDAP connection failed'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('LDAP connection test error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'LDAP connection error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Test user authentication.
     */
    public function testAuth(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            $user = $this->ldapAuthService->authenticate(
                $request->input('username'),
                $request->input('password')
            );

            if ($user) {
                return response()->json([
                    'success' => true,
                    'message' => 'Authentication successful',
                    'user' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'roles' => $user->getRoleNames(),
                        'ldap_dn' => $user->ldap_dn,
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication failed'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('LDAP auth test error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Authentication error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get LDAP user information.
     */
    public function getUserInfo(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
        ]);

        try {
            $ldapUser = $this->ldapAuthService->getLdapUser($request->input('username'));

            if ($ldapUser) {
                return response()->json([
                    'success' => true,
                    'user' => [
                        'dn' => $ldapUser->getDn(),
                        'username' => $ldapUser->username,
                        'email' => $ldapUser->email,
                        'display_name' => $ldapUser->display_name,
                        'department' => $ldapUser->department,
                        'title' => $ldapUser->title,
                        'groups' => $ldapUser->groups->map(fn($group) => $group->getName())->toArray(),
                        'roles' => $ldapUser->getRoles(),
                        'is_hod' => $ldapUser->isHod(),
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found in LDAP'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('LDAP user info error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching user info: ' . $e->getMessage()
            ]);
        }
    }
}

