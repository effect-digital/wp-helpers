<?php

namespace Industrious\WpHelpers\Traits;

use RGFormsModel, GFCommon;

trait UsesGravityForms
{
    /**
     * Submit Entry
     *
     * @param  array  $data
     * @return int
     */
    protected function submitEntry(array $data)
    {
        return GFAPI::add_entry($data);
    }

    /**
     * Send Gravity Form Notifications
     *
     * @param  int    $form_id
     * @param  int    $entry_id
     * @return null
     */
    protected function send_gf_notifications(int $form_id, int $entry_id)
    {
        // Get the array info for our forms and entries
        // that we need to send notifications for
        $form = RGFormsModel::get_form_meta($form_id);
        $entry = RGFormsModel::get_lead($entry_id);

        // Loop through all the notifications for the
        // form so we know which ones to send
        $notification_ids = [];

        foreach ($form['notifications'] as $id => $info) {
            array_push($notification_ids, $id);
        }

        // Send the notifications
        GFCommon::send_notifications($notification_ids, $form, $entry);
    }
}
