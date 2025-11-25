<?php
/**
 * Active Directory / LDAP Configuration
 * 
 * LDAP settings are now loaded from .env file
 * This file only contains the ActiveDirectory class
 * 
 * To configure LDAP, edit your .env file:
 * - ENABLE_LDAP=true
 * - LDAP_HOST=your-ad-server.com
 * - LDAP_PORT=your-ldap-port
 * - LDAP_DOMAIN=your-domain.local
 * - LDAP_BASE_DN=DC=your-domain,DC=local
 * - LDAP_GROUP=YourGroupName
 * - LDAP_GROUP_DN=CN=YourGroupName,OU=Groups,DC=your-domain,DC=local
 */

// LDAP constants are now defined in config/env.php from .env file
// No hardcoded values here!