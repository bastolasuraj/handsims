<?php

namespace App\Controllers;

use App\Core\Controller;

require_once __DIR__ . '/../Helpers/csrf.php';

class ToastSettingsController extends Controller
{
    public function index()
    {
        $this->requireAuth();

        // Only allow local accounts to access this page
        if (!isset($_SESSION['auth_type']) || $_SESSION['auth_type'] !== 'local') {
            $_SESSION['error'] = 'Access denied. This feature is only available for local accounts.';
            $this->redirect('/dashboard');
            return;
        }

        $toastTemplateModel = $this->model('ToastTemplateModel');
        $templates = $toastTemplateModel->getAll();

        $data = [
            'templates' => $templates,
            'csrf_token' => csrf_token()
        ];

        $this->view('settings/toast-templates', $data);
    }

    public function update()
    {
        $this->requireAuth();

        // Only allow local accounts
        if (!isset($_SESSION['auth_type']) || $_SESSION['auth_type'] !== 'local') {
            $_SESSION['error'] = 'Access denied. This feature is only available for local accounts.';
            $this->redirect('/dashboard');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/settings/toast-templates');
            return;
        }

        // CSRF Protection
        if (!isset($_POST['csrf'])) {
            $_SESSION['error'] = 'CSRF token missing from request.';
            $this->redirect('/settings/toast-templates');
            return;
        }

        if (!csrf_verify($_POST['csrf'])) {
            $_SESSION['error'] = 'CSRF token validation failed. Please try again.';
            $this->redirect('/settings/toast-templates');
            return;
        }

        $templateKey = $_POST['template_key'] ?? '';
        $templateMessage = $_POST['template_message'] ?? '';

        if (empty($templateKey) || empty($templateMessage)) {
            $_SESSION['error'] = 'Template key and message are required.';
            $this->redirect('/settings/toast-templates');
            return;
        }

        if (strlen($templateMessage) > 500) {
            $_SESSION['error'] = 'Template message cannot exceed 500 characters.';
            $this->redirect('/settings/toast-templates');
            return;
        }

        $toastTemplateModel = $this->model('ToastTemplateModel');
        $result = $toastTemplateModel->update($templateKey, $templateMessage);

        if ($result) {
            // Format a success message using the toast template system itself
            $formattedKey = ucwords(str_replace('_', ' ', $templateKey));
            $_SESSION['success'] = 'Toast template "' . $formattedKey . '" updated successfully!';

            // Add log
            $this->addLog("Toast template updated", $_SESSION['username'] . " updated toast template: " . $templateKey);
        } else {
            $_SESSION['error'] = 'Failed to update toast template.';
        }

        $this->redirect('/settings/toast-templates');
    }

    public function reset()
    {
        $this->requireAuth();

        // Only allow local accounts
        if (!isset($_SESSION['auth_type']) || $_SESSION['auth_type'] !== 'local') {
            $_SESSION['error'] = 'Access denied. This feature is only available for local accounts.';
            $this->redirect('/dashboard');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/settings/toast-templates');
            return;
        }

        // CSRF Protection
        csrf_check();

        $templateKey = $_POST['template_key'] ?? '';

        if (empty($templateKey)) {
            $_SESSION['error'] = 'Template key is required.';
            $this->redirect('/settings/toast-templates');
            return;
        }

        $toastTemplateModel = $this->model('ToastTemplateModel');
        $result = $toastTemplateModel->resetToDefault($templateKey);

        if ($result) {
            $formattedKey = ucwords(str_replace('_', ' ', $templateKey));
            $_SESSION['success'] = 'Toast template "' . $formattedKey . '" reset to default!';

            // Add log
            $this->addLog("Toast template reset", $_SESSION['username'] . " reset toast template: " . $templateKey);
        } else {
            $_SESSION['error'] = 'Failed to reset toast template.';
        }

        $this->redirect('/settings/toast-templates');
    }

    public function resetAll()
    {
        $this->requireAuth();

        // Only allow local accounts
        if (!isset($_SESSION['auth_type']) || $_SESSION['auth_type'] !== 'local') {
            $_SESSION['error'] = 'Access denied. This feature is only available for local accounts.';
            $this->redirect('/dashboard');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/settings/toast-templates');
            return;
        }

        // CSRF Protection
        csrf_check();

        $toastTemplateModel = $this->model('ToastTemplateModel');
        $result = $toastTemplateModel->resetAllToDefaults();

        if ($result) {
            $_SESSION['success'] = 'All toast templates reset to defaults successfully!';

            // Add log
            $this->addLog("All toast templates reset", $_SESSION['username'] . " reset all toast templates to defaults");
        } else {
            $_SESSION['error'] = 'Failed to reset toast templates.';
        }

        $this->redirect('/settings/toast-templates');
    }
}
