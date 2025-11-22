<?php

namespace App\Models;
use Exception;

use App\Core\Model;

class ToastTemplateModel extends Model
{
    /**
     * Get all toast templates
     */
    public function getAll()
    {
        $sql = "SELECT * FROM toast_templates ORDER BY template_key";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get a specific template by key
     */
    public function getByKey($key)
    {
        $sql = "SELECT * FROM toast_templates WHERE template_key = :key";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['key' => $key]);
        return $stmt->fetch();
    }

    /**
     * Update a template message
     */
    public function update($key, $message)
    {
        $sql = "UPDATE toast_templates SET template_message = :message, updated_at = NOW() WHERE template_key = :key";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'key' => $key,
            'message' => $message
        ]);
    }

    /**
     * Reset a template to default
     */
    public function resetToDefault($key)
    {
        $defaults = [
            'stock_add_success' => '%q units of %s %p added to %l warehouse',
            'stock_out_success' => '%q units of %s %p issued to %d from %l',
            'stock_transfer_success' => '%q units of %s %p transferred from %f to %t',
            'product_add_success' => 'Product "%n" created successfully with part number %pn',
            'product_update_success' => 'Product "%n" updated successfully',
            'product_delete_success' => 'Product "%n" deleted successfully',
            'config_update_success' => '%t "%n" updated successfully',
            'config_add_success' => '%t "%n" added successfully',
            'config_delete_success' => '%t "%n" deleted successfully'
        ];

        if (isset($defaults[$key])) {
            return $this->update($key, $defaults[$key]);
        }

        return false;
    }

    /**
     * Reset all templates to defaults
     */
    public function resetAllToDefaults()
    {
        $defaults = [
            'stock_add_success' => '%q units of %s %p added to %l warehouse',
            'stock_out_success' => '%q units of %s %p issued to %d from %l',
            'stock_transfer_success' => '%q units of %s %p transferred from %f to %t',
            'product_add_success' => 'Product "%n" created successfully with part number %pn',
            'product_update_success' => 'Product "%n" updated successfully',
            'product_delete_success' => 'Product "%n" deleted successfully',
            'config_update_success' => '%t "%n" updated successfully',
            'config_add_success' => '%t "%n" added successfully',
            'config_delete_success' => '%t "%n" deleted successfully'
        ];

        $this->db->beginTransaction();

        try {
            foreach ($defaults as $key => $message) {
                $this->update($key, $message);
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Format a message by replacing placeholders with actual values
     *
     * @param string $templateKey The template key to use
     * @param array $data Associative array of placeholder => value pairs
     * @return string The formatted message
     */
    public function formatMessage($templateKey, $data = [])
    {
        $template = $this->getByKey($templateKey);

        if (!$template) {
            return 'Operation completed successfully'; // Fallback message
        }

        $message = $template['template_message'];

        // Replace placeholders with actual values
        foreach ($data as $placeholder => $value) {
            // Handle empty values
            if (empty($value) && $value !== '0' && $value !== 0) {
                $value = '';
            }

            $message = str_replace($placeholder, $value, $message);
        }

        // Clean up any remaining unreplaced placeholders or extra spaces
        $message = preg_replace('/\s+/', ' ', $message); // Multiple spaces to single
        $message = trim($message);

        return $message;
    }
}
