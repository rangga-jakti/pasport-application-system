<?php

/**
 * View Helper Functions
 * Used for presentation layer only (UI formatting)
 */

/**
 * Convert Indonesian DB values to English display values
 * Database stays untouched.
 */
function yesNo(string $value): string
{
    return $value === 'Ada' ? 'Yes' : 'No';
}

/**
 * Convert registration status to badge HTML
 */
function statusBadge(?string $status): string
{
    if ($status === 'OK') {
        return '<span class="badge bg-success">Approved</span>';
    }

    if ($status === 'Tidak') {
        return '<span class="badge bg-danger">Rejected</span>';
    }

    return '<span class="badge bg-warning text-dark">Not Reviewed</span>';
}
