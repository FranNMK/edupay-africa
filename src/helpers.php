<?php

/**
 * Escape a string for safe HTML output.
 *
 * @param mixed $value Value to escape.
 * @return string HTML-safe string.
 */
function h(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Return the current CSRF token, generating one if it does not exist.
 * Requires an active session.
 *
 * @return string
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Render a hidden CSRF input field for use inside HTML forms.
 *
 * @return string HTML hidden input element.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">';
}

/**
 * Verify the submitted CSRF token against the session token.
 * Returns false if the tokens do not match or are missing.
 *
 * @return bool
 */
function csrf_verify(): bool
{
    $submitted = $_POST['csrf_token'] ?? '';
    $expected  = $_SESSION['csrf_token'] ?? '';
    if ($submitted === '' || $expected === '') {
        return false;
    }
    return hash_equals($expected, $submitted);
}
