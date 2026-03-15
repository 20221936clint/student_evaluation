<?php
/**
 * Role Check Include File
 * Include this at the top of any page that needs role-based access control
 * It checks if the user has the correct role and shows a modal if not
 * 
 * Usage:
 *   $role_access = require_role('program_head'); // or 'admin', 'instructor'
 *   $show_role_modal = !$role_access['allowed'];
 */

// Get the required role from the function argument
$required_role = $role_required ?? 'program_head';

// Check role access
$role_access = check_role_access($required_role);
$show_role_modal = !$role_access['allowed'];

// Store for use in the page
$GLOBALS['show_role_modal'] = $show_role_modal;
$GLOBALS['role_access'] = $role_access;
?>
