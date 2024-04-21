<?php

/*
 * Originally copied as is from https://github.com/parsedown/laravel
 * on 21 Apr 2024 as that repo has switched to read-only and was
 * marked as no longer supported. MIT licensed.
 */


return [
    /**
     * Tells **Parsedown** if it should convert line breaks such as `\n` into `<br />` tags.
     *
     * @see https://github.com/erusev/parsedown/wiki/Usage
     */
    'breaks_enabled' => false,

    /**
     *  Tells the `parsedown()` helper and the `@parsedown` **Blade** directive if the user input should be inline parsed by default.
     *
     * @see https://github.com/erusev/parsedown/wiki/Usage
     */
    'inline' => false,

    /**
     * Tells **Parsedown** if it should escape **HTML** in trusted input. This method isn't safe from XSS!
     *
     * @see https://github.com/erusev/parsedown#escaping-html
     */
    'markup_escaped' => false,

    /**
     * Tells **Parsedown** if it needs to process untrusted user-input.
     *
     * @see https://github.com/erusev/parsedown#security
     */
    'safe_mode' => true,

    /**
     * Tells **Parsedown** if it should automatically convert urls into anchor tags.
     *
     * @see https://github.com/erusev/parsedown/wiki/Usage
     */
    'urls_linked' => true,
];
