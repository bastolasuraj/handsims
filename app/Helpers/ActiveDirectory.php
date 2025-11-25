<?php

namespace App\Helpers;

class ActiveDirectory
{
    private $connection = null;
    private $bound = false;
    private $enabled = false;
    
    public function __construct()
    {
        // Check if LDAP extension is loaded
        if (!defined('LDAP_ENABLED') || !LDAP_ENABLED) {
            $this->enabled = false;
            error_log('LDAP is not enabled. Check .env settings.');
            return;
        }

        if (!extension_loaded('ldap')) {
            $this->enabled = false;
            error_log('LDAP extension is not loaded. Please enable php_ldap in php.ini');
            return;
        }
        
        $this->enabled = true;
        
        // Try to connect to LDAP server
        $this->connection = @ldap_connect('ldap://' . LDAP_HOST . ':' . LDAP_PORT);
        
        if (!$this->connection) {
            $this->enabled = false;
            error_log('Could not connect to LDAP server at ' . LDAP_HOST);
            return;
        }
        
        // Set LDAP options
        ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->connection, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($this->connection, LDAP_OPT_NETWORK_TIMEOUT, 10);
    }
    
    public function isEnabled()
    {
        return $this->enabled;
    }
    
    public function authenticate($username, $password)
    {
        // If LDAP is not enabled or connected, return false
        if (!$this->enabled || !$this->connection) {
            return false;
        }
        
        if (empty($username) || empty($password)) {
            return false;
        }
        
        // Try to bind with user credentials
        $userDn = $username . '@' . LDAP_DOMAIN;
        
        try {
            error_log("LDAP: Attempting to bind with user: " . $userDn);
            $bind = @ldap_bind($this->connection, $userDn, $password);
            
            if (!$bind) {
                $ldapError = ldap_error($this->connection);
                error_log("LDAP: Bind failed - " . $ldapError);
                return false;
            }
            
            error_log("LDAP: Bind successful for user: " . $username);
            
            $this->bound = true;
            
            // Check if user is member of required group
            if ($this->checkGroupMembership($username)) {
                return $this->getUserInfo($username);
            }
            
            return false;
            
        } catch (\Exception $e) { // Use global Exception
            error_log('LDAP Authentication Error: ' . $e->getMessage());
            return false;
        }
    }
    
    private function checkGroupMembership($username)
    {
        if (!$this->bound) {
            return false;
        }
        
        // Search for user
        $filter = "(sAMAccountName=$username)";
        $attributes = array('memberOf', 'cn', 'mail', 'displayName');
        
        error_log("LDAP: Searching for user with filter: " . $filter . " in " . LDAP_BASE_DN);
        $search = @ldap_search($this->connection, LDAP_BASE_DN, $filter, $attributes);
        
        if (!$search) {
            $ldapError = ldap_error($this->connection);
            error_log("LDAP: Search failed - " . $ldapError);
            return false;
        }
        
        $entries = ldap_get_entries($this->connection, $search);
        
        if ($entries['count'] == 0) {
            return false;
        }
        
        // Check if user is member of required group
        if (isset($entries[0]['memberof'])) {
            $groups = $entries[0]['memberof'];
            error_log("LDAP: User is member of " . $groups['count'] . " groups");
            
            for ($i = 0; $i < $groups['count']; $i++) {
                error_log("LDAP: Checking group: " . $groups[$i]);
                if (stripos($groups[$i], LDAP_GROUP) !== false) {
                    error_log("LDAP: User is member of required group: " . LDAP_GROUP);
                    return true;
                }
            }
            error_log("LDAP: User is NOT member of required group: " . LDAP_GROUP);
        } else {
            error_log("LDAP: No group membership information found for user");
        }
        
        return false;
    }
    
    private function getUserInfo($username)
    {
        if (!$this->bound) {
            return false;
        }
        
        $filter = "(sAMAccountName=$username)";
        $attributes = array('cn', 'mail', 'displayName', 'sAMAccountName', 'department', 'title');
        
        $search = @ldap_search($this->connection, LDAP_BASE_DN, $filter, $attributes);
        
        if (!$search) {
            return false;
        }
        
        $entries = ldap_get_entries($this->connection, $search);
        
        if ($entries['count'] > 0) {
            return array(
                'username' => $entries[0]['samaccountname'][0] ?? $username,
                'full_name' => $entries[0]['displayname'][0] ?? $entries[0]['cn'][0] ?? $username,
                'email' => $entries[0]['mail'][0] ?? '',
                'department' => $entries[0]['department'][0] ?? '',
                'title' => $entries[0]['title'][0] ?? ''
            );
        }
        
        return false;
    }
    
    public function __destruct()
    {
        if ($this->connection && $this->enabled) {
            @ldap_unbind($this->connection);
        }
    }
}