<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

if (isset($renderer) && isset($entry) && is_object($entry)) {

    $boats = $entry->getBoats(); ?>

    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Year</th>
                <th>Owner</th>
                <th>Classification</th>
            </tr>
        </thead>
        <tbody> <?php
            if (isset($boats) && count($boats)) {
                foreach($boats as $boat) { ?>
                    <tr>
                        <td><?= $boat->getBoatName() ?></td>
                        <td><?= $boat->getBoatYear() ?></td>
                        <td><?= $boat->getBoatOwner() ?></td>
                        <td><?= $boat->getBoatClass() ?></td>
                    </tr> <?php
                }
            } else { ?>
                <tr>
                    <td colspan="4">No boats found.</td>
                </tr> <?php
            } ?>
        </tbody>
    </table> <?php
} ?>